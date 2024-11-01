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

$coupon = $this->coupon;

?>

<!-- NOTES - Textarea -->

<?php
echo $this->formFactory->createField()
	->type('textarea')
	->name('notes')
	->value($coupon->notes)
	->class('full-width')
	->height(180)
	->style('resize: vertical;')
	->hiddenLabel(true)
	->description(JText::translate('VRE_NOTES_COUPON_DESC'));
?>
