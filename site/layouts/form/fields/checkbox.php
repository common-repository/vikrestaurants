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

JHtml::fetch('vrehtml.assets.fancybox');

$label    = isset($displayData['label'])    ? $displayData['label']    : '';
$name     = isset($displayData['name'])     ? $displayData['name']     : '';
$value    = isset($displayData['value'])    ? $displayData['value']    : 1;
$id       = isset($displayData['id'])       ? $displayData['id']       : '';
$class    = isset($displayData['class'])    ? $displayData['class']    : '';
$required = isset($displayData['required']) ? $displayData['required'] : false;
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$checked  = isset($displayData['checked'])  ? $displayData['checked']  : false;
$onchange = isset($displayData['onchange']) ? $displayData['onchange'] : '';
$field    = isset($displayData['field'])    ? $displayData['field']    : [];

$isreq = $required ? '<span class="vrrequired"><sup aria-hidden="true">*</sup></span> ' : '';
				
if (!empty($field['poplink']))
{
	if (preg_match("/^index.php/i", $field['poplink']))
	{
		// route link to be used externally
		$field['poplink'] = VREFactory::getPlatform()->getUri()->route($field['poplink']);
	}

	$label = "<a href=\"javascript: void(0);\" onclick=\"vreOpenPopup('" . $field['poplink'] . "');\">" . $label . "</a>";
}
else
{
	$label = "<span>" . $label . "</span>";
}

?>

<div class="vr-cf-checkbox-wrap">

	<input
		type="checkbox"
		name="<?php echo $this->escape($name); ?>"
		value="<?php echo $this->escape($value); ?>"
		id="<?php echo $this->escape($id); ?>"
		class="<?php echo $this->escape($class); ?>"
		<?php echo $disabled ? 'disabled="disabled"' : ''; ?>
		<?php echo $checked ? 'checked="checked"' : ''; ?>
		<?php echo $onchange ? 'onchange="' . $onchange . '"' : ''; ?>
	/>
	
	<label<?php echo ($id ? ' for="' . $this->escape($id) . '"' : ''); ?>>

		<?php if ($label && $required): ?>
			<span class="vrrequired"><sup aria-hidden="true">*</sup></span>
		<?php endif; ?>

		<?php echo $label; ?>

	</label>

</div>
