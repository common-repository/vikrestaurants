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

$params = $this->params;

// fetch closing days
$closing_days = VikRestaurants::getClosingDays();

$cdLayout = new JLayoutFile('blocks.card');

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigClosingDaysDetails". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('ClosingDaysDetails');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<div class="vre-cards-container cards-closing-days" id="cards-closing-days">

			<?php
			foreach ($closing_days as $i => $day)
			{
				if ($day['freq'] == 1)
				{
					// weekly
					$day['label'] = JFactory::getDate(date('Y-m-d H:i:s', $day['ts']))->format('l');
				}
				else
				{
					// use translation
					$day['label'] = JText::translate('VRFREQUENCYTYPE' . $day['freq']);
				}
				?>
				<div class="vre-card-fieldset up-to-3" id="closing-day-fieldset-<?php echo (int) $i; ?>">

					<?php
					$displayData = [];

					// fetch primary text
					$displayData['primary'] = $day['date'];
				
					// fetch secondary text
					$displayData['secondary'] = '<span class="badge badge-info">' . $day['label'] . '</span>';

					// fetch edit button
					$displayData['edit'] = 'vreOpenClosingDayCard(\'' . $i . '\');';

					// render layout
					echo $cdLayout->render($displayData);
					?>
					
					<input type="hidden" name="cl_day_json[]" value="<?php echo $this->escape(json_encode($day)); ?>" />

				</div>
				<?php
			}
			?>

			<!-- ADD PLACEHOLDER -->

			<div class="vre-card-fieldset up-to-3 add add-closing-day">
				<div class="vre-card compress">
					<i class="fas fa-plus"></i>
				</div>
			</div>

		</div>

		<div style="display:none;" id="closing-day-struct">
	
			<?php
			// create closing day structure for new items
			$displayData = array();
			$displayData['class']     = '';
			$displayData['primary']   = '';
			$displayData['secondary'] = '';
			$displayData['edit']      = true;

			echo $cdLayout->render($displayData);
			?>

		</div>

	</div>
	
</div>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage closing days
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'cldays-inspector',
	array(
		'title'       => JText::translate('VRMANAGECONFIG21'),
		'closeButton' => true,
		'keyboard'    => true,
		'footer'      => $footer,
		'width'       => 400,
	),
	$this->loadTemplate('closingdays_modal')
);
?>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigClosingDaysDetails","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Closing Days > Closing Days tab.
 *
 * @since 1.9
 */
foreach ($forms as $formTitle => $formHtml)
{
	?>
	<div class="config-fieldset">
		
		<div class="config-fieldset-head">
			<h3><?php echo JText::translate($formTitle); ?></h3>
		</div>

		<div class="config-fieldset-body">
			<?php echo $formHtml; ?>
		</div>
		
	</div>
	<?php
}

JText::script('VRFREQUENCYTYPE0');
JText::script('VRFREQUENCYTYPE1');
JText::script('VRFREQUENCYTYPE2');
JText::script('VRFREQUENCYTYPE3');
?>

<script>
	(function($, w) {
		'use strict';

		let CL_DAYS_COUNT   = <?php echo count($closing_days); ?>;
		let SELECTED_CL_DAY = null;

		$(function() {
			// open inspector for new closing days
			$('.vre-card-fieldset.add-closing-day').on('click', () => {
				vreOpenClosingDayCard();
			});

			// fill the form before showing the inspector
			$('#cldays-inspector').on('inspector.show', () => {
				let json = [];

				// fetch JSON data
				if (SELECTED_CL_DAY) {
					const fieldset = $('#' + SELECTED_CL_DAY);

					json = fieldset.find('input[name="cl_day_json[]"]').val();

					try {
						json = JSON.parse(json);
					} catch (err) {
						json = {};
					}
				}

				if (json.date === undefined) {
					// creating new record, hide delete button
					$('#cldays-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#cldays-inspector [data-role="delete"]').show();
				}

				fillClosingDayForm(json);
			});

			// apply the changes
			$('#cldays-inspector').on('inspector.save', function() {
				// validate form
				if (!closingDaysValidator.validate()) {
					return false;
				}

				// get saved record
				let data = getClosingDayData();

				let fieldset;

				if (SELECTED_CL_DAY) {
					fieldset = $('#' + SELECTED_CL_DAY);
				} else {
					fieldset = vreAddClosingDayCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="cl_day_json[]"]').val(JSON.stringify(data));

				// refresh card details
				vreRefreshClosingDayCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			// delete the record
			$('#cldays-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_CL_DAY);

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});
		});

		w.vreOpenClosingDayCard = (index) => {
			if (typeof index !== 'undefined') {
				SELECTED_CL_DAY = 'closing-day-fieldset-' + index;
			} else {
				SELECTED_CL_DAY = null;
			}

			// open inspector
			vreOpenInspector('cldays-inspector');
		}

		const vreAddClosingDayCard = (data) => {
			let index = CL_DAYS_COUNT++;

			SELECTED_CL_DAY = 'closing-day-fieldset-' + index;

			let html = $('#closing-day-struct').clone().html();

			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset" id="closing-day-fieldset-' + index + '">' + html + '</div>'
			).insertBefore($('.vre-card-fieldset.add-closing-day').last());

			// get created fieldset
			let fieldset = $('#' + SELECTED_CL_DAY);

			fieldset.vrecard('edit', 'vreOpenClosingDayCard(' + index + ')');

			// create input to hold JSON data
			let input = $('<input type="hidden" name="cl_day_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshClosingDayCard = (elem, data) => {
			// update primary text
			elem.vrecard('primary', data.date);

			let lookup, label;

			if (data.freq == 1) {
				// create week days lookup
				lookup = <?php echo json_encode(JHtml::fetch('vikrestaurants.days')); ?>;
				// get selected day of the week
				label = lookup[new Date(data.ts).getDay()].text;
			} else {
				// create frequency lookup
				lookup = [
					Joomla.JText._('VRFREQUENCYTYPE0'),
					Joomla.JText._('VRFREQUENCYTYPE1'),
					Joomla.JText._('VRFREQUENCYTYPE2'),
					Joomla.JText._('VRFREQUENCYTYPE3'),
				];
				// get frequency label
				label = lookup[data.freq];
			}

			// append frequency label
			let secondary = jQuery('<span class="badge badge-info"></span>').text(label);

			// update secondary text
			elem.vrecard('secondary', secondary);
		}
	})(jQuery, window);
</script>