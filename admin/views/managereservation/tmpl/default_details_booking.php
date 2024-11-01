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
$timeSelect = $this->formFactory->createField()
	->name('hourmin')
	->id('vr-hour-sel')
	->value($reservation->hourmin)
	->required(true)
	->group('restaurant')
	->day($reservation->date)
	->hidden(true)
	->render(new E4J\VikRestaurants\Form\Renderers\CheckinTimeFieldRenderer);

$busyTimeButton = $this->formFactory->createField()
	->type('button')
	->text('<i class="fas fa-calendar-alt"></i>')
	->hidden(true)
	->onclick('vrOpenBusyModal()');

$stayTimeButton = $this->formFactory->createField()
	->type('button')
	->text('<i class="fas fa-chevron-down"></i>')
	->hidden(true)
	->onclick('vrToggleStayTime(this)');

echo $this->formFactory->createField()
	->label(JText::translate('VRMANAGERESERVATION14'))
	->control(['required' => true])
	->render(function($data) use ($timeSelect, $busyTimeButton, $stayTimeButton) {
		?>
		<div class="multi-field">
			<?php echo $timeSelect; ?>

			<div class="btn-group flex-auto">
				<?php echo $busyTimeButton; ?>
				<?php echo $stayTimeButton; ?>
			</div>
		</div>
		<?php
	});
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
	->control([
		'class'   => 'staytime-child',
		'visible' => false,
	])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
?>

<!-- PEOPLE - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('people')
	->id('vr-people-sel')
	->value($reservation->people)
	->required(true)
	->label(JText::translate('VRMANAGERESERVATION4'))
	->options(JHtml::fetch('vikrestaurants.people'));
?>

<script>
	(function($, w) {
		'use strict';

		const avgStayTime     = <?php echo VREFactory::getConfig()->getUint('averagetimestay'); ?>;
		const currentStayTime = <?php echo (int) $reservation->stay_time; ?>;

		const openModal = (id, url, jqmodal) => {
			<?php echo VREApplication::getInstance()->bootOpenModalJS(); ?>
		}

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

		w.vrUpdateWorkingShifts = () => {
			// making an AJAX request
			w.IS_AJAX_CALLING = true;

			_vrUpdateWorkingShifts(
				'#vrdatefilter',
				'#vr-hour-sel',
				(resp) => {
					// refresh available tables and menus on request complete
					vrUpdateAvailableTables();
					vrUpdateAvailableMenus();
				},
				(error) => {
					// refresh available tables and menus on request failed
					vrUpdateAvailableTables();
					vrUpdateAvailableMenus();
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

		w.vrUpdateAvailableMenus = () => {
			w.IS_AJAX_CALLING = true;

			new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=reservation.availablemenusajax'); ?>',
					{
						date:    $('#vrdatefilter').val(),
						hourmin: $('#vr-hour-sel').val(),
					},
					(data) => {
						resolve(data);
					},
					(error) => {
						reject(error);
					}
				);
			}).then((menus) => {
				setupAvailableMenus(menus);

				if (menus.length) {
					$('#menus-fieldset').show();
				} else {
					$('#menus-fieldset').hide();
				}
			}).catch((error) => {
				// prompt received error message
				alert(error.responseText || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));
			}).finally(() => {
				// request has finished
				w.IS_AJAX_CALLING = false;
			});
		}

		w.vrOpenBusyModal = () => {
			let url = 'index.php?option=com_vikrestaurants&view=restbusyres&tmpl=component&date=' + $('#vrdatefilter').val() + '&time=' + $('#vr-hour-sel').val();
			openModal('busytime', url, true);
		}

		w.vrToggleStayTime = (btn) => {
			const icon = $(btn).find('i');
			let val = parseInt($('input[name="stay_time"]').val());

			if (icon.hasClass('fa-chevron-down')) {
				icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');

				$('.staytime-child').show();
				
				if (val == 0) {
					// set default time of stay
					$('input[name="stay_time"]').val(avgStayTime);
				}
			} else {
				icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');

				$('.staytime-child').hide();

				if (val === avgStayTime && currentStayTime === 0) {
					// revert to 0 in order to keep the stay time dynamic
					$('input[name="stay_time"]').val(0);
				}
			}
		}

		$(function() {
			$('#vr-hour-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '90%',
			});

			$('#vr-people-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '90%',
			});

			// update available tables whenever something relevant changes
			$('#vr-hour-sel, #vr-people-sel, #vr-stay-time').on('change', () => {
				vrUpdateAvailableTables();
			});

			// update available menus whenever something relevant changes
			$('#vr-hour-sel').on('change', () => {
				vrUpdateAvailableMenus();
			});
		})
	})(jQuery, window);
</script>