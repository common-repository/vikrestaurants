<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

$content = $this->area->content;

?>

<div id="polygon-googlemap" style="width: 100%; height: 600px;" class="<?php echo !$content || $this->area->type !== 'polygon' ? 'disabled-map' : ''; ?>">
	<?php if (!$content || $this->area->type !== 'polygon'): ?>
		<a href="javascript:void(0)" id="polygon-get-coords" class="btn map-get-coords">
			<i class="fas fa-location-arrow"></i>
		</a>
	<?php endif; ?>
</div>

<div class="polygon-google-auth-error" style="display: none;">
	<?php echo VREApplication::getInstance()->alert(JText::translate('VRE_GOOGLE_API_KEY_ERROR')); ?>
</div>

<?php
JText::script('VRTKAREAUSERPOSITION');
?>

<script>
	(function($, w) {
		'use strict';

		// reference to the Google Map instance
		let POLYGON_MAP = null;
		// a list of references to the Google Map markers
		let POLYGON_MARKERS = {};
		// reference to the Google Map shape instance
		let POLYGON_SHAPE = null;

		const polygonCoordHandler = (index, lat, lng, confirmed) => {
			const block = $('.polygon-point[data-id="' + index + '"]');

			let r = true;

			if (block.find('input[name="content[polygon][latitude][]').val().length) {
				// skip confirmation in case the 3rd argument is specified
				r = confirmed || confirm(Joomla.JText._('VRTKAREAUSERPOSITION'));
			}
			
			if (r) {
				updatePolygonPoint(index, {
					latitude:  lat,
					longitude: lng,
				});
			}
		}

		w.updatePolygonMarker = (index, lat, lng) => {
			if (typeof google === 'undefined') {
				// API Key not configured
				$(w).trigger('google.autherror');
				return false;
			}
			
			const coord = new google.maps.LatLng(lat, lng);

			if (POLYGON_MARKERS.hasOwnProperty(index)) {
				POLYGON_MARKERS[index].setPosition(coord);
			} else {
				// origins, anchor positions and coordinates of the marker increase in the X
				// direction to the right and in the Y direction down
				const image = {
				  	url: '<?php echo VREASSETS_ADMIN_URI; ?>images/pin-circle.png',
				  	// this marker is 16x16 pixel
				  	size: new google.maps.Size(16, 16),
				  	// the origin for this image is (0, 0)
				  	origin: new google.maps.Point(0, 0),
				  	// the anchor for this image is the center of the pin
				  	anchor: new google.maps.Point(8, 8)
				};

				const marker = new google.maps.Marker({
					position: coord,
					draggable: true,
					map: POLYGON_MAP,
					icon: image,
				});

				// update vertex position after dragging the marker
				marker.addListener('dragend', (e) => {
			        const coord = marker.getPosition();
			        polygonCoordHandler(index, coord.lat(), coord.lng(), true);
			    });

				// open the inspector after clicking the marker
			    marker.addListener('click', (e) => {
			    	selectPolygonPoint(index);
			    });

				POLYGON_MARKERS[index] = marker;
			}

			refreshPolygonShape();
		}

		w.removePolygonMarker = (id) => {
			if (POLYGON_MARKERS.hasOwnProperty(id)) {
				POLYGON_MARKERS[id].setMap(null);
				delete POLYGON_MARKERS[id];
			}

			refreshPolygonShape();
		}

		w.refreshPolygonShape = () => {
			if (POLYGON_SHAPE) {
				POLYGON_SHAPE.setPaths(getPolygonPoints());
			} else {
				initializePolygonMap();
			}
		}

		const setPolygonShapeOptions = () => {
			const options = {};

			// fetch fill color
			options.fillColor = $('input[name="attributes[polygon][color]"]').val();

			if (options.fillColor) {
				options.fillColor = '#' + options.fillColor.replace(/^#/);
			} else {
				options.fillColor = '#FF0000';
			}

			// fetch border color
			options.strokeColor = $('input[name="attributes[polygon][strokecolor]"]').val();

			if (options.strokeColor) {
				options.strokeColor = '#' + options.strokeColor.replace(/^#/);
			} else {
				options.strokeColor = fillColor;
			}

			// fetch border width
			options.strokeWeight = parseInt($('input[name="attributes[polygon][strokeweight]"]').val());

			if (isNaN(options.strokeWeight) || options.strokeWeight < 0) {
				options.strokeWeight = 2;
			}

			// trigger event to allow third party plugins to extend the shape options
			let event = $.Event('deliveryarea.polygon.attributes.draw');
			event.options = options;
			$(w).trigger(event);

			POLYGON_SHAPE.setOptions(options);
		}

		const initializePolygonMap = (coordinates) => {
			$('#polygon-get-coords').remove();
			$('#polygon-googlemap').removeClass('map-disabled');

			if (typeof google === 'undefined') {
				// API Key not configured
				$(w).trigger('google.autherror');
				return false;
			}

			const prop = {
				zoom: 13,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				clickableIcons: false,
			};
			
			POLYGON_MAP = new google.maps.Map(document.getElementById('polygon-googlemap'), prop);

			// get bounds handler
			let markerBounds = new google.maps.LatLngBounds();

			if (typeof coordinates === 'undefined') {
				// define the LatLng coordinates for the polygon's path
				coordinates = getPolygonPoints();
			}

			coordinates.forEach((coord) => {
				coord.lat = parseFloat(typeof coord.lat !== 'undefined' ? coord.lat : coord.latitude);
				coord.lng = parseFloat(typeof coord.lng !== 'undefined' ? coord.lng : coord.longitude);

				markerBounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
			});

			// fill all areas
			fillPolygonMapShapes($('input[name="attributes[polygon][display_shapes]"]').is(':checked'));

			// construct the polygon
			POLYGON_SHAPE = new google.maps.Polygon({
				paths: coordinates,
				// strokeColor: strokeColor,
				strokeOpacity: 0.8,
				// strokeWeight: strokeWeight,
				// fillColor: fillColor,
				fillOpacity: 0.35,
				clickable: false,
				map: POLYGON_MAP,
			});

			// construct shape options here for a better modularity
			setPolygonShapeOptions();

			if (coordinates.length > 1) {
				POLYGON_MAP.fitBounds(markerBounds);
			}

			POLYGON_MAP.setCenter(markerBounds.getCenter());

			for (let k in POLYGON_MARKERS) {
				if (POLYGON_MARKERS.hasOwnProperty(k)) {
					POLYGON_MARKERS[k].setMap(POLYGON_MAP);
				}
			} 

			POLYGON_MAP.addListener('click', (e) => {
				const block = $('.polygon-point').last();

				const latInput = block.find('input[name="content[polygon][latitude][]"]');
				const lngInput = block.find('input[name="content[polygon][longitude][]"]');

				if (!latInput.length || !lngInput.length || (latInput.val().length && lngInput.val().length)) {
					// add new point in case the list is empty or in case
					// the last added point has been already filled in
					addPolygonPoint({
						latitude:  e.latLng.lat(),
						longitude: e.latLng.lng(),
					});
				} else {
					// otherwise update coordinates of last element
					polygonCoordHandler(block.attr('data-id'), e.latLng.lat(), e.latLng.lng());
				}
			});
		}

		w.fillPolygonMapShapes = (status) => {
			fillMapShapes(status, POLYGON_MAP);
		}

		$(function() {
			$('.polygon-shape-repaint').on('change', () => {
				if (!POLYGON_SHAPE) {
					return false;
				}

				// update polygon style
				setPolygonShapeOptions();
			});

			$('#polygon-get-coords').on('click', () => {
				// retrieve user coordinates
				VikGeo.getCurrentPosition().then((coord) => {
					// create a new polygon point
					addPolygonPoint({
						latitude:  coord.lat,
						longitude: coord.lng,
					});
				}).catch((error) => {
					// unable to obtain current position, show error
					alert(error);
				});
			});

			// display error in case Google fails the authentication
			$(w).on('google.autherror', () => {
				// hide map (forced)
				$('#polygon-googlemap')
					.css('display', 'none')
					.css('width', '0px')
					.css('height', '0px');

				// display alert
				$('.polygon-google-auth-error')
					.show()
					.css('cursor', 'pointer')	
					.on('click', (event) => {
						// go to configuration page and focus the API Key setting
						w.parent.location.href = '<?php echo VREFactory::getPlatform()->getUri()->admin('index.php?option=com_vikrestaurants&view=editconfig#googleapikey', false); ?>';
					});
			});

			// set up polygon shape within the map
			$(w).on('deliveryarea.shapes.setup.polygon', (event) => {
				if (typeof google === 'undefined') {
					return false;
				}

				let coords = [];

				event.shape.content.forEach((coord) => {
					coords.push({
						lat: parseFloat(coord.latitude),
						lng: parseFloat(coord.longitude),
					});
				});

				MAP_SHAPES.push(
					new google.maps.Polygon({
						paths: coords,
						strokeColor: '#' + event.shape.attributes.strokecolor.replace(/^#/, ''),
						strokeOpacity: 0.5,
						strokeWeight: event.shape.attributes.strokeweight,
						fillColor: '#' + event.shape.attributes.color.replace(/^#/, ''),
						fillOpacity: 0.20,
						clickable: false,
					})
				);
			});

			<?php if ($this->area->type == 'polygon') { ?>
				initializePolygonMap(<?php echo json_encode($content); ?>);
			<?php } ?>
		});
	})(jQuery, window);
</script>