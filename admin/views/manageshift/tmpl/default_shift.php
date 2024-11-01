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

$shift = $this->shift;

$hours   = JHtml::fetch('vikrestaurants.hours');
$minutes = JHtml::fetch('vikrestaurants.minutes', 5);

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($shift->name)
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGESHIFT1'));
?>

<!-- DISPLAY LABEL - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('showlabel')
	->checked($shift->showlabel)
	->label(JText::translate('VRMANAGESHIFT5'))
	->onchange('showLabelValueChanged(this.checked)');
?>

<!-- LABEL - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('label')
	->value($shift->label)
	->label(JText::translate('VRMANAGESHIFT6'))
	->control([
		'class' => 'vr-showlabel-child',
		'style' => $shift->showlabel ? '' : 'display: none;',
	]);
?>

<!-- FROM HOUR MIN - Form -->

<?php
$from = JHtml::fetch('vikrestaurants.min2time', $shift->from, false);

// construct from hours dropdown
$hoursSelect = $this->formFactory->createField()
	->type('select')
	->name('from')
	->value($from->hour)
	->id('vr-hourfrom-sel')
	->required(true)
	->hidden(true)
	->options($hours);

// construct from minutes dropdown
$minutesSelect = $this->formFactory->createField()
	->type('select')
	->name('minfrom')
	->value($from->min)
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
$to = JHtml::fetch('vikrestaurants.min2time', $shift->to, false);

// construct to hours dropdown
$hoursSelect = $this->formFactory->createField()
	->type('select')
	->name('to')
	->value($to->hour)
	->id('vr-hourto-sel')
	->required(true)
	->hidden(true)
	->options($hours);

// construct to minutes dropdown
$minutesSelect = $this->formFactory->createField()
	->type('select')
	->name('minto')
	->value($to->min)
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

<!-- GROUP - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('group')
	->value($shift->group)
	->required(true)
	->label(JText::translate('VRMANAGESHIFT4'))
	->options(JHtml::fetch('vrehtml.admin.groups', [1, 2]));
?>

<script>
	(function($, w) {
		'use strict';

		w.showLabelValueChanged = (is) => {
			if (is) {
				$('.vr-showlabel-child').show();
			} else {
				$('.vr-showlabel-child').hide();
			}
		}

		$(function() {
			$('#vr-hourfrom-sel, #vr-minfrom-sel, #vr-hourto-sel, #vr-minto-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 'auto',
			});

			$('select[name="group"]').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			// Observe validator instance and wait until it is ready.
			// Workaround needed to avoid the issue that occurs on WordPress.
			onInstanceReady(() => {
				if (typeof w.validator === 'undefined') {
					return false;
				}

				return w.validator;
			}).then((validator) => {
				// register callback for times validation
				validator.addCallback((form) => {
					const fromHour = $('#vr-hourfrom-sel');
					const fromMin  = $('#vr-minfrom-sel');

					const toHour = $('#vr-hourto-sel');
					const toMin  = $('#vr-minto-sel');

					if (parseInt(fromHour.val()) * 60 + parseInt(fromMin.val()) > parseInt(toHour.val()) * 60 + parseInt(toMin.val())) {
						if (fromHour.val() != toHour.val()) {
							validator.setInvalid(fromHour);
							validator.setInvalid(toHour);
						} else {
							validator.unsetInvalid(fromHour);
							validator.unsetInvalid(toHour);
						}

						validator.setInvalid(fromMin);
						validator.setInvalid(toMin);

						return false;
					}

					validator.unsetInvalid(fromHour);
					validator.unsetInvalid(toHour);
					validator.unsetInvalid(fromMin);
					validator.unsetInvalid(toMin);

					return true;
				});
			});
		});
	})(jQuery, window);
</script>