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

$productLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-cards-container cards-tkmenu-products" id="cards-tkmenu-products">

	<?php
	foreach ($this->menu->products as $i => $product)
	{
		?>
		<div class="vre-card-fieldset up-to-3" id="tkmenu-product-fieldset-<?php echo (int) $i; ?>">

			<?php
			$displayData = array();

			// fetch card ID
			$displayData['id'] = 'tkmenu-prod-card-' . $i;

			// fetch card class
			if ($product->published)
			{
				$displayData['class'] = 'published';
			}

			// fetch product image
			$displayData['image'] = $product->img_path ? VREMEDIA_URI . $product->img_path : VREASSETS_ADMIN_URI . 'images/product-placeholder.png';

			// fetch badge
			$displayData['badge'] = '<i class="' . ($product->ready ? 'far fa-snowflake' : 'fas fa-stopwatch') . '"></i>';

			// fetch primary text
			$displayData['primary']  = $product->name;

			// fetch secondary text
			$displayData['secondary'] = '<span class="badge badge-info product-cost">' . $currency->format($product->price) . '</span>';

			if ($product->options)
			{
				$displayData['secondary'] .= '<span class="badge badge-success product-vars">' . JText::plural('VRE_N_VARIATIONS', count($product->options)) . '</span>';
			}

			// fetch edit button
			$displayData['edit'] = 'vreOpenTkmenuProductCard(' . $i . ');';

			// render layout
			echo $productLayout->render($displayData);
			?>
			
			<input type="hidden" name="product_json[]" value="<?php echo $this->escape(json_encode($product)); ?>" />

		</div>
		<?php
	}

	/**
	 * Preserve the deleted products and variations in case of failure.
	 * 
	 * @since 1.9
	 */
	if (!empty($this->menu->deleted_products))
	{
		foreach ($this->menu->deleted_products as $deleted)
		{
			?><input type="hidden" name="product_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}

	if (!empty($this->menu->deleted_options))
	{
		foreach ($this->menu->deleted_options as $deleted)
		{
			?><input type="hidden" name="option_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}
	?>

	<div class="vre-card-fieldset up-to-3 add add-tkmenu-product">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="tkmenu-product-struct">
	
	<?php
	// create menu entry structure for new items
	$displayData = [];
	$displayData['id']        = 'tkmenu-prod-card-{id}';
	$displayData['image']     = VREASSETS_ADMIN_URI . 'images/product-placeholder.png';
	$displayData['badge']     = '<i class="fas fa-stopwatch"></i>';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';
	$displayData['edit']      = true;

	echo $productLayout->render($displayData);
	?>

</div>

<?php
JText::script('VRSYSTEMCONFIRMATIONMSG');
JText::script('VRE_ADD_PRODUCT');
JText::script('VRE_EDIT_PRODUCT');
JText::script('VRMANAGETKENTRYFIELDSET2');
JText::script('VRE_N_VARIATIONS');
JText::script('VRE_N_VARIATIONS_1');
?>

<script>
	(function($, w) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo count($this->menu->products); ?>;
		let SELECTED_OPTION = null;

		$(function() {
			// open inspector for new products
			$('.vre-card-fieldset.add-tkmenu-product').on('click', () => {
				vreOpenTkmenuProductCard();
			});

			$('#cards-tkmenu-products').sortable({
				// exclude "add" box
				items: '.vre-card-fieldset:not(.add)',
				// hide "add" box when sorting starts
				start: function() {
					$('.vre-card-fieldset.add-tkmenu-product').hide();
				},
				// show "add" box again when sorting stops
				stop: function() {
					$('.vre-card-fieldset.add-tkmenu-product').show();
				},
			});

			// fill the form before showing the inspector
			$('#tkmenu-product-inspector').on('inspector.show', function() {
				let data = [];

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					data = fieldset.find('input[name="product_json[]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				if (data.id === undefined) {
					// creating new record, hide delete button
					$('#tkmenu-product-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#tkmenu-product-inspector [data-role="delete"]').show();
				}

				// fill the form with the retrieved data
				fillTkmenuProductForm(data);
			});

			$('#tkmenu-product-inspector').on('inspector.save', function() {
				// validate form
				if (!productValidator.validate()) {
					return false;
				}

				// get updated menu product data
				const data = getTkmenuProductData();

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddTkmenuProductCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="product_json[]"]').val(JSON.stringify(data));

				// get all deleted products
				getDeletedProductVariations().forEach((id) => {
					fieldset.append('<input type="hidden" name="option_deleted[]" value="' + id + '" />');
				});

				// refresh details shown in card
				vreRefreshTkmenuProductCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');

				// update counter badge
				vreRefreshProductsCount();
			});

			$('#tkmenu-product-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="product_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="product_deleted[]" value="' + json.id + '" />');
				}

				// auto delete fieldset
				fieldset.remove();

				// NOTE: do not need to delete options too because they will be removed
				// in cascade while erasing the parent menu entry.

				// auto-close on delete
				$(this).inspector('dismiss');

				// update counter badge
				vreRefreshProductsCount();
			});

			/**
			 * Handle inspector hide.
			 *
			 * We need to bind the event by using a handler in order to have a lower priority,
			 * since the hook used to observe any form changes may be attached after this one.
			 */
			$(document).on('inspector.close', '#tkmenu-product-inspector', () => {
				if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
					// reset editor after closing the inspector
					const editor = Joomla.editors.instances.entry_description;
					
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

		w.vreOpenTkmenuProductCard = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRE_ADD_PRODUCT');
				SELECTED_OPTION = null;
			} else {
				title = Joomla.JText._('VRE_EDIT_PRODUCT');
				SELECTED_OPTION = 'tkmenu-product-fieldset-' + index;
			}
			
			// open inspector
			vreOpenInspector('tkmenu-product-inspector', {title: title});
		}

		const vreAddTkmenuProductCard = (data) => {
			let index = OPTIONS_COUNT++;

			SELECTED_OPTION = 'tkmenu-product-fieldset-' + index;

			let html = $('#tkmenu-product-struct').clone().html();
			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-3" id="' + SELECTED_OPTION + '">' + html + '</div>'
			).insertBefore('.vre-card-fieldset.add-tkmenu-product');

			// get created fieldset
			const fieldset = $('#' + SELECTED_OPTION);

			fieldset.vrecard('edit', 'vreOpenTkmenuProductCard(' + index + ')');

			// create input to hold JSON data
			const input = $('<input type="hidden" name="product_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshTkmenuProductCard = (elem, data) => {
			// update image
			elem.vrecard('image', data.img_path ? '<?php echo VREMEDIA_URI; ?>' + data.img_path : '<?php echo VREASSETS_ADMIN_URI; ?>images/product-placeholder.png');

			// update primary text
			elem.vrecard('primary', data.name);

			// update secondary text
			let secondary = $('<span class="badge badge-info product-cost"></span>').html(Currency.getInstance().format(data.price));

			if (data.options && data.options.length) {
				let varsBadge = $('<span class="badge badge-success product-vars"></span>');

				if (data.options.length == 1) {
					varsBadge.text(Joomla.JText._('VRE_N_VARIATIONS_1'));
				} else {
					varsBadge.text(Joomla.JText._('VRE_N_VARIATIONS').replace(/%d/, data.options.length));
				}

				secondary = secondary.add(varsBadge);
			}

			elem.vrecard('secondary', secondary);

			if (data.published == 1) {
				elem.find('.vre-card').addClass('published');
			} else {
				elem.find('.vre-card').removeClass('published');
			}

			// update product badge
			elem.vrecard('badge', '<i class="' + (data.ready == 1 ? 'far fa-snowflake' : 'fas fa-stopwatch') + '"></i>');
		}

		const vreRefreshProductsCount = () => {
			// update entries count
			$('#tkmenu_entries_tab_badge').attr('data-count', $('.vre-card-fieldset[id^="tkmenu-product-fieldset-"]').length);
		}
	})(jQuery, window);
</script>
