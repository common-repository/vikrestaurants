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

if (!empty($control['class']))
{
	// prepend default control class
	$control['class'] = 'control-group ' . $control['class'];
}
else
{
	// set default control class
	$control['class'] = 'control-group';
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
            <div class="hide-aware-inline-help">
                <small class="form-text"><?php echo $description; ?></small>
            </div>
        <?php endif; ?>
	</div>

</div>