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

?>

<div class="inspector-form" id="inspector-menu-section-form">

	<?php echo $vik->bootStartTabSet('section', ['active' => 'section_details']); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('section', 'section_details', JText::translate('JDETAILS')); ?>

			<div class="inspector-fieldset">

				<!-- NAME - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('section_name')
					->required(true)
					->label(JText::translate('VRMANAGEMENU27'));
				?>

				<!-- IMAGE - Media -->

				<?php
				echo $this->formFactory->createField()
					->type('media')
					->id('section_image')
					->label(JText::translate('VRMANAGEMENU18'));
				?>

				<!-- PUBLISHED - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('section_published')
					->checked(true)
					->label(JText::translate('VRMANAGEMENU26'));
				?>

				<!-- HIGHLIGHT - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('section_highlight')
					->checked(true)
					->label(JText::translate('VRMANAGEMENU32'))
					->description(JText::translate('VRMANAGEMENU32_HELP'));
				?>

				<!-- ORDER DISHES - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('section_orderdishes')
					->checked(true)
					->label(JText::translate('VRMANAGEMENU34'))
					->description(JText::translate('VRMANAGEMENU34_DESC'));
				?>

				<!-- DESCRIPTION - Editor -->

				<?php
				echo $this->formFactory->createField()
					->type('editor')
					->name('section_description')
					->label(JText::translate('VRMANAGEMENU17'))
					->render(function($data, $input) {
						// wrap editor within a form in order to avoid TinyMCE errors
						echo '<form>' . $input . '</form>';
					});
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- PRODUCTS -->

		<?php echo $vik->bootAddTab('section', 'section_products', JText::translate('VRMENUMENUSPRODUCTS')); ?>

			<div style="display:none;" id="section-prod-repeat">

				<div class="inspector-repeatable-head">
					<span class="section-prod-summary">
						<i class="fas fa-ellipsis-v big hndl" style="margin-right: 4px;"></i>

						<span class="badge badge-info prod-name"></span>
						<span class="badge badge-important prod-charge"></span>
					</span>

					<span>
						<a href="javascript: void(0);" class="menu-section-edit-prod no-underline">
							<i class="fas fa-pen-square big ok"></i>
						</a>

						<a href="javascript: void(0);" class="menu-section-trash-prod no-underline">
							<i class="fas fa-minus-square big no"></i>
						</a>
					</span>
				</div>

				<div class="inspector-repeatable-body">

					<!-- NAME - Text -->

					<?php
					echo $this->formFactory->createField()
						->type('text')
						->class('section_prod_name')
						->readonly(true)
						->label(JText::translate('VRMANAGETKSTOCK1'));
					?>

					<!-- Price - Number -->

					<?php
					echo $this->formFactory->createField()
						->type('text')
						->class('section_prod_price')
						->readonly(true)
						->label(JText::translate('VRMANAGEMENUSPRODUCT4'))
						->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
							'before' => VREFactory::getCurrency()->getSymbol(),
						]));
					?>

					<!-- CHARGE - Number -->

					<?php
					echo $this->formFactory->createField()
						->type('number')
						->name('section_prod_charge')
						->class('section_prod_charge')
						->label(JText::translate('VRMANAGETKAREA4'))
						->description(JText::translate('VRE_PRODUCT_INC_PRICE_SHORT'))
						->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
							'before' => VREFactory::getCurrency()->getSymbol(),
						]));
					?>

					<input type="hidden" name="section_prod_id" class="section_prod_id" />
					<input type="hidden" name="section_prod_id_assoc" class="section_prod_id_assoc" />

				</div>

			</div>

			<div class="inspector-repeatable-container" id="section-prod-pool">
				
			</div>

			<!-- ADD PRODUCT - Button -->

			<?php
			echo $this->formFactory->createField()
				->type('button')
				->id('add-section-product-btn')
				->text('<i class="fas fa-plus-circle"></i> '. JText::translate('VRMANAGEMENU23'))
				->hiddenLabel(true);
			?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>

	<input type="hidden" id="section_id" class="field" value="" />
</div>

