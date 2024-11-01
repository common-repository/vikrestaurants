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

$id     = isset($displayData['id'])     ? $displayData['id']     : '';
$class  = isset($displayData['class'])  ? $displayData['class']  : '';
$text   = isset($displayData['text'])   ? $displayData['text']  : '';
$href   = isset($displayData['href'])   ? $displayData['href']   : 'javascript:void(0)';
$target = isset($displayData['target']) ? $displayData['target'] : '';

$class = ($class ? $class . ' ' : '') . 'btn';

// try to make "&" XML safe
$href = preg_replace("/&(?!amp;)/", '&amp;', $href);

?>

<a
    href="<?php echo $href; ?>"
    class="<?php echo $class; ?>"
    id="<?php echo $id; ?>"
    <?php echo $target ? 'target="' . $target . '"' : ''; ?>
><?php echo $text; ?></a>