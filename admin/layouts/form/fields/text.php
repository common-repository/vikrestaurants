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

$type     = !empty($displayData['type'])       ? $displayData['type']        : 'text';
$name     = isset($displayData['name'])        ? $displayData['name']        : '';
$id       = isset($displayData['id'])          ? $displayData['id']          : $name;
$value    = isset($displayData['value'])       ? $displayData['value']       : '';
$class    = isset($displayData['class'])       ? $displayData['class']       : '';
$hint     = isset($displayData['placeholder']) ? $displayData['placeholder'] : '';
$title    = isset($displayData['title'])       ? $displayData['title']       : '';
$maxlen   = isset($displayData['maxlength'])   ? $displayData['maxlength']   : false;
$disabled = isset($displayData['disabled'])    ? $displayData['disabled']    : false;
$readonly = isset($displayData['readonly'])    ? $displayData['readonly']    : false;
$style    = isset($displayData['style'])       ? $displayData['style']       : '';
$pattern  = isset($displayData['pattern'])     ? $displayData['pattern']     : null;
$tabindex = isset($displayData['tabindex'])    ? $displayData['tabindex']    : null;
$data     = isset($displayData['data'])        ? $displayData['data']        : '';

?>

<input
	type="<?php echo $this->escape($type); ?>"
	<?php echo $name ? 'name="' . $this->escape($name) . '"' : ''; ?>
    <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>
	value="<?php echo $this->escape($value); ?>"
	size="40"
	class="<?php echo $this->escape($class); ?>"
    <?php echo $style ? 'style="' . $this->escape($style) . '"' : ''; ?>
    <?php echo $title ? 'title="' . $this->escape($title) . '"' : ''; ?>
    <?php echo $hint ? 'placeholder="' . $this->escape($hint) . '"' : ''; ?>
    <?php echo $maxlen ? 'maxlength="' . abs((int) $maxlen) . '"' : ''; ?>
    <?php echo $disabled ? 'disabled' : ''; ?>
    <?php echo $readonly && !$disabled ? 'readonly' : ''; ?>
    <?php echo $pattern ? 'pattern="' . $this->escape($pattern) . '"' : ''; ?>
    <?php echo is_null($tabindex) ? '' : 'tabindex="' . (int) $tabindex . '"'; ?>
    <?php echo $data; ?>
/>