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

$currency = VREFactory::getCurrency();

$sectionLayout = new JLayoutFile('blocks.card');

?>
			
<div class="vre-cards-container cards-menu-sections" id="cards-menu-sections">

	<?php
	foreach ($this->menu->sections as $i => $section)
	{
		?>
		<div class="vre-card-fieldset up-to-3" id="menu-section-fieldset-<?php echo (int) $i; ?>">

			<?php
			$displayData = array();

			// fetch card ID
			$displayData['id'] = 'menu-section-card-' . $i;

			// fetch section image
			$displayData['image'] = $section->image ? VREMEDIA_URI . $section->image : VREASSETS_ADMIN_URI . 'images/product-placeholder.png';

			// fetch primary text
			$displayData['primary']  = $section->name;

			// fetch secondary text
			$displayData['secondary'] = '<span class="badge badge-' . (count($section->products) ? 'success' : 'important') . ' products-count">'
				. JText::plural('VRE_N_PRODUCTS', count($section->products))
				. '</span>';

			// fetch edit button
			$displayData['edit'] = 'vreOpenMenuSectionCard(' . $i . ');';

			// render layout
			echo $sectionLayout->render($displayData);
			?>
			
			<input type="hidden" name="section_json[]" value="<?php echo $this->escape(json_encode($section)); ?>" />

		</div>
		<?php
	}

	/**
	 * Preserve the deleted sections and products in case of failure.
	 * 
	 * @since 1.9
	 */
	if (!empty($this->menu->deleted_sections))
	{
		foreach ($this->menu->deleted_sections as $deleted)
		{
			?><input type="hidden" name="section_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}

	if (!empty($this->menu->deleted_products))
	{
		foreach ($this->menu->deleted_products as $deleted)
		{
			?><input type="hidden" name="product_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}
	?>

	<!-- ADD PLACEHOLDER -->

	<div class="vre-card-fieldset up-to-3 add add-menu-section">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="menu-section-struct">
	
	<?php
	// create menu section structure for new items
	$displayData = array();
	$displayData['id']        = 'menu-section-card-{id}';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';
	$displayData['edit']      = true;

	echo $sectionLayout->render($displayData);
	?>

</div>

<?php
JText::script('VRE_ADD_SECTION');
JText::script('VRE_EDIT_SECTION');
JText::script('VRE_N_PRODUCTS');
JText::script('VRE_N_PRODUCTS_1');
JText::script('VRE_N_PRODUCTS_0');
?>

<script>
	(function($, w) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo count($this->menu->sections); ?>;
		let SELECTED_OPTION = null;

		$(function() {
			// open inspector for new sections
			$('.vre-card-fieldset.add-menu-section').on('click', () => {
				vreOpenMenuSectionCard();
			});

			$('#cards-menu-sections').sortable({
				// exclude "add" box
				items: '.vre-card-fieldset:not(.add)',
				// hide "add" box when sorting starts
				start: function() {
					$('.vre-card-fieldset.add-menu-section').hide();
				},
				// show "add" box again when sorting stops
				stop: function() {
					$('.vre-card-fieldset.add-menu-section').show();
				},
			});

			// fill the form before showing the inspector
			$('#menu-section-inspector').on('inspector.show', function() {
				let data = [];

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					data = fieldset.find('input[name="section_json[]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				if (data.id === undefined) {
					// creating new record, hide delete button
					$('#menu-section-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#menu-section-inspector [data-role="delete"]').show();
				}

				// fill the form with the retrieved data
				fillMenuSectionForm(data);
			});

			$('#menu-section-inspector').on('inspector.save', function() {
				// validate form
				if (!sectionValidator.validate()) {
					return false;
				}

				// get updated menu section data
				const data = getMenuSectionData();

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddMenuSectionCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="section_json[]"]').val(JSON.stringify(data));

				// get all deleted products
				getDeletedSectionProducts().forEach((id) => {
					fieldset.append('<input type="hidden" name="product_deleted[]" value="' + id + '" />');
				});

				// refresh details shown in card
				vreRefreshMenuSectionCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			$('#menu-section-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="section_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="section_deleted[]" value="' + json.id + '" />');
				}

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});

			/**
			 * Handle inspector hide.
			 *
			 * We need to bind the event by using a handler in order to have a lower priority,
			 * since the hook used to observe any form changes may be attached after this one.
			 */
			$(document).on('inspector.close', '#menu-section-inspector', () => {
				if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
					// reset editor after closing the inspector
					const editor = Joomla.editors.instances.section_description;
					
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

		w['vreOpenMenuSectionCard'] = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRE_ADD_SECTION');
				SELECTED_OPTION = null;
			} else {
				title = Joomla.JText._('VRE_EDIT_SECTION');
				SELECTED_OPTION = 'menu-section-fieldset-' + index;
			}
			
			// open inspector
			vreOpenInspector('menu-section-inspector', {title: title});
		}

		const vreAddMenuSectionCard = (data) => {
			let index = OPTIONS_COUNT++;

			SELECTED_OPTION = 'menu-section-fieldset-' + index;

			let html = $('#menu-section-struct').clone().html();
			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-3" id="' + SELECTED_OPTION + '">' + html + '</div>'
			).insertBefore('.vre-card-fieldset.add-menu-section');

			// get created fieldset
			const fieldset = $('#' + SELECTED_OPTION);

			fieldset.vrecard('edit', 'vreOpenMenuSectionCard(' + index + ')');

			// create input to hold JSON data
			const input = $('<input type="hidden" name="section_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshMenuSectionCard = (elem, data) => {
			// update image
			elem.vrecard('image', data.image ? '<?php echo VREMEDIA_URI; ?>' + data.image : '<?php echo VREASSETS_ADMIN_URI; ?>images/product-placeholder.png');

			// update primary text
			elem.vrecard('primary', data.name);

			// update secondary text
			let secondary, badge = 'badge-success';

			if (data.products.length > 1) {
				secondary = Joomla.JText._('VRE_N_PRODUCTS').replace(/%d/, data.products.length);
			} else if (data.products.length == 1) {
				secondary = Joomla.JText._('VRE_N_PRODUCTS_1');
			} else {
				secondary = Joomla.JText._('VRE_N_PRODUCTS_0');
				badge     = 'badge-important';
			}

			elem.vrecard('secondary', $('<span class="badge products-count"></span').addClass(badge).html(secondary));
		}
	})(jQuery, window);
</script>
