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

$hours   = JHtml::fetch('vikrestaurants.hours');
$minutes = JHtml::fetch('vikrestaurants.minutes', 5);

$timeFormat = VREFactory::getConfig()->get('timeformat');

?>

<div class="inspector-form" id="inspector-sd-shifts-form">

	<div class="inspector-fieldset">

		<!-- LABEL - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('vr-shift-label')
			->label(JText::translate('VRMANAGESHIFT6'))
			->description(JText::translate('VRMANAGESHIFT6_DESC'));
		?>

		<!-- FROM HOUR MIN - Form -->

		<?php
		// construct from hours dropdown
		$hoursSelect = $this->formFactory->createField()
			->type('select')
			->value(12)
			->id('vr-hourfrom-sel')
			->required(true)
			->hidden(true)
			->options($hours);

		// construct from minutes dropdown
		$minutesSelect = $this->formFactory->createField()
			->type('select')
			->id('vr-minfrom-sel')
			->required(true)
			->hidden(true)
			->options($minutes);

		echo $this->formFactory->createField()
			->label(JText::translate('VRMANAGESHIFT2'))
			->required(true)
			->render(function($data, $input) use ($hoursSelect, $minutesSelect) {
				?>
				<div class="multi-field">
					<?php echo $hoursSelect; ?>
					<?php echo $minutesSelect; ?>
				</div>
				<?php
			});
		?>

		<!-- TO HOUR MIN - Form -->

		<?php
		// construct to hours dropdown
		$hoursSelect = $this->formFactory->createField()
			->type('select')
			->value(23)
			->id('vr-hourto-sel')
			->required(true)
			->hidden(true)
			->options($hours);

		// construct to minutes dropdown
		$minutesSelect = $this->formFactory->createField()
			->type('select')
			->id('vr-minto-sel')
			->required(true)
			->hidden(true)
			->options($minutes);

		echo $this->formFactory->createField()
			->label(JText::translate('VRMANAGESHIFT3'))
			->required(true)
			->render(function($data, $input) use ($hoursSelect, $minutesSelect) {
				?>
				<div class="multi-field">
					<?php echo $hoursSelect; ?>
					<?php echo $minutesSelect; ?>
				</div>
				<?php
			});
		?>

	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		w.getWorkingShift = () => {
			const data = {};

			data.label = $('#vr-shift-label').val();

			const fromHour = parseInt($('#vr-hourfrom-sel').val());
			const fromMin  = parseInt($('#vr-minfrom-sel').val());

			data.from = fromHour * 60 + fromMin;

			const toHour = parseInt($('#vr-hourto-sel').val());
			const toMin  = parseInt($('#vr-minto-sel').val());

			data.to = toHour * 60 + toMin;

			data.name = getFormattedTime(fromHour, fromMin, '<?php echo $timeFormat; ?>') + ' - ' + getFormattedTime(toHour, toMin, '<?php echo $timeFormat; ?>');

			if (data.label) {
				data.name = data.label + ' (' + data.name + ')';
			}

			return data;
		}

		$(function() {
			w.workingShiftsValidator = new VikFormValidator('#inspector-sd-shifts-form');

			// register callback for times validation
			w.workingShiftsValidator.addCallback(() => {
				const fromHour = $('#vr-hourfrom-sel');
				const fromMin  = $('#vr-minfrom-sel');

				const toHour = $('#vr-hourto-sel');
				const toMin  = $('#vr-minto-sel');

				if (parseInt(fromHour.val()) * 60 + parseInt(fromMin.val()) > parseInt(toHour.val()) * 60 + parseInt(toMin.val())) {
					if (fromHour.val() != toHour.val()) {
						w.workingShiftsValidator.setInvalid(fromHour);
						w.workingShiftsValidator.setInvalid(toHour);
					} else {
						w.workingShiftsValidator.unsetInvalid(fromHour);
						w.workingShiftsValidator.unsetInvalid(toHour);
					}

					w.workingShiftsValidator.setInvalid(fromMin);
					w.workingShiftsValidator.setInvalid(toMin);

					return false;
				}

				w.workingShiftsValidator.unsetInvalid(fromHour);
				w.workingShiftsValidator.unsetInvalid(toHour);
				w.workingShiftsValidator.unsetInvalid(fromMin);
				w.workingShiftsValidator.unsetInvalid(toMin);

				return true;
			});

			$('#vr-hourfrom-sel, #vr-minfrom-sel, #vr-hourto-sel, #vr-minto-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 'auto',
			});
		});
	})(jQuery, window);
</script>