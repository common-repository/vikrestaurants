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
$min      = isset($displayData['min'])      ? $displayData['min']      : 0;
$max      = isset($displayData['max'])      ? $displayData['max']      : 100;
$title    = isset($displayData['title'])    ? $displayData['title']    : '';
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$style    = isset($displayData['style'])    ? $displayData['style']    : '';
$data     = isset($displayData['data'])     ? $displayData['data']     : '';

?>

<input
	type="range"
	name="<?php echo $this->escape($name); ?>"
	id="<?php echo $this->escape($id); ?>"
	value="<?php echo (float) $value; ?>"
	size="40"
	class="<?php echo $this->escape($class); ?>"
	min="<?php echo (float) $min; ?>"
	max="<?php echo (float) $max; ?>"
	<?php echo $title ? 'title="' . $this->escape($title) . '"' : ''; ?>
	<?php echo $style ? 'style="' . $this->escape($style) . '"' : ''; ?>
	<?php echo $disabled ? 'disabled' : ''; ?>
	<?php echo $data; ?>
/>