<script>
	(function($, w) {
		'use strict';

		w.fillMenuSectionForm = (data) => {
			const form = $('#inspector-menu-section-form');

			// update name
			if (data.name === undefined) {
				data.name = '';
			}

			$('#section_name').val(data.name);

			sectionValidator.unsetInvalid($('#section_name'));

			// update image
			if (data.image === undefined) {
				data.image = '';
			}

			$('#section_image').mediamanager('val', data.image);

			// update published
			if (data.published === undefined) {
				data.published = true;
			} else if (typeof data.published === 'string') {
				data.published = parseInt(data.published);
			}

			$('#section_published').prop('checked', data.published);

			// update highlight
			if (data.highlight === undefined) {
				data.highlight = true;
			} else if (typeof data.highlight === 'string') {
				data.highlight = parseInt(data.highlight);
			}

			$('#section_highlight').prop('checked', data.highlight);

			// update order dishes
			if (data.orderdishes === undefined) {
				data.orderdishes = false;
			} else if (typeof data.orderdishes === 'string') {
				data.orderdishes = parseInt(data.orderdishes);
			}

			$('#section_orderdishes').prop('checked', data.orderdishes);

			// update description
			Joomla.editors.instances.section_description.setValue(data.description ? data.description : '');

			$('#section-prod-pool').html('');

			if (data.products !== undefined) {
				data.products.forEach((prod) => {
					addSectionProduct(prod);
				});
			}
			
			// update ID
			$('#section_id').val(data.id);

			// clear all deleted products on open
			form.find('input.section_prod_deleted').remove();
		}

		w.getMenuSectionData = () => {
			const form = $('#inspector-menu-section-form');

			let data = {};

			// set ID
			data.id = $('#section_id').val();

			// set name
			data.name = $('#section_name').val();

			// set image
			data.image = $('#section_image').mediamanager('val');

			// set published
			data.published = $('#section_published').is(':checked') ? 1 : 0;

			// set highlight
			data.highlight = $('#section_highlight').is(':checked') ? 1 : 0;

			// set order dishes
			data.orderdishes = $('#section_orderdishes').is(':checked') ? 1 : 0;

			// get description
			data.description = Joomla.editors.instances.section_description.getValue();

			// set products
			data.products = [];

			// iterate forms
			$('#section-prod-pool .inspector-repeatable').each(function() {
				let tmp = {};

				// retrieve assoc ID
				tmp.id = parseInt($(this).find('input.section_prod_id_assoc').val());

				// retrieve product ID
				tmp.idProduct = parseInt($(this).find('input.section_prod_id').val());

				// retrieve product name
				tmp.name = $(this).find('input.section_prod_name').val();

				// retrieve product price
				tmp.price = parseFloat($(this).find('input.section_prod_price').val());
				tmp.price = isNaN(tmp.price) ? 0.0 : tmp.price;

				// retrieve product charge/discount
				tmp.charge = parseFloat($(this).find('input.section_prod_charge').val());
				tmp.charge = isNaN(tmp.charge) ? 0.0 : tmp.charge;

				data.products.push(tmp);
			});

			return data;
		}

		w.getDeletedSectionProducts = () => {
			let list = [];

			$('#inspector-menu-section-form')
				.find('input.section_prod_deleted')
					.each(function() {
						list.push(parseInt($(this).val()));
					});

			return list;
		}

		const addSectionProduct = (data) => {
			if (typeof data !== 'object') {
				data = {};
			}

			let form = $('#inspector-menu-section-form');

			// get repeatable form of the inspector
			const repeatable = $(form).find('#section-prod-repeat');
			// clone the form
			const clone = $('<div class="inspector-repeatable"></div>')
				.append(repeatable.clone().html());

			const nameInput = clone.find('input.section_prod_name');

			// set up product name/label
			if (typeof data.name !== 'undefined') {
				nameInput.val(data.name);

				// auto-collapse existing blocks
				clone.addClass('collapsed');
			}

			const priceInput = clone.find('input.section_prod_price');

			// set up product price
			if (typeof data.price !== 'undefined') {
				priceInput.val(data.price);
			}

			// set up product charge/discount
			const chargeInput = clone.find('input.section_prod_charge');
			chargeInput.val(data.charge || 0);

			const idProductInput = clone.find('input.section_prod_id');

			// set up product ID
			if (typeof data.idProduct !== 'undefined') {
				idProductInput.val(data.idProduct);
			}

			// set up product assoc ID
			const idInput = clone.find('input.section_prod_id_assoc');
			idInput.val(data.id || 0);

			// refresh head every time something changes
			$(chargeInput).on('change', function() {
				let charge = parseFloat($(this).val());

				if (isNaN(charge)) {
					$(this).val(0);
				}

				vreRefreshSummaryProduct(clone);
			});

			// set up summary head
			vreRefreshSummaryProduct(clone);

			// handle delete button
			clone.find('.menu-section-trash-prod').on('click', () => {
				if (confirm(Joomla.JText._('VRSYSTEMCONFIRMATIONMSG'))) {
					deleteProductBlock(clone);
				}
			});

			// handle edit button
			clone.find('.menu-section-edit-prod').on('click', () => {
				clone.toggleClass('collapsed');
			});

			// append the clone to the document
			$('#section-prod-pool').append(clone);

			// start by focusing "charge" input
			chargeInput.focus();

			clone.find('.vr-quest-popover').popover({
				sanitize: false,
				container: 'body',
				trigger: 'hover focus',
				html: true,
			});
		}

		const vreRefreshSummaryProduct = (block) => {
			const currency = Currency.getInstance();

			// extract name from block
			let name = block.find('input.section_prod_name').val();

			// extract price from block
			let price = parseFloat(block.find('input.section_prod_price').val());
			price = isNaN(price) ? 0 : price;

			// extract charge from block
			let charge = parseFloat(block.find('input.section_prod_charge').val());
			charge = isNaN(charge) ? 0 : charge;

			charge = currency.sum(price, charge);

			// set badge within block head
			block.find('.section-prod-summary').find('.prod-name').text(name);
			block.find('.section-prod-summary').find('.prod-charge').html(currency.format(charge));
		}

		const deleteProductBlock = (block) => {
			const id = parseInt($(block).find('.section_prod_id_assoc').val());

			if (!isNaN(id) && id > 0) {
				// register product to delete
				$('#inspector-menu-section-form').append('<input type="hidden" class="section_prod_deleted" value="' + id + '" />');
			}

			$(block).remove();
		}

		$(function() {
			w.sectionValidator = new VikFormValidator('#inspector-menu-section-form');

			$('#add-section-product-btn').on('click', () => {
				let products = [];

				// iterate forms
				$('#section-prod-pool .inspector-repeatable').each(function() {
					products.push(parseInt($(this).find('.section_prod_id').val()));
				});

				// initialise modal
				vreInitProductsLayout(products);

				vrOpenJModal('products', null, true);
			});

			$('#adminForm').on('submit', () => {
				const editor = Joomla.editors.instances.section_description;

				if (editor.onSave) {
					editor.onSave();
				}
			});

			$('#section-prod-pool').sortable({
				items:  '.inspector-repeatable',
				revert: false,
				axis:   'y',
				handle: '.hndl',
				cursor: 'move',
			});

			$('#save-section-products').on('click', () => {
				const products = vreGetSelectedProducts();

				let existing = [];

				// look for any products to delete
				$('#section-prod-pool .inspector-repeatable').each(function() {
					const idProduct = parseInt($(this).find('.section_prod_id').val());

					if (!products.some(prod => prod.id == idProduct)) {
						// product missing, delete it
						deleteProductBlock(this);
					} else {
						existing.push(idProduct);
					}
				});

				// look for new products to add
				products.forEach((prod) => {
					if (existing.indexOf(parseInt(prod.id)) === -1) {
						// product not found, register it
						addSectionProduct(Object.assign(prod, {
							id: 0,
							idProduct: prod.id,
						}));
					}
				});

				vrCloseJModal('products');
			});
		});
	})(jQuery, window);
</script>