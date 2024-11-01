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

$menu = $this->menu;

$shifts = JHtml::fetch('vrehtml.admin.shifts', $restaurantGroup = 1);

?>
			
<!-- PUBLISHED - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('published')
	->checked($menu->published)
	->label(JText::translate('VRMANAGEMENU26'));
?>

<!-- SPECIAL DAY - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('special_day')
	->checked($menu->special_day)
	->label(JText::translate('VRMANAGEMENU2'))
	->description(JText::translate('VRMANAGEMENU2_HELP'))
	->onchange('specialDayValueChanged(this.checked)');
?>

<!-- WORKING SHIFTS - Select -->

<?php
if (count($shifts))
{
	echo $this->formFactory->createField()
		->type('select')
		->name('working_shifts')
		->id('vrwsselect')
		->value($menu->working_shifts)
		->label(JText::translate('VRMANAGEMENU3'))
		->multiple(true)
		->options($shifts)
		->control([
			'class' => 'vr-spday-child',
			'style' => $menu->special_day ? 'display: none;' : '',
		]);
}
?>

<!-- DAYS FILTER - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('days_filter')
	->id('vrdfselect')
	->value($menu->days_filter)
	->label(JText::translate('VRMANAGEMENU4'))
	->multiple(true)
	->options(JHtml::fetch('vikrestaurants.days'))
	->control([
		'class' => 'vr-spday-child',
		'style' => $menu->special_day ? 'display: none;' : '',
	]);
?>

<?php
JText::script('VRMANAGEMENU24');
JText::script('VRMANAGEMENU25');
?>

<script>
	(function($, w) {
		'use strict';

		w.specialDayValueChanged = (checked) => {
			if (checked) {
				$('.vr-spday-child').hide();
			} else {
				$('.vr-spday-child').show();
			}
		}

		$(function() {
			$('#vrwsselect').select2({
				placeholder: Joomla.JText._('VRMANAGEMENU24'),
				allowClear: true,
				width: '100%',
			});
			
			$('#vrdfselect').select2({
				placeholder: Joomla.JText._('VRMANAGEMENU25'),
				allowClear: true,
				width: '100%',
			});
		});
	})(jQuery, window);
</script>