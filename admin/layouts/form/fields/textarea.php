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

$name     = !empty($displayData['name'])       ? $displayData['name']        : '';
$id       = !empty($displayData['id'])         ? $displayData['id']          : $name;
$value    = isset($displayData['value'])       ? $displayData['value']       : '';
$class    = isset($displayData['class'])       ? $displayData['class']       : '';
$hint     = isset($displayData['placeholder']) ? $displayData['placeholder'] : '';
$width    = isset($displayData['width'])       ? $displayData['width']       : '';
$height   = isset($displayData['height'])      ? $displayData['height']      : '80px';
$disabled = isset($displayData['disabled'])    ? $displayData['disabled']    : false;
$readonly = isset($displayData['readonly'])    ? $displayData['readonly']    : false;
$style    = isset($displayData['style'])       ? $displayData['style']       : [];
$tabindex = isset($displayData['tabindex'])    ? $displayData['tabindex']    : null;

$style = (array) $style;

if ($width)
{
	$style[] = 'width: ' . $width . (is_numeric($width) ? 'px' : '') . ';';
}

if ($height)
{
	$style[] = 'height: ' . $height . (is_numeric($height) ? 'px' : '') . ';';
}

?>

<textarea
	<?php echo $name ? 'name="' . $this->escape($name) . '"' : ''; ?>
    <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>
	class="<?php echo $this->escape($class); ?>"
	style="<?php echo $this->escape(implode(' ', $style)); ?>"
    <?php echo $hint ? 'placeholder="' . $this->escape($hint) . '"' : ''; ?>
	<?php echo is_null($tabindex) ? '' : 'tabindex="' . (int) $tabindex . '"'; ?>
    <?php echo $disabled ? 'disabled' : ''; ?>
    <?php echo $readonly && !$disabled ? 'readonly' : ''; ?>
><?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?></textarea>
