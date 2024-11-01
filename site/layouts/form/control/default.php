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
$control     = isset($displayData['control'])     ? $displayData['control']     : [];
$validator   = isset($displayData['validator'])   ? $displayData['validator']   : '';
$field       = isset($displayData['field'])       ? $displayData['field']       : [];

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

if (empty($displayData['hiddenLabel']))
{
	// choose for what field types the label should be always hidden
	$displayData['hiddenLabel'] = in_array($field['type'] ?? '', ['checkbox', 'separator']);
}

?>

<div<?php echo $attr; ?>>

	<?php if (empty($displayData['hiddenLabel'])): ?>
		<div class="control-label">
			<label<?php echo ($id ? ' for="' . $this->escape($id) . '"' : ''); ?>>
				<?php echo $label; ?>

				<?php if ($label && $required): ?>
					<span class="star" aria-hidden="true">*</span>
				<?php endif; ?>
			</label>
		</div>
	<?php endif; ?>

	<div class="controls">
		<?php echo $input; ?>

		<?php if ($description): ?>
			<small class="control-description"><?php echo $description; ?></small>
        <?php endif; ?>
	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			<?php if ($validator): ?>
				onInstanceReady(() => {
					// wait until the provided validator has been loaded
					return w['<?php echo $validator; ?>'];
				}).then((validator) => {

					// copy the default method
					validator._getLabel = VikFormValidator.prototype.getLabel;

					/**
					 * Overwrite getLabel method to properly access the
					 * label by using our custom layout.
					 *
					 * @param   mixed  input  The input element.
					 *
					 * @return  mixed  The label of the input.
					 */
					validator.getLabel = function(input) {
						if ($(input).is(':checkbox')) {
							// get label next to the checkbox
							const label = $(input).next('label');

							// check if we have a popup link
							if (label.find('a')) {
								return label.find('a');
							}

							return label;
						}

						// invoke default getter
						return this._getLabel(input);
					}
				});
			<?php endif; ?>
		});

	})(jQuery, window);
</script>