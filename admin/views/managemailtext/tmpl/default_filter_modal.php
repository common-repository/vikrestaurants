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

<div class="inspector-form" id="inspector-mailtext-filter-form">

	<div id="mailtext-filter-fieldset-add">
		<?php echo $this->loadTemplate('filter_modal_add'); ?>
	</div>

	<div id="mailtext-filter-fieldset-edit" style="display: none;">
		<?php echo $this->loadTemplate('filter_modal_edit'); ?>
	</div>

</div>

<script>
	(function($) {
		'use strict';

		let activeFilter;

		window.fillMailtextFilterForm = (data) => {
			if (typeof data === 'string') {
				data = {id: data};

				// show back button only in case of insert
				$('#mailtext-filter-inspector button[data-role="back"]').show();
			}

			if (typeof data.id === 'undefined') {
				// choose filter first
				$('#mailtext-filter-fieldset-edit').hide();
				$('#mailtext-filter-fieldset-add').show();

				let activeFilters = [];

				// fetch all the created filters
				$('#cards-mailtext-filters input[name="filter_json[]"]').each(function() {
					let filter = JSON.parse($(this).val());

					// register the filter ID
					activeFilters.push(filter.id);
				});

				// since the same filter cannot be used more than once, we should
				//  disable all those filters that have been already created
				$('#add-cards-mailtext-filters .vre-card-fieldset').each(function() {
					// obtain filter name from ID attribute ("filter-fieldset-{ID}")
					let id = $(this).attr('id').replace(/^filter-fieldset-/, '');

					// check whether the current filter has been already used
					let alreadyUsed = activeFilters.indexOf(id) !== -1;

					if (alreadyUsed) {
						$(this).hide();
					} else {
						$(this).show();
					}
				});

				$('#mailtext-filter-inspector button[data-role="save"]').prop('disabled', true);
				return;
			}

			// edit mode
			$('.inspector-filter-fieldset').hide();
			$('#inspector-filter-fieldset-' + data.id).show();

			$('#mailtext-filter-fieldset-add').hide();
			$('#mailtext-filter-fieldset-edit').show();

			$('#mailtext-filter-inspector button[data-role="save"]').prop('disabled', false);

			activeFilter = data.id;

			const fieldset = $('#inspector-filter-fieldset-' + data.id);

			window.conditionalTextFormSetter(data, window.conditionalTextFilters, fieldset);

			// freeze the form to discard any pending changes
            window.filterInspectorObserver.freeze();
		}

		window.getMailtextFilterData = () => {
			let data = {};

			// recover active filter
			data.id = activeFilter;

			const fieldset = $('#inspector-filter-fieldset-' + data.id);

			// fetch filter parameters
			data.options = window.conditionalTextFormGetter(data.id, window.conditionalTextFilters, fieldset);

			return data;
		}
	})(jQuery);
</script>