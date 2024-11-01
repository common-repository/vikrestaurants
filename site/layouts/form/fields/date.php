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

$name     = isset($displayData['name'])        ? $displayData['name']        : '';
$id       = isset($displayData['id'])          ? $displayData['id']          : $name;
$value    = isset($displayData['value'])       ? $displayData['value']       : '';
$class    = isset($displayData['class'])       ? $displayData['class']       : '';
$hint     = isset($displayData['placeholder']) ? $displayData['placeholder'] : '';
$style    = isset($displayData['style'])       ? $displayData['style']       : '';
$data     = isset($displayData['data'])        ? $displayData['data']        : '';

// append "has-value" class in case the field is auto-completed
$class .= strlen($value) ? ' has-value' : '';

// prepare jQuery datepicker options
$options = [
	/**
	 * Whether the year should be rendered as a dropdown instead of text.
	 * Use the yearRange option to control which years are made available
	 * for selection.
	 *
	 * Since the calendar field is usually used for Birth dates, the year
	 * selection will be enabled by default, so that the date can be filled
	 * without having to manually enter the year through the keyboard.
	 *
	 * @var boolean
	 *
	 * @link https://api.jqueryui.com/datepicker/#option-changeYear
	 */
	'changeYear' => true,

	/**
	 * The range of years displayed in the year drop-down: either relative
	 * to today's year ("-nn:+nn"), relative to the currently selected year
	 * ("c-nn:c+nn"), absolute ("nnnn:nnnn"), or combinations of these formats
	 * ("nnnn:-nn").
	 *
	 * Since the calendar field is usually used for Birth dates, the year
	 * selection won't allow future years and will go back up to 100 years.
	 *
	 * @var boolean
	 *
	 * @link https://api.jqueryui.com/datepicker/#option-yearRange
	 */
	'yearRange' => '-100:+0',
];

// handle datepicker scripts
JHtml::fetch('vrehtml.sitescripts.calendar', '#' . $id . ':input', $options);

?>

<input
	type="text"
	<?php echo $name ? 'name="' . $this->escape($name) . '"' : ''; ?>
    <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>
	value="<?php echo $this->escape($value); ?>"
	size="40"
	class="vrinput vrcalendar <?php echo $this->escape($class); ?>"
    <?php echo $style ? 'style="' . $this->escape($style) . '"' : ''; ?>
    <?php echo $hint ? 'placeholder="' . $this->escape($hint) . '"' : ''; ?>
    <?php echo $data; ?>
/>

<script>
	$(function() {
		$('#<?php echo $id; ?>:input').datepicker('option', 'onSelect', function() {
			$(this).trigger('blur');
		});
	});
</script>