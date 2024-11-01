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
$maxlen   = isset($displayData['maxlength'])   ? $displayData['maxlength']   : false;
$disabled = isset($displayData['disabled'])    ? $displayData['disabled']    : false;
$readonly = isset($displayData['readonly'])    ? $displayData['readonly']    : false;
$style    = isset($displayData['style'])       ? $displayData['style']       : '';
$tabindex = isset($displayData['tabindex'])    ? $displayData['tabindex']    : null;
$data     = isset($displayData['data'])        ? $displayData['data']        : '';

// append "has-value" class in case the field is auto-completed
$class .= strlen($value) ? ' has-value' : '';

?>

<input
	type="password"
	<?php echo $name ? 'name="' . $this->escape($name) . '"' : ''; ?>
    <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>
	value="<?php echo $this->escape($value); ?>"
	size="40"
	class="vrinput <?php echo $this->escape($class); ?>"
    <?php echo $style ? 'style="' . $this->escape($style) . '"' : ''; ?>
    <?php echo $hint ? 'placeholder="' . $this->escape($hint) . '"' : ''; ?>
    <?php echo $maxlen ? 'maxlength="' . abs((int) $maxlen) . '"' : ''; ?>
    <?php echo $disabled ? 'disabled' : ''; ?>
    <?php echo $readonly && !$disabled ? 'readonly' : ''; ?>
    <?php echo is_null($tabindex) ? '' : 'tabindex="' . (int) $tabindex . '"'; ?>
    <?php echo $data; ?>
    autocomplete="off"
/>
