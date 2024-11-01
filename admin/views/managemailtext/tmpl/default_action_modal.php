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

<div class="inspector-form" id="inspector-mailtext-action-form">

	<div id="mailtext-action-fieldset-add">
		<?php echo $this->loadTemplate('action_modal_add'); ?>
	</div>

	<div id="mailtext-action-fieldset-edit" style="display: none;">
		<?php echo $this->loadTemplate('action_modal_edit'); ?>
	</div>

</div>

<script>
	(function($) {
		'use strict';

		let activeAction;

		window.fillMailtextActionForm = (data) => {
			if (typeof data === 'string') {
				data = {id: data};

				// show back button only in case of insert
				$('#mailtext-action-inspector button[data-role="back"]').show();
			}

			if (typeof data.id === 'undefined') {
				// choose action first
				$('#mailtext-action-fieldset-edit').hide();
				$('#mailtext-action-fieldset-add').show();

				$('#mailtext-action-inspector button[data-role="save"]').prop('disabled', true);
				return;
			}

			// edit mode
			$('.inspector-action-fieldset').hide();
			$('#inspector-action-fieldset-' + data.id).show();

			$('#mailtext-action-fieldset-add').hide();
			$('#mailtext-action-fieldset-edit').show();

			$('#mailtext-action-inspector button[data-role="save"]').prop('disabled', false);

			activeAction = data.id;

			const fieldset = $('#inspector-action-fieldset-' + data.id);

			window.conditionalTextFormSetter(data, window.conditionalTextActions, fieldset);

			// freeze the form to discard any pending changes
            window.actionInspectorObserver.freeze();
		}

		window.getMailtextActionData = () => {
			let data = {};

			// recover active action
			data.id = activeAction;

			const fieldset = $('#inspector-action-fieldset-' + data.id);

			// fetch action parameters
			data.options = window.conditionalTextFormGetter(data.id, window.conditionalTextActions, fieldset);

			return data;
		}
	})(jQuery);
</script>