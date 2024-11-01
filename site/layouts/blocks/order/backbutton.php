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

/**
 * Layout variables
 * -----------------
 * @var  bool    $display  Whether the back button should be displayed or not (true by default).
 * @var  string  $text     The text to use for the button ("View All Orders" by default).
 * @var  int     $itemid   An optional Item ID to use for URL rewriting.
 */
$display = isset($displayData['display']) ? (bool) $displayData['display'] : true;
$text    = isset($displayData['text'])    ? (string) $displayData['text']  : '';
$itemid  = isset($displayData['itemid'])  ? (int) $displayData['itemid']   : 0;

if ($display === false)
{
    // do not display the back button
    return;
}
?>

<div class="vreorder-backbox">
    <a
        href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=allorders' . ($itemid ? '&Itemid=' . $itemid : '')); ?>"
        class="vre-btn primary small"
    >
        <?php echo $text ?: JText::translate('VRALLORDERSBUTTON'); ?>
    </a>
</div>
