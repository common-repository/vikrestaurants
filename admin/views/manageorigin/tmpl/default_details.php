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

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($origin->name)
	->required(true)
	->class('input-xxlarge input-large-text')
	->label(JText::translate('VRMANAGELANG2'));
?>

<!-- ADDRESS - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('address')
	->value($origin->address)
	->required(true)
	->label(JText::translate('VRCUSTFIELDRULE4'));
?>

<!-- LATITUDE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('latitude')
	->value($origin->latitude)
	->label(JText::translate('VRMANAGETKAREA7'))
	->min(-90)
	->max(90)
	->step('any');
?>

<!-- LONGITUDE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('longitude')
	->value($origin->longitude)
	->label(JText::translate('VRMANAGETKAREA8'))
	->min(-180)
	->max(180)
	->step('any');
?>

<!-- PUBLISHED - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('published')
	->checked($origin->published)
	->label(JText::translate('VRMANAGETKTOPPING3'));
?>

<!-- IMAGE UPLOAD - Image -->

<?php
echo $this->formFactory->createField()
	->name('image')
	->value($origin->image)
	->label(JText::translate('VRE_ORIGIN_MARKER_IMAGE'))
	->description(JText::translate('VRE_ORIGIN_MARKER_IMAGE_DESC'))
	->render(function($data, $input) {
		return VREApplication::getInstance()->getMediaField($data->get('name'), $data->get('value'));
	});
?>

<script>
	(function($) {
		'use strict';

		// register Google Autocomplete
		$(function() {
			// listen console to catch any interesting error
			VikGMapsUtils.listenConsole();

			if (typeof google === 'undefined' || typeof google.maps.places === 'undefined') {
				// Missing Google API Key or Places API not enabled, do not proceed
				return false;
			}

			<?php
			if (VikRestaurants::isGoogleMapsApiEnabled('places'))
			{
				// include JavaScript code to support the addresses autocompletion
				// only in case the Places API is enabled in the configuration
				?>
				const input = $('input[name="address"]')[0];

				// use Google Autocomplete feature
				const googleAddress = new google.maps.places.Autocomplete(
					input, {}
				);

				googleAddress.addListener('place_changed', function() {
					const place = googleAddress.getPlace();

					// auto-fill latitude and longitude
					if (place.geometry) {
						$('input[name="latitude"]').val(place.geometry.location.lat());
						$('input[name="longitude"]').val(place.geometry.location.lng()).trigger('change');
					}
				});

				$(window).on('google.autherror google.apidisabled.places', () => {
					// disable autocomplete on failure
					VikGMapsUtils.disableAutocomplete(input, googleAddress);
				});

				VikGeo.getCurrentPosition().then((coord) => {
					// coordinates retrieved, set up google bounds
					const circle = new google.maps.Circle({
						center: coord,
						radius: 100,
					});

		  			googleAddress.setBounds(circle.getBounds());
				}).catch((error) => {
					// unable to obtain current position, show error
					console.error(error);
				});
				<?php
			}
			?>
		});
	})(jQuery);
</script>