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

$vik = VREApplication::getInstance();

?>

<div class="row-fluid">

	<div class="span12 full-width">
		<?php
		echo $vik->openEmptyFieldset('');

		// create custom fields renderer for the restaurant group
		$renderer = new E4J\VikRestaurants\CustomFields\FieldsRenderer($this->customFields);

		/**
		 * Render the custom fields form by using the apposite helper.
		 *
		 * Looking for a way to override the custom fields? Take a look
		 * at "/layouts/form/fields/" folder, which should contain all
		 * the supported types of custom fields.
		 *
		 * @since 1.9
		 */
		echo $renderer->display($this->reservation->custom_f, [
			'strict' => false,
		]);
			
		echo $vik->closeEmptyFieldset();
		?>
	</div>

</div>

<?php
// create name-id custom fields lookup
$lookup = [];

foreach ($this->customFields as $field)
{
	$lookup[$field['name']] = $field['id'];
}

JText::script('JGLOBAL_SELECT_AN_OPTION');
?>

<script>
	(function($, w) {
		'use strict';

		const CUSTOM_FIELDS_LOOKUP = <?php echo json_encode($lookup); ?>;

		w.compileCustomFields = (fields) => {
			$.each(fields, (name, value) => {
				if (!CUSTOM_FIELDS_LOOKUP.hasOwnProperty(name)) {
					// field not found, next one
					return true;
				}

				const input = $('*[name="vrcf' + CUSTOM_FIELDS_LOOKUP[name] + '"]');

				if (input.length) {
					if (input.is('select')) {
						if (input.find('option[value="' + value + '"]').length) {
							// refresh select value if the option exists
							input.select2('val', value);
						} else {
							// otherwise select the first option
							input.select2('val', input.find('option').first().val());
						}
					} else if (input.is(':checkbox')) {
						// check/uncheck the input
						input.prop('checked', value ? true : false);
					} else if (input.hasClass('phone-field')) {
						// update phone number
						input.intlTelInput('setNumber', value);
					} else {
						// otherwise refresh as default input
						try {
							input.val(value);

							if (input.data('alt-value') !== undefined) {
								// we are probably updating a calendar,
								// make sure to update also the alt value
								input.attr('data-alt-value', value);
							}
						} catch (error) {
							// catch error because input file might raise
							// an error while trying to set a value
						}
					}
				}
			});
		}

		$(function() {
			// render select
			$('select.custom-field').each(function() {
				// check whether the first option is a placeholder
				let hasPlaceholder = $(this).find('option').first().text().length == 0;

				$(this).select2({
					width: '90%',
					// check whether we should specify a placeholder
					placeholder: hasPlaceholder ? Joomla.JText._('JGLOBAL_SELECT_AN_OPTION') : '',
					// disable search for select with 3 or lower options
					minimumResultsForSearch: $(this).find('option').length > 3 ? 0 : -1,
					// check whether the field supports empty values
					allowClear: !$(this).hasClass('required') && hasPlaceholder ? true : false,
				});
			});
		});
	})(jQuery, window);
</script>