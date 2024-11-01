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

$origin = $this->origin;

$vik = VREApplication::getInstance();

if ($origin->latitude === null || $origin->longitude === null)
{
	// display notice
	echo $vik->alert(JText::translate('VRE_ORIGIN_COORD_INFO'), 'info', false, ['id' => 'origin-map-warning']);
}

?>

<div id="origin-googlemap" style="width: 100%; height: 400px;<?php echo ($origin->longitude === null ? 'display:none;' : ''); ?>"></div>

<script>
	(function($, w) {
		'use strict';

		let map, marker, infoWindow;

		<?php
		if ($origin->latitude !== null && $origin->longitude !== null)
		{
			?>
			let originLat = <?php echo floatval($origin->latitude); ?>;
			let originLng = <?php echo floatval($origin->longitude); ?>;
			<?php
		}
		else
		{
			?>
			let originLat = '';
			let originLng = '';
			<?php
		}
		?>
		
		w.changeOriginLatLng = (lat, lng) => {
			originLat = lat;
			originLng = lng;

			if (originLat.length == 0 || originLng.length == 0) {
				originLat = originLng = '';
			}

			initializeOriginMap();
		}

		w.changeOriginTitle = (title) => {
			if (marker) {
				marker.setTitle(name);
			}
		}

		w.changeOriginIcon = (icon) => {
			if (marker) {
				if (icon.length) {
					marker.setIcon('<?php echo JUri::root(); ?>' + icon);
				} else {
					marker.setIcon(null);
				}
			}
		}
		
		const initializeOriginMap = () => {
			if (originLat.length == 0) {
				$('#origin-googlemap').hide();
				$('#origin-map-warning').show();
				return;
			}

			const coord = new google.maps.LatLng(originLat, originLng);

			$('#origin-map-warning').hide();

			if (map) {
				// map already created, just display it
				$('#origin-googlemap').show();
				// and update the marker
				marker.setAnimation(google.maps.Animation.DROP);
				marker.setPosition(coord);
				map.setCenter(coord);
				return;
			}
			
			const mapProp = {
				center: coord,
				zoom: 17,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
			};
			
			map = new google.maps.Map($('#origin-googlemap')[0], mapProp);

			// create marker
			marker = new google.maps.Marker({
				position: coord,
				draggable: true,
				title: $('input[name="name"]').val(),
			});

			let icon = $('input[name="image"]').val();

			if (icon.length) {
				marker.setIcon('<?php echo JUri::root(); ?>' + icon);
			}

			// update circle position after dragging the marker
			marker.addListener('dragend', (e) => {
				const markerCoord = marker.getPosition();

				$('input[name="latitude"]').val(markerCoord.lat());
				$('input[name="longitude"]').val(markerCoord.lng());
			});

			infoWindow = new google.maps.InfoWindow();

			marker.addListener('click', (e) => {
				content = [
					$('<h3></h3>').html($('input[name="name"]').val()).html(),
					$('textarea[name="description"]').val(),
					$('input[name="address"]').val(),
				].filter((c) => {
					return c.length;
				}).join("<br /><br />");

				infoWindow.setContent(content);
				infoWindow.open(map, marker);
			});
			
			marker.setMap(map);
			
			$('#origin-googlemap').show();
		}

		$(function() {
			onInstanceReady(() => {
				// wait until Google Maps has been loaded
				return VikGMapsUtils.isReady;
			}).then(() => {
				// render Google Maps
				initializeOriginMap();
			});

			$('input[name="name"]').on('change', function() {
				changeOriginTitle($(this).val());
			});

			$('input[name="image"]').on('change', function() {
				changeOriginIcon($(this).val());
			});

			$('input[name="latitude"], input[name="longitude"]').on('change', () => {
				changeOriginLatLng(
					$('input[name="latitude"]').val(),
					$('input[name="longitude"]').val()
				);
			});
		});
	})(jQuery, window);
</script>