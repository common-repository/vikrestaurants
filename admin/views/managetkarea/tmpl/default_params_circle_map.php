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

<div id="circle-googlemap" style="width: 100%; height: 600px;" class="<?php echo empty($content->center->latitude) || empty($content->center->longitude) ? 'disabled-map' : ''; ?>">
	<?php if (empty($content->center->latitude) || empty($content->center->longitude)): ?>
		<a href="javascript:void(0)" id="circle-get-coords" class="btn map-get-coords">
			<i class="fas fa-location-arrow"></i>
		</a>
	<?php endif; ?>
</div>

<div class="circle-google-auth-error" style="display: none;">
	<?php echo VREApplication::getInstance()->alert(JText::translate('VRE_GOOGLE_API_KEY_ERROR')); ?>
</div>

<?php
JText::script('VRTKAREAUSERPOSITION');
?>

<script>
	(function($, w) {
		'use strict';

		// holds the circle center and radius
		let CIRCLE = {
			lat:    null,
			lng:    null,
			radius: 0,
		};

		<?php if ($this->area->type == 'circle' && isset($content->center)): ?>
			CIRCLE.lat    = <?php echo (float) $content->center->latitude ?? 0; ?>;
			CIRCLE.lng    = <?php echo (float) $content->center->longitude ?? 0; ?>;
			CIRCLE.radius = <?php echo (float) $content->radius ?? 1; ?>;
		<?php endif; ?>

		// reference to the Google Map instance
		let CIRCLE_MAP = null;
		// reference to the Google Map marker instance
		let CIRCLE_MARKER = null;
		// reference to the Google Map shape instance
		let CIRCLE_SHAPE = null;

		const circleCoordHandler = (lat, lng, confirmed) => {
			let r = true;

			if (CIRCLE.lat !== null && !isNaN(CIRCLE.lat)) {
				// skip confirmation in case the 3rd argument is specified
				r = confirmed || confirm(Joomla.JText._('VRTKAREAUSERPOSITION'));
			}
			
			if (r) {
				$('input[name="content[circle][center][latitude]"]').val(lat);
				$('input[name="content[circle][center][longitude]"]').val(lng).trigger('change');
			}
		}

		const changeCircleContents = (lat, lng, radius) => {
			lat = parseFloat(lat);
			lng = parseFloat(lng);

			if (isNaN(lat) || isNaN(lng)) {
				// do not go ahead
				return false;
			}

			let center_map = false;

			if (CIRCLE && (Math.abs(CIRCLE.lat - lat) >= 0.3 || Math.abs(CIRCLE.lng - lng) >= 0.3)) {
				// center map in case the difference between the current coordinates and
				// the previous ones is equals or greater than 0.3
				center_map = true;
			}

			CIRCLE.lat = lat;
			CIRCLE.lng = lng;

			if (radius !== undefined) {
				CIRCLE.radius = parseFloat(radius);
			}

			if (CIRCLE.lat.length == 0 || CIRCLE.lng.length == 0) {
				return;
			}

			if (CIRCLE_SHAPE) {
				const coord = new google.maps.LatLng(CIRCLE.lat, CIRCLE.lng);

				// update marker position
				CIRCLE_MARKER.setPosition(coord);

				// update circle
				CIRCLE_SHAPE.setCenter(coord);
				CIRCLE_SHAPE.setRadius(CIRCLE.radius * 1000);

				if (center_map) {
					CIRCLE_MAP.setCenter(coord);
				}
			} else {
				// initialize the map for the first time
				initializeCircleMap();
			}
		}

		const setCircleShapeOptions = () => {
			const options = {};

			// fetch fill color
			options.fillColor = $('input[name="attributes[circle][color]"]').val();

			if (options.fillColor) {
				options.fillColor = '#' + options.fillColor.replace(/^#/);
			} else {
				options.fillColor = '#FF0000';
			}

			// fetch border color
			options.strokeColor = $('input[name="attributes[circle][strokecolor]"]').val();

			if (options.strokeColor) {
				options.strokeColor = '#' + options.strokeColor.replace(/^#/);
			} else {
				options.strokeColor = fillColor;
			}

			// fetch border width
			options.strokeWeight = parseInt($('input[name="attributes[circle][strokeweight]"]').val());

			if (isNaN(options.strokeWeight) || options.strokeWeight < 0) {
				options.strokeWeight = 2;
			}

			// trigger event to allow third party plugins to extend the shape options
			let event = $.Event('deliveryarea.circle.attributes.draw');
			event.options = options;
			$(w).trigger(event);

			CIRCLE_SHAPE.setOptions(options);
		}

		const initializeCircleMap = () => {
			if (CIRCLE.lat === null || CIRCLE.lng === null ) {
				return false;
			}

			$('#circle-get-coords').remove();
			$('#circle-googlemap').removeClass('map-disabled');

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
			
			const coord = new google.maps.LatLng(CIRCLE.lat, CIRCLE.lng);

			prop.center = coord;
			
			CIRCLE_MAP = new google.maps.Map(document.getElementById('circle-googlemap'), prop);
			
			CIRCLE_MARKER = new google.maps.Marker({
				position: coord,
				draggable: true,
			});
				
			CIRCLE_MARKER.setMap(CIRCLE_MAP);

			// fill all areas
			fillCircleMapShapes($('input[name="attributes[circle][display_shapes]"]').is(':checked'));

			CIRCLE_SHAPE = new google.maps.Circle({
				// strokeColor: strokeColor,
				strokeOpacity: 0.8,
				// strokeWeight: strokeWeight,
				// fillColor: fillColor,
				fillOpacity: 0.35,
				map: CIRCLE_MAP,
				center: coord,
				radius: CIRCLE.radius * 1000,
				clickable: false,
			});

			// construct shape options here for a better modularity
			setCircleShapeOptions();

			CIRCLE_MAP.addListener('click', (e) => {
				circleCoordHandler(e.latLng.lat(), e.latLng.lng());
			});

			// update circle position after dragging the marker
			CIRCLE_MARKER.addListener('dragend', (e) => {
				const coord = CIRCLE_MARKER.getPosition();
				circleCoordHandler(coord.lat(), coord.lng(), true);
			});
		}

		w.fillCircleMapShapes = (status) => {
			fillMapShapes(status, CIRCLE_MAP);
		}

		$(function() {
			$('.circle-map-repaint').on('change', () => {
				changeCircleContents(
					$('input[name="content[circle][center][latitude]"]').val(),
					$('input[name="content[circle][center][longitude]"]').val(),
					$('input[name="content[circle][radius]"]').val()
				);
			});

			$('.circle-shape-repaint').on('change', () => {
				if (!CIRCLE_SHAPE) {
					return false;
				}

				// update circle style
				setCircleShapeOptions();
			});

			$('#circle-get-coords').on('click', () => {
				// retrieve user coordinates
				VikGeo.getCurrentPosition().then((coord) => {
					// coordinates retrieved, change circle center
					circleCoordHandler(coord.lat, coord.lng);
				}).catch((error) => {
					// unable to obtain current position, show error
					alert(error);
				});
			});

			// display error in case Google fails the authentication
			$(w).on('google.autherror', () => {
				// hide map (forced)
				$('#circle-googlemap')
					.css('display', 'none')
					.css('width', '0px')
					.css('height', '0px');

				// display alert
				$('.circle-google-auth-error')
					.show()
					.css('cursor', 'pointer')	
					.on('click', (event) => {
						// go to configuration page and focus the API Key setting
						w.parent.location.href = '<?php echo VREFactory::getPlatform()->getUri()->admin('index.php?option=com_vikrestaurants&view=editconfig#googleapikey', false); ?>';
					});
			});

			// set up circle shape within the map
			$(w).on('deliveryarea.shapes.setup.circle', (event) => {
				if (typeof google === 'undefined') {
					return false;
				}

				MAP_SHAPES.push( 
					new google.maps.Circle({
						strokeColor: '#' + event.shape.attributes.strokecolor.replace(/^#/, ''),
						strokeOpacity: 0.5,
						strokeWeight: event.shape.attributes.strokeweight,
						fillColor: '#' + event.shape.attributes.color.replace(/^#/, ''),
						fillOpacity: 0.20,
						center: new google.maps.LatLng(event.shape.content.center.latitude, event.shape.content.center.longitude),
						radius: event.shape.content.radius * 1000,
						clickable: false,
					})
				);
			});

			<?php if ($this->area->type == 'circle') { ?>
				initializeCircleMap();
			<?php } ?>
		});
	})(jQuery, window);
</script>