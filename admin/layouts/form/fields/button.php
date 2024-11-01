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

$id       = isset($displayData['id'])       ? $displayData['id']       : '';
$class    = isset($displayData['class'])    ? $displayData['class']    : '';
$text     = isset($displayData['text'])     ? $displayData['text']     : '';
$style    = isset($displayData['style'])    ? $displayData['style']    : '';
$click    = isset($displayData['onclick'])  ? $displayData['onclick']  : '';
$disabled = isset($displayData['disabled']) ? $displayData['disabled'] : false;
$type     = isset($displayData['type'])     ? $displayData['type']     : 'button';
$data     = isset($displayData['data'])     ? $displayData['data']     : '';

$class = ($class ? $class . ' ' : '') . 'btn';

?>

<button
    type="<?php echo $type; ?>"
    class="<?php echo $class; ?>"
    <?php echo $id ? 'id="' . $id . '"' : ''; ?>
    <?php echo $style ? 'style="' . $style . '"' : ''; ?>
    <?php echo $disabled ? 'disabled' : ''; ?>
    <?php echo $click ? 'onclick="' . $click . '"' : ''; ?>
    <?php echo $data; ?>
><?php echo $text; ?></button>