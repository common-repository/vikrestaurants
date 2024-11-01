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

<div class="inspector-form" id="inspector-tkarea-zipcode-form">

	<div class="inspector-fieldset">

		<!-- FROM - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('params_zipcodes_from')
			->label(JText::translate('VRTKZIPPLACEHOLDER1'))
			->control([
				'required' => true,
			]);
		?>

		<!-- TO - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('params_zipcodes_to')
			->label(JText::translate('VRTKZIPPLACEHOLDER2'))
			->control();
		?>

		<!-- PUBLISHED - Checkbox -->

		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->id('params_zipcodes_published')
			->label(JText::translate('VRMANAGETKMENU12'));
		?>

	</div>

</div>

<script>

	(function($, w) {
		'use strict';

		w.fillTkareaZIPCodeForm = (data) => {
			// update from
			$('#params_zipcodes_from').val(data.from || '');

			// update to
			$('#params_zipcodes_to').val(data.to || '');

			// update published
			if (data.published === undefined) {
				data.published = true;
			} else if (typeof data.published === 'string') {
				data.published = parseInt(data.published);
			}

			$('#params_zipcodes_published').prop('checked', data.published);
		}

		w.getTkareaZIPCodeData = () => {
			let data = {};

			// set from
			data.from = $('#params_zipcodes_from').val();

			// set to
			data.to = $('#params_zipcodes_to').val();

			// set published
			data.published = $('#params_zipcodes_published').is(':checked') ? 1 : 0;

			return data;
		}
	})(jQuery, window);
</script>