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

$input       = isset($displayData['input'])       ? $displayData['input']       : '';
$label       = isset($displayData['label'])       ? $displayData['label']       : '';
$description = isset($displayData['description']) ? $displayData['description'] : '';
$required    = isset($displayData['required'])    ? $displayData['required']    : false;
$id          = isset($displayData['id'])          ? $displayData['id']          : '';
$validator   = isset($displayData['validator'])   ? $displayData['validator']   : '';
$control     = isset($displayData['control'])     ? $displayData['control']     : [];
$field       = isset($displayData['field'])       ? $displayData['field']       : [];

if (!empty($control['class']))
{
	// prepend default control class
	$control['class'] = 'cf-control ' . $control['class'];
}
else
{
	// set default control class
	$control['class'] = 'cf-control';
}

if (empty($control['id']) && $id)
{
	// use a default ID for the control
	$control['id'] = $id . '-control';
}

if (isset($control['visible']) && !$control['visible'])
{
	// hide control when hidden
	$control['style'] = (!empty($control['style']) ? $control['style'] . ' ' : '') . 'display: none;';
}

if (!$required && isset($control['required']))
{
	// use the required value specified for the control
	$required = (bool) $control['required'];
}

$attr = '';

// stringify control attributes
foreach ($control as $k => $v)
{
	if (in_array($k, ['visible', 'required']))
	{
		continue;
	}

	$attr .= ' ' . $k . '="' . $this->escape($v) . '"';
}

// choose for what field types the label should be displayed inline
$inlineLabel = in_array($field['type'] ?? '', ['select', 'html']);

if (empty($displayData['hiddenLabel']))
{
	// choose for what field types the label should be always hidden
	$displayData['hiddenLabel'] = !empty($field['hiddenLabel']) || in_array($field['type'] ?? '', ['checkbox', 'separator']);
}

?>

<div<?php echo $attr; ?>>

	<?php if ($inlineLabel && empty($displayData['hiddenLabel'])): ?>
		<div class="cf-label inline">
			<label<?php echo ($id ? ' for="' . $this->escape($id) . '"' : ''); ?>>
			
				<?php echo $label; ?>

				<?php if ($label && $required): ?>
					<span class="vrrequired"><sup aria-hidden="true">*</sup></span>
				<?php endif; ?>

			</label>
		</div>
	<?php endif; ?>

	<div class="cf-value">

		<?php if (empty($displayData['hiddenLabel']) && !$inlineLabel): ?>
			<?php
			/**
			 * Added a hidden label before the input to fix the auto-complete
			 * bug on Safari, which always expects to have the inputs displayed
			 * after their labels.
			 *
			 * @since 1.8.2
			 */
			?>
			<label<?php echo ($id ? ' for="' . $this->escape($id) . '"' : ''); ?> style="display: none;">
				<?php echo $label; ?>
			</label>
		<?php endif; ?>

		<?php echo $input; ?>

		<?php if (empty($displayData['hiddenLabel']) && !$inlineLabel): ?>
			<span class="cf-highlight"><!-- input highlight --></span>

			<span class="cf-bar"><!-- input bar --></span>

			<label<?php echo ($id ? ' for="' . $this->escape($id) . '"' : ''); ?> class="cf-label">
			
				<?php echo $label; ?>

				<?php if ($label && $required): ?>
					<span class="vrrequired"><sup aria-hidden="true">*</sup></span>
				<?php endif; ?>

			</label>
		<?php endif; ?>

	</div>

	<?php if ($description): ?>
        <div class="cf-description">
        	<?php echo $description; ?>
        </div>
    <?php endif; ?>

</div>

<script>
	(function($, w) {
		'use strict';

		const handleCustomFieldFocus = (input) => {
			$(input).closest('.cf-value')
				.find('.cf-bar, .cf-label')
					.addClass('focus');
		}

		const handleCustomFieldBlur = (input) => {
			let val = $(input).val();

			if ($(input).attr('type') == 'tel' && $.fn.intlTelInput) {
				// get number to make sure we are not obtaining only the default prefix
				val = $(input).intlTelInput('getNumber');
			}

			// remove class from label only if empty
			if (!val || val.length == 0) {
				$(input).closest('.cf-value')
					.find('.cf-label')
						.removeClass('focus');
			}

			$(input).closest('.cf-value')
				.find('.cf-bar')
					.removeClass('focus');
		}

		$(function() {
			// get current field
			const input = $('#<?php echo $id; ?>');

			// add/remove has-value class during change and blur events
			$(input).on('change blur', function() {
				let val = $(this).val();

				if (val && val.length) {
					$(this).addClass('has-value');
				} else {
					$(this).removeClass('has-value');
				}
			});

			// handle focus classes on siblings (on focus)
			$(input).on('focus', function() {
				handleCustomFieldFocus(this);
			});

			// handle focus classes on siblings (on blur)
			$(input).on('blur', function() {
				handleCustomFieldBlur(this);
			});

			if ($(input).attr('type') === 'tel') {
				// trigger focus and blur to properly set the control classes
				// for the telephone input, since stand-alone CSS cannot deal
				// with the composite structure of this field
				handleCustomFieldFocus(input);
				handleCustomFieldBlur(input);
			}

			<?php if ($validator): ?>
				onInstanceReady(() => {
					// wait until the provided validator has been loaded
					return w['<?php echo $validator; ?>'];
				}).then((validator) => {
					/**
					 * Overwrite getLabel method to properly access the
					 * label by using our custom layout.
					 *
					 * @param   mixed  input  The input element.
					 *
					 * @return  mixed  The label of the input.
					 */
					validator.getLabel = (input) => {
						if ($(input).is(':checkbox')) {
							// get label next to the checkbox
							const label = $(input).next('label');

							// check if we have a popup link
							if (label.find('a')) {
								return label.find('a');
							}

							return label;
						}

						return $(input).closest('.cf-value').find('.cf-label');
					}
				});
			<?php endif; ?>
		});
	})(jQuery, window);
</script>