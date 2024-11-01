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

<!-- START - Calendar -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('start_ts')
	->value($specialday->start_ts == -1 ? '' : $specialday->start_ts)
	->label(JText::translate('VRMANAGESPDAY2'));
?>

<!-- END - Calendar -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('end_ts')
	->value($specialday->end_ts == -1 ? '' : $specialday->end_ts)
	->label(JText::translate('VRMANAGESPDAY3'));
?>

<!-- DAYS FILTER - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('days_filter')
	->id('vrdfselect')
	->value($specialday->days_filter)
	->multiple(true)
	->label(JText::translate('VRMANAGESPDAY5'))
	->options(JHtml::fetch('vikrestaurants.days'));
?>

<!-- WORKING SHIFTS - Select -->

<?php
if (VREFactory::getConfig()->getUint('opentimemode') === 1)
{
	// field to create working shifts on-the-fly
	$newShiftButton = $this->formFactory->createField()
		->type('button')
		->text('<i class="fas fa-plus"></i>')
		->class('sd-shifts-create-btn')
		->hidden(true);

	$shiftsValue = $specialday->working_shifts;

	foreach ($specialday->custom_shifts as $sh)
	{
		$shiftsValue[] = json_encode($sh);
	}

	// restaurant working shifts
	echo $this->formFactory->createField()
		->type('select')
		->name($specialday->group == 1 ? 'working_shifts' : '')
		->id('vr-restaurant-shifts')
		->value($specialday->group == 1 ? $shiftsValue : [])
		->multiple(true)
		->label(JText::translate('VRMANAGESPDAY4'))
		->options(array_merge(
			JHtml::fetch('vrehtml.admin.shifts', 1),
			array_map(function($sh)
			{
				return JHtml::fetch('select.option', json_encode($sh), $sh->name);
			}, $specialday->custom_shifts)
		))
		->control([
			'class' => 'restaurant-params',
			'style' => $specialday->group == 1 ? '' : 'display: none;',
		])
		->render(function($data, $input) use ($newShiftButton) {
			?>
			<div class="multi-field">
				<?php echo $input; ?>

				<div class="btn-group flex-auto">
					<?php echo $newShiftButton; ?>
				</div>
			</div>
			<?php
		});

	// take-away working shifts
	echo $this->formFactory->createField()
		->type('select')
		->name($specialday->group == 2 ? 'working_shifts' : '')
		->id('vr-takeaway-shifts')
		->value($specialday->group == 2 ? $shiftsValue : [])
		->multiple(true)
		->label(JText::translate('VRMANAGESPDAY4'))
		->options(array_merge(
			JHtml::fetch('vrehtml.admin.shifts', 2),
			array_map(function($sh)
			{
				return JHtml::fetch('select.option', json_encode($sh), $sh->name);
			}, $specialday->custom_shifts)
		))
		->control([
			'class' => 'takeaway-params',
			'style' => $specialday->group == 2 ? '' : 'display: none;',
		])
		->render(function($data, $input) use ($newShiftButton) {
			?>
			<div class="multi-field">
				<?php echo $input; ?>

				<div class="btn-group flex-auto">
					<?php echo $newShiftButton; ?>
				</div>
			</div>
			<?php
		});
}
?>

<?php
// render inspector to create working shifts on-the-fly
echo JHtml::fetch(
    'vrehtml.inspector.render',
    'specialday-shifts-inspector',
    array(
        'title'       => JText::translate('VRMENUSHIFTS'),
        'closeButton' => true,
        'keyboard'    => false,
        'footer'      => '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('VRSAVE') . '</button>',
    ),
    $this->loadTemplate('shifts_modal')
);

JText::script('VRMANAGEMENU24');
JText::script('VRMANAGEMENU25');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vr-restaurant-shifts, #vr-takeaway-shifts').select2({
				placeholder: Joomla.JText._('VRMANAGEMENU24'),
				allowClear: true,
				width: 400,
			});
			
			$('#vrdfselect').select2({
				placeholder: Joomla.JText._('VRMANAGEMENU25'),
				allowClear: true,
				width: 400,
			});

			$('.sd-shifts-create-btn').on('click', () => {
				vreOpenInspector('specialday-shifts-inspector');
			});

			$('#specialday-shifts-inspector').on('inspector.save', function() {
				// validate form
				if (!workingShiftsValidator.validate()) {
					return false;
				}

				// get saved record
				let shift = getWorkingShift();

				// create a new option for the working shifts dropdown
				const option = $('<option></option>')
					.text(shift.name)
					.val(JSON.stringify(shift));

				$('select[name="working_shifts[]"]').append(option);

				let values = $('select[name="working_shifts[]"]').select2('val');
				values.push(option.val());
				$('select[name="working_shifts[]"]').select2('val', values);

				// auto-close on save
				$(this).inspector('dismiss');
			});
		});
	})(jQuery);
</script>