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

<div class="inspector-form" id="inspector-tkarea-city-form">

	<div class="inspector-fieldset">

		<!-- NAME - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('params_cities_name')
			->label(JText::translate('VRMANAGECUSTOMER7'))
			->control([
				'required' => true,
			]);
		?>

		<!-- PUBLISHED - Checkbox -->

		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->id('params_cities_published')
			->label(JText::translate('VRMANAGETKMENU12'));
		?>

	</div>

</div>

<script>

	(function($, w) {
		'use strict';

		w.fillTkareaCityForm = (data) => {
			// update name
			$('#params_cities_name').val(data.name || '');

			// update published
			if (data.published === undefined) {
				data.published = true;
			} else if (typeof data.published === 'string') {
				data.published = parseInt(data.published);
			}

			$('#params_cities_published').prop('checked', data.published);
		}

		w.getTkareaCityData = () => {
			let data = {};

			// set name
			data.name = $('#params_cities_name').val();

			// set published
			data.published = $('#params_cities_published').is(':checked') ? 1 : 0;

			return data;
		}
	})(jQuery, window);
</script>