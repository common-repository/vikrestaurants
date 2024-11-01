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

$name  = !empty($displayData['name']) ? $displayData['name']  : 'name';
$id    = !empty($displayData['id'])   ? $displayData['id']    : $name;
$value = isset($displayData['value']) ? $displayData['value'] : '';
$class = isset($displayData['class']) ? $displayData['class'] : '';

$config = [
	// validate phone number field to make sure
	// the specified value is a valid phone
	// 'validator' => 'vrCustomFieldsValidator',
	
	// custom data to be passed when initializing
	// international tel input
	'data' => [
		// display flags dropdown according to the
		// global configuration (Show Prefix Selection)
		'allowDropdown' => VREFactory::getConfig()->getBool('phoneprefix'),
	],
];

// render input using intltel
JHtml::fetch('vrehtml.assets.intltel', '#' . $id, $config);

// append "has-value" class in case the field is auto-completed
$class .= strlen($value) ? ' has-value' : '';
?>

<input
	type="tel"
	name="<?php echo $this->escape($name); ?>"
	id="<?php echo $this->escape($id); ?>"
	value="<?php echo $this->escape($value); ?>"
	size="40"
	class="vrinput <?php echo $this->escape($class); ?>"
/>

<input type="hidden" name="<?php echo $this->escape($id); ?>_dialcode" value="" />
<input type="hidden" name="<?php echo $this->escape($id); ?>_country" value="" />

<script>
	(function($) {
		'use strict';

		$(function() {
			// save "country code" and "dial code" every time the phone number changes
			$('#<?php echo $id; ?>').on('change countrychange', function() {
				let country = $(this).intlTelInput('getSelectedCountryData');

				if (!country) {
					return false;
				}

				if (country.iso2) {
					$('input[name="<?php echo $id; ?>_country"]').val(country.iso2.toUpperCase());
				}

				if (country.dialCode) {
					let dial = '+' + country.dialCode.toString().replace(/^\+/);

					if (country.areaCodes) {
						dial += ' ' + country.areaCodes[0];
					}

					$('input[name="<?php echo $id; ?>_dialcode"]').val(dial);
				}
			}).trigger('change');
		});
	})(jQuery);
</script>