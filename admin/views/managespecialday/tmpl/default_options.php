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

$specialday = $this->specialday;

?>

<!-- PRIORITY - Dropdown -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('priority')
	->value($specialday->priority)
	->label(JText::translate('VRMANAGESPDAY20'))
	->options([
		JHtml::fetch('select.option', 1, JText::translate('VRPRIORITY1')),
		JHtml::fetch('select.option', 2, JText::translate('VRPRIORITY2')),
		JHtml::fetch('select.option', 3, JText::translate('VRPRIORITY3')),
	]);
?>

<!-- IGNORE CLOSING DAYS - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('ignoreclosingdays')
	->checked($specialday->ignoreclosingdays)
	->label(JText::translate('VRMANAGESPDAY13'))
	->description(JText::translate('VRMANAGESPDAY13_HELP'));
?>

<!-- MARK ON CALENDAR - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('markoncal')
	->checked($specialday->markoncal)
	->label(JText::translate('VRMANAGESPDAY12'))
	->description(JText::translate('VRMANAGESPDAY12_HELP'));
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('select[name="priority"]').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});
		});
	})(jQuery);
</script>