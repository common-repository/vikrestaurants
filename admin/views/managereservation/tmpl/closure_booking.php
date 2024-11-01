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

JHtml::fetch('vrehtml.scripts.updateshifts', 1, '_vrUpdateWorkingShifts');

$reservation = $this->reservation;

?>

<!-- CHECK-IN DATE - Date -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('date')
	->id('vrdatefilter')
	->value($reservation->date)
	->required(true)
	->label(JText::translate('VRMANAGERESERVATION13'))
	->attributes([
		'onChange' => 'vrUpdateWorkingShifts();',
	]);
?>

<!-- CHECK-IN TIME - Select -->

<?php
echo $this->formFactory->createField()
	->name('hourmin')
	->id('vr-hour-sel')
	->value($reservation->hourmin)
	->required(true)
	->group('restaurant')
	->day($reservation->date)
	->label(JText::translate('VRMANAGERESERVATION14'))
	->render(new E4J\VikRestaurants\Form\Renderers\CheckinTimeFieldRenderer);
?>

<!-- STAY TIME - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('stay_time')
	->id('vr-stay-time')
	->value($reservation->stay_time)
	->label(JText::translate('VRMANAGERESERVATION25'))
	->description(JText::translate('VRMANAGERESERVATION25_DESC'))
	->min(15)
	->step(1)
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
?>

<!-- RE-OPEN - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('reopen')
	->label(JText::translate('VRMANAGERESERVATION26'))
	->description(JText::translate('VRMANAGERESERVATION26_HELP'))
	->onchange('reopenValueChanged(this.checked)');
?>

<input type="hidden" id="vr-people-sel" value="<?php echo (int) $this->reservation->people; ?>" />

<script>
	(function($, w) {
		'use strict';

		const enableAvailableTables = (rooms) => {
			// turn off the click event to avoid registering multiple callbacks
			// $(document).off('click', 'g.table-graphic');
			
			for (let roomId in rooms) {
				if (!rooms.hasOwnProperty(roomId)) {
					continue;
				}

				$('#vre-map-svg-wrapper' + roomId).html(rooms[roomId]);
			}
		}

		w.reopenValueChanged = (checked) => {
			// enable/disable other options depending on the "RE-OPEN" checkbox status
			$('input,select')
				.not('input[type="hidden"]')
				.not('input[name="reopen"]')
				.prop('disabled', checked ? true : false);
		}

		w.vrUpdateWorkingShifts = () => {
			// making an AJAX request
			w.IS_AJAX_CALLING = true;

			_vrUpdateWorkingShifts(
				'#vrdatefilter',
				'#vr-hour-sel',
				(resp) => {
					// refresh available tables and menus on request complete
					vrUpdateAvailableTables();
				},
				(error) => {
					// refresh available tables and menus on request failed
					vrUpdateAvailableTables();
				}
			);
		}

		w.vrUpdateAvailableTables = () => {
			$('#vr-table-sel').prop('disabled', true);

			w.IS_AJAX_CALLING = true;

			// disable table selection until the map is refreshed
			w.enableTableSelection(false);

			new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=reservation.availabletablesajax'); ?>',
					{
						date:     $('#vrdatefilter').val(),
						hourmin:  $('#vr-hour-sel').val(),
						people:   $('#vr-people-sel').val(),
						staytime: $('#vr-stay-time').val(),
						id_res:   <?php echo (int) $reservation->id; ?>,
						options:  w.MAP_OPTIONS,
					},
					(data) => {
						resolve(data);
					},
					(error) => {
						reject(error);
					}
				);
			}).then((rooms) => {
				enableAvailableTables(rooms);
			}).catch((error) => {
				// prompt received error message
				alert(error.responseText || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));
			}).finally(() => {
				// request has finished
				w.IS_AJAX_CALLING = false;

				// re-enable table selection
				w.enableTableSelection(true);
			});
		}

		$(function() {
			$('#vr-hour-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '90%',
			});

			// update available tables whenever something relevant changes
			$('#vr-hour-sel').on('change', () => {
				vrUpdateAvailableTables();
			});
		})
	})(jQuery, window);
</script>