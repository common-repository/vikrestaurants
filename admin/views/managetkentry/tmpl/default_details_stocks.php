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

$entry = $this->entry;

?>

<!-- ITEMS IN STOCK - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('items_in_stock')
	->value($entry->items_in_stock)
	->label(JText::translate('VRMANAGETKSTOCK3'))
	->description(JText::translate('VRMANAGETKSTOCK3_HELP'))
	->min(0)
	->step(1);
?>

<!-- NOTIFY BELOW - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('notify_below')
	->value($entry->notify_below)
	->label(JText::translate('VRMANAGETKSTOCK4'))
	->description(JText::translate('VRMANAGETKSTOCK4_HELP'))
	->min(0)
	->step(1);
?>
