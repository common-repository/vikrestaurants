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
$value    = isset($displayData['value'])    ? $displayData['value']    : 1;
$id       = isset($displayData['id'])       ? $displayData['id']       : '';
$class    = isset($displayData['class'])    ? $displayData['class']    : '';
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$checked  = isset($displayData['checked'])  ? $displayData['checked']  : false;
$onchange = isset($displayData['onchange']) ? $displayData['onchange'] : '';

$class = ($class ? $class . ' ' : '') . 'ios-toggle ios-toggle-round';

?>

<span class="switch-ios">
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

    <label for="<?php echo $this->escape($id); ?>"></label>
</span>
