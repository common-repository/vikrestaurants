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

$name     = !empty($displayData['name'])    ? $displayData['name']     : '';
$id       = !empty($displayData['id'])      ? $displayData['id']       : $name;
$value    = isset($displayData['value'])    ? $displayData['value']    : '';
$class    = isset($displayData['class'])    ? $displayData['class']    : '';
$multiple = isset($displayData['multiple']) ? $displayData['multiple'] : false;
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$options  = isset($displayData['options'])  ? $displayData['options']  : array();

if ($multiple)
{
	$value = (array) $value;
}

// check if we have an associative array
$is_assoc = (array_keys($options) !== range(0, count($options) - 1));

// check whether we should build an array of options
if ($is_assoc || ($options && is_scalar($options[0])))
{
	$tmp = array();

	foreach ($options as $optValue => $optText)
	{
		if (!$is_assoc)
		{
			// in case of linear array, use the option text as value
			$optValue = $optText;
		}

		$tmp[] = JHtml::fetch('select.option', $optValue, $optText);
	}

	$options = $tmp;
}

if ($options && !strlen($options[0]->value) && !strlen($options[0]->text) && !$multiple)
{
	// use default placeholder provided by the CMS (only for single select)
	$options[0]->text = JText::translate('JGLOBAL_SELECT_AN_OPTION');
}

?>

<select
	<?php echo $name ? 'name="' . $this->escape($name) . '"' : ''; ?>
    <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>
	class="vr-cf-select vre-select <?php echo $this->escape($class); ?>"
	<?php echo $multiple ? 'multiple' : ''; ?>
	<?php echo $disabled ? 'disabled' : ''; ?>
>
	<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $value); ?>
</select>

<?php
if ($multiple)
{
	JHtml::fetch('vrehtml.assets.select2');
	JText::script('JGLOBAL_SELECT_AN_OPTION');
	?>
	<script>
		(function($) {
			'use strict';

			$(function() {
				$('#<?php echo $id; ?>').removeClass('vre-select').select2({
					placeholder: Joomla.JText._('JGLOBAL_SELECT_AN_OPTION'),
					allowClear: true,
					width: 'resolve',
				});
			});
		})(jQuery);
	</script>
	<?php
}
