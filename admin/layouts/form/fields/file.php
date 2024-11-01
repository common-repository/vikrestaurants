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
$class    = isset($displayData['class'])    ? $displayData['class']    : '';
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$style    = isset($displayData['style'])    ? $displayData['style']    : '';
$data     = isset($displayData['data'])     ? $displayData['data']     : '';

?>

<input
	type="file"
	<?php echo $name ? 'name="' . $this->escape($name) . '"' : ''; ?>
    <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>
    <?php echo $class ? 'class="' . $this->escape($class) . '"' : ''; ?>
    <?php echo $style ? 'style="' . $this->escape($style) . '"' : ''; ?>
    <?php echo $disabled ? 'disabled' : ''; ?>
    <?php echo $data; ?>
    size="40"
/>
