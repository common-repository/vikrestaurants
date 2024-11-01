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

$name     = isset($displayData['name'])     ? $displayData['name']     : '';
$id       = isset($displayData['id'])       ? $displayData['id']       : $name;
$value    = isset($displayData['value'])    ? $displayData['value']    : '';
$class    = isset($displayData['class'])    ? $displayData['class']    : '';
$min      = isset($displayData['min'])      ? $displayData['min']      : '';
$max      = isset($displayData['max'])      ? $displayData['max']      : '';
$step     = isset($displayData['step'])     ? $displayData['step']     : 'any';
$title    = isset($displayData['title'])    ? $displayData['title']    : '';
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$readonly = isset($displayData['readonly']) ? $displayData['readonly'] : false;
$style    = isset($displayData['style'])    ? $displayData['style']    : '';
$data     = isset($displayData['data'])     ? $displayData['data']     : '';

?>

<input
	type="number"
	name="<?php echo $this->escape($name); ?>"
	id="<?php echo $this->escape($id); ?>"
	value="<?php echo (float) $value; ?>"
	size="40"
	class="<?php echo $this->escape($class); ?>"
	<?php echo strlen($min) ? 'min="' . (float) $min . '"' : ''; ?>
	<?php echo strlen($max) ? 'max="' . (float) $max . '"' : ''; ?>
	step="<?php echo $this->escape($step); ?>"
	<?php echo $title ? 'title="' . $this->escape($title) . '"' : ''; ?>
	<?php echo $style ? 'style="' . $this->escape($style) . '"' : ''; ?>
	<?php echo $disabled ? 'disabled' : ''; ?>
    <?php echo $readonly && !$disabled ? 'readonly' : ''; ?>
	<?php echo $data; ?>
/>
