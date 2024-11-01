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

?>

<div class="inspector-form" id="inspector-clday-form">

	<div class="inspector-fieldset">

		<!-- DATE -->

		<?php
		echo $this->formFactory->createField([
			'type'     => 'date',
			'name'     => 'cl_day_ts',
			'id'       => 'cl_day_ts',
			'label'    => JText::translate('VRRESERVATIONDATEFILTER'),
			'required' => true,
		]);
		?>

		<!-- RECURRENCE -->

		<?php
		echo $this->formFactory->createField([
			'type'    => 'select',
			'id'      => 'cl_day_freq',
			'label'   => JText::translate('VRERECURRENCE'),
			'options' => [
				// single day
				JHtml::fetch('select.option', 0, JText::translate('VRFREQUENCYTYPE0')),
				// weekly
				JHtml::fetch('select.option', 1, JText::translate('VRFREQUENCYTYPE1')),
				// monthly
				JHtml::fetch('select.option', 2, JText::translate('VRFREQUENCYTYPE2')),
				// yearly
				JHtml::fetch('select.option', 3, JText::translate('VRFREQUENCYTYPE3')),
			],
		]);
		?>

	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		w.fillClosingDayForm = (data) => {
			// update date
			if (data.date !== undefined) {
				// update data-alt-value too for MooTools compliance
				$('#cl_day_ts').val(data.date).attr('data-alt-value', data.date);
			}

			w.closingDaysValidator.unsetInvalid($('#cl_day_ts'));

			// update frequency
			if (data.freq !== undefined) {
				$('#cl_day_freq').select2('val', data.freq);
			}

			// update services
			if (data.services !== undefined) {
				$('#cl_day_services').select2('val', data.services);
			}
		}

		w.getClosingDayData = () => {
			let data = {};

			// set formatted date
			data.date = $('#cl_day_ts').val();

			// obtain date in military format (false to avoid instantiating a date object)
			data.ts = getDateFromFormat(data.date, '<?php echo $this->params['dateformat']; ?>', false);

			// set frequency
			data.freq = $('#cl_day_freq').val();

			return data;
		}

		$(function() {
			w.closingDaysValidator = new VikFormValidator('#inspector-clday-form');

			$('#cl_day_freq').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '100%',
			});
		});
	})(jQuery, window);
</script>