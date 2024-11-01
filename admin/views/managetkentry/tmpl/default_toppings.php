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

$vik = VREApplication::getInstance();

$groupLayout = new JLayoutFile('blocks.card');

?>
	
<div class="vre-cards-container cards-tkentry-groups" id="cards-tkentry-groups">

	<?php
	foreach ($this->entry->groups as $i => $group)
	{
		?>
		<div class="vre-card-fieldset up-to-3" id="tkentry-group-fieldset-<?php echo (int) $i; ?>">

			<?php
			$displayData = array();

			// fetch card ID
			$displayData['id'] = 'tkentry-group-card-' . $i;

			// fetch badge
			$displayData['badge'] = '<i class="fas fa-' . ($group->multiple ? 'check-circle' : 'dot-circle') . '"></i>';

			// fetch primary text
			$displayData['primary']  = $group->title;

			// fetch secondary text
			$text = JText::plural('VRE_N_TOPPINGS', count($group->toppings));
			$displayData['secondary'] = '<span class="badge badge-' . (count($group->toppings) ? 'info' : 'important') . ' group-toppings">' . $text . '</span>';

			if ($group->id_variation)
			{
				// search selected variation
				for ($j = 0, $varname = null; $j < count($this->entry->options) && !$varname; $j++)
				{
					if ($this->entry->options[$j]->id == $group->id_variation)
					{
						$varname = $this->entry->options[$j]->name;
					}
				}

				if ($varname)
				{
					// display variation badge
					$displayData['secondary'] .= '<span class="badge badge-warning group-variation">' . $varname . '</span>';
				}
				else
				{
					// display variation badge (not found)
					$displayData['secondary'] .= '<span class="badge badge-important group-variation">' . JText::translate('JGLOBAL_NO_MATCHING_RESULTS') . '</span>';
				}
			}

			// fetch edit button
			$displayData['edit'] = 'vreOpenTkentryGroupCard(' . $i . ');';

			// render layout
			echo $groupLayout->render($displayData);
			?>
			
			<input type="hidden" name="group_json[]" value="<?php echo $this->escape(json_encode($group)); ?>" />

		</div>
		<?php
	}

	/**
	 * Preserve the deleted groups in case of failure.
	 * 
	 * @since 1.9
	 */
	if (!empty($this->entry->deleted_groups))
	{
		foreach ($this->entry->deleted_groups as $deleted)
		{
			?><input type="hidden" name="group_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}
	?>

	<div class="vre-card-fieldset up-to-3 add add-tkentry-group">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="tkentry-group-struct">
	
	<?php
	// create entry group structure for new items
	$displayData = array();
	$displayData['id']        = 'tkentry-group-card-{id}';
	$displayData['badge']     = '<i class="fas fa-check-circle"></i>';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';
	$displayData['edit']      = true;

	echo $groupLayout->render($displayData);
	?>

</div>

<?php
JText::script('VRSYSTEMCONFIRMATIONMSG');
JText::script('VRE_ADD_TOPPING_GROUP');
JText::script('VRE_EDIT_TOPPING_GROUP');
JText::script('VRE_N_TOPPINGS_0');
JText::script('VRE_N_TOPPINGS_1');
JText::script('VRE_N_TOPPINGS');
?>

<script>
	(function($, w) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo count($this->entry->groups); ?>;
		let SELECTED_OPTION = null;

		$(function() {
			// open inspector for new toppings groups
			$('.vre-card-fieldset.add-tkentry-group').on('click', () => {
				vreOpenTkentryGroupCard();
			});

			$('#cards-tkentry-groups').sortable({
				// exclude "add" box
				items: '.vre-card-fieldset:not(.add)',
				// hide "add" box when sorting starts
				start: function() {
					$('.vre-card-fieldset.add-tkentry-group').hide();
				},
				// show "add" box again when sorting stops
				stop: function() {
					$('.vre-card-fieldset.add-tkentry-group').show();
				},
			});

			// fill the form before showing the inspector
			$('#tkentry-group-inspector').on('inspector.show', function() {
				let data = [];

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					data = fieldset.find('input[name="group_json[]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				if (data.id === undefined) {
					// creating new record, hide delete button
					$('#tkentry-group-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#tkentry-group-inspector [data-role="delete"]').show();
				}

				// fill the form with the retrieved data
				fillTkentryGroupForm(data);
			});

			$('#tkentry-group-inspector').on('inspector.save', function() {
				// validate form
				if (!groupValidator.validate()) {
					return false;
				}

				// get updated toppings group data
				const data = getTkentryGroupData();

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddTkentryGroupCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="group_json[]"]').val(JSON.stringify(data));

				// refresh details shown in card
				vreRefreshTkentryGroupCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			$('#tkentry-group-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="group_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="group_deleted[]" value="' + json.id + '" />');
				}

				// auto delete fieldset
				fieldset.remove();

				// NOTE: do not need to delete toppings too because they will be removed
				// in cascade while erasing the parent entry group.

				// auto-close on delete
				$(this).inspector('dismiss');
			});

			/**
			 * Handle inspector hide.
			 *
			 * We need to bind the event by using a handler in order to have a lower priority,
			 * since the hook used to observe any form changes may be attached after this one.
			 */
			$(document).on('inspector.close', '#tkentry-group-inspector', () => {
				if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
					// reset editor after closing the inspector
					const editor = Joomla.editors.instances.group_description;
					
					editor.setValue('');

					if (editor.onSave) {
						editor.onSave();
					}

					// flag TinyMCE editor as clean because every time we edit
					// something and we close the inspector, the editor might
					// prompt an alert saying if we wish to stay or leave
					if (editor.instance && editor.instance.isNotDirty === false) {
						editor.instance.isNotDirty = true;
					}
				}
			});
		});

		w.vreOpenTkentryGroupCard = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRE_ADD_TOPPING_GROUP');
				SELECTED_OPTION = null;
			} else {
				title = Joomla.JText._('VRE_EDIT_TOPPING_GROUP');
				SELECTED_OPTION = 'tkentry-group-fieldset-' + index;
			}
			
			// open inspector
			vreOpenInspector('tkentry-group-inspector', {title: title});
		}

		const vreAddTkentryGroupCard = (data) => {
			let index = OPTIONS_COUNT++;

			SELECTED_OPTION = 'tkentry-group-fieldset-' + index;

			let html = $('#tkentry-group-struct').clone().html();
			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-3" id="' + SELECTED_OPTION + '">' + html + '</div>'
			).insertBefore('.vre-card-fieldset.add-tkentry-group');

			// get created fieldset
			const fieldset = $('#' + SELECTED_OPTION);

			fieldset.vrecard('edit', 'vreOpenTkentryGroupCard(' + index + ')');

			// create input to hold JSON data
			const input = $('<input type="hidden" name="group_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshTkentryGroupCard = (elem, data) => {
			// update primary text
			elem.vrecard('primary', data.title);

			let toppingsCount;

			if (data.toppings.length == 1) {
				toppingsCount = Joomla.JText._('VRE_N_TOPPINGS_1');
			} else if (data.toppings.length > 1) {
				toppingsCount = Joomla.JText._('VRE_N_TOPPINGS').replace(/%d/, data.toppings.length);
			} else {
				toppingsCount = Joomla.JText._('VRE_N_TOPPINGS_0');
			}

			// display total number of selected toppings
			let secondary = $('<span class="badge group-toppings"></span>')
				.addClass(data.toppings.length ? 'badge-info' : 'badge-important')
				.html(toppingsCount);

			// in case of selected variation, display it
			if (parseInt(data.id_variation)) {
				let varname = null;
				const variations = <?php echo json_encode($this->entry->options); ?>;

				for (let i = 0; i < variations.length && !varname; i++) {
					if (variations[i].id == data.id_variation) {
						varname = variations[i].name;
					}
				}

				if (varname) {
					secondary = secondary.add($('<span class="badge badge-warning group-variation"></span>').text(varname));
				}
			}

			// update secondary text
			elem.vrecard('secondary', secondary);

			if (data.published == 1) {
				elem.find('.vre-card').addClass('published');
			} else {
				elem.find('.vre-card').removeClass('published');
			}

			// update toppings group badge
			elem.vrecard('badge', '<i class="fas ' + (data.multiple == 1 ? 'fa-check-circle' : 'fa-dot-circle') + '"></i>');
		}
	})(jQuery, window);
</script>