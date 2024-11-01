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

JHtml::fetch('vrehtml.assets.googlemaps', null, 'places');
JHtml::fetch('vrehtml.assets.toast', 'top-left');

$vik = VREApplication::getInstance();

?>

<div class="control-group"><div id="googlemap" class="gm-fixed"></div></div>

<div class="vr-map-address-box">

	<input type="text" name="address" value="" id="vraddress" autocomplete="off" size="64" placeholder="<?php echo JText::translate('VRTKMAPTESTADDRESS'); ?>" />

	<div class="vr-map-address-box-response" style="display: none;"></div>

</div>

<?php
JText::script('VRSYSTEMCONNECTIONERR');
JText::script('VRE_GOOGLE_API_KEY_ERROR');
?>

<script>
	(function($, w) {
		'use strict';

		let MAP_SHAPES = [];

		// a reference to Google Map
		let map = null;

		// a reference to the user position marker
		let marker = null;

		const initializeMap = () => {
			if (typeof google === 'undefined') {
				// API Key not configured
				$(w).trigger('google.autherror');
				return false;
			}

			// create marker
			marker = new google.maps.Marker();

			map = new google.maps.Map(document.getElementById('googlemap'), {
				zoom: 12,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
			});

			// get bounds handler
			const markerBounds = new google.maps.LatLngBounds();

			// obtain all the existing delivery areas
			const shapes = <?php echo json_encode($this->shapes); ?>;

			// collects all the points of the map
			let coordinates = [];

			shapes.forEach((shape) => {
				// trigger event to allow the correct handler to properly set up the shape within the map
				let event = $.Event('deliveryarea.shapes.setup.' + shape.type);
				event.shape = shape;
				$(w).trigger(event);

				// extend coordinates
				coordinates = coordinates.concat(event.coordinates || []);
			});

			MAP_SHAPES.forEach((shape) => {
				shape.setMap(map);
			});

			// extend map bounds
			coordinates.forEach((coord) => {
				markerBounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
			});

			if (coordinates.length > 1) {
				map.fitBounds(markerBounds);
				map.setCenter(markerBounds.getCenter());
			} else if (coordinates.length == 1) {
				map.setCenter(markerBounds.getCenter());
				map.setZoom(14);
			} else {
				// recover user current position
				VikGeo.getCurrentPosition().then((data) => {
					// set map center
					map.setCenter(new google.maps.LatLng(data.lat, data.lng));
					map.setZoom(14);
				});
			}
		}

		const evaluateCoordinatesFromAddress = (address) => {
			if (marker) {
				marker.setMap(null);
			}

			if (address.length == 0 || typeof google === 'undefined') {
				return;
			}

			const geocoder = new google.maps.Geocoder();

			geocoder.geocode({'address': address}, (results, status) => {
				if (status == 'OK') {
					// extract components from place
					const data = VikGeo.extractDataFromPlace(results[0]);

					// get delivery information
					getLocationDeliveryInfo(data);
				}
			});
		}

		const getLocationDeliveryInfo = (query) => {
			marker.setPosition(new google.maps.LatLng(query.lat, query.lng));

			marker.setAnimation(google.maps.Animation.DROP);
			marker.setMap(map);

			map.setCenter(marker.position);

			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=tkarea.getinfoajax&tmpl=component'); ?>',
				{
					query: query,
				},
				(resp) => {
					$('.vr-map-address-box-response').html(resp.html);
					$('.vr-map-address-box-response').slideDown();
				},
				(error) => {
					alert(Joomla.JText._('VRSYSTEMCONNECTIONERR'));
				}
			);
		}

		$(function() {
			// load map with 256 milliseconds of delay in order to allow Google
			// to read the correct size of the screen
			setTimeout(initializeMap, 256);

			$('#vraddress').on('change', function() {
				// this function won't be triggered when selecting an address
				// through the Google auto-complete feature
				evaluateCoordinatesFromAddress($(this).val());
			});

			<?php
			if (VikRestaurants::isGoogleMapsApiEnabled('places'))
			{
				// include JavaScript code to support the addresses autocompletion
				// only in case the Places API is enabled in the configuration
				?>
				onInstanceReady(() => {
					if (VikGMapsUtils.hasError()) {
						throw 'invalid';
					}

					// wait until Google Maps has been loaded
					return VikGMapsUtils.isReady;
				}).then(() => {
					// set up address autocomplete
					VikGMapsUtils.setupAutocomplete('#vraddress', (data) => {
						if (data) {
							getLocationDeliveryInfo(data);
						}
					});
				}).catch(() => {
					// unable to load Google
				});
				<?php
			}
			?>

			const response = $('.vr-map-address-box-response');

			$('#vraddress').on('input propertychange paste', () => {
				if (response.is(':visible')) {
					response.slideUp();
				}
			});

			// paint circle on map
			$(w).on('deliveryarea.shapes.setup.circle', (event) => {
				event.coordinates = [{
					lat: parseFloat(event.shape.content.center.latitude),
					lng: parseFloat(event.shape.content.center.longitude),
				}];

				MAP_SHAPES.push( 
					new google.maps.Circle({
						strokeColor: '#' + event.shape.attributes.strokecolor.replace(/^#/, ''),
						strokeOpacity: 0.5,
						strokeWeight: event.shape.attributes.strokeweight,
						fillColor: '#' + event.shape.attributes.color.replace(/^#/, ''),
						fillOpacity: 0.20,
						center: new google.maps.LatLng(event.coordinates[0].lat, event.coordinates[0].lng),
						radius: event.shape.content.radius * 1000,
						clickable: false,
					})
				);
			});

			// paint polygon on map
			$(w).on('deliveryarea.shapes.setup.polygon', (event) => {
				event.coordinates = [];

				event.shape.content.forEach((coord) => {
					event.coordinates.push({
						lat: parseFloat(coord.latitude),
						lng: parseFloat(coord.longitude),
					});
				});

				MAP_SHAPES.push(
					new google.maps.Polygon({
						paths: event.coordinates,
						strokeColor: '#' + event.shape.attributes.strokecolor.replace(/^#/, ''),
						strokeOpacity: 0.5,
						strokeWeight: event.shape.attributes.strokeweight,
						fillColor: '#' + event.shape.attributes.color.replace(/^#/, ''),
						fillOpacity: 0.20,
						clickable: false,
					})
				);
			});

			// display error in case Google fails the authentication
			$(w).on('google.autherror', () => {
				// display alert
				VREToast.dispatch({
					text:   Joomla.JText._('VRE_GOOGLE_API_KEY_ERROR'),
					status: 2,
					delay:  20000,
					style: {
						// do not use BOLD to make text more readable
						'font-weight': 'normal',
					},
					action: (event) => {
						// go to configuration page and focus the API Key setting
						w.parent.location.href = '<?php echo VREFactory::getPlatform()->getUri()->admin('index.php?option=com_vikrestaurants&view=editconfig#googleapikey', false); ?>';
					},
				});
			});
		});
	})(jQuery, window);	
</script>