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

$attributes = JHtml::fetch('vrehtml.admin.tkattributes');

$attr_icons = [];

foreach ($attributes as $attr)
{
	$attr_icons[$attr->value] = $attr->icon;
}

?>

<div class="inspector-form" id="inspector-tkmenu-product-form">

	<?php echo $vik->bootStartTabSet('tkentry', ['active' => 'tkentry_details']); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('tkentry', 'tkentry_details', JText::translate('JDETAILS')); ?>

			<div class="inspector-fieldset">
			
				<!-- NAME - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('entry_name')
					->required(true)
					->label(JText::translate('VRMANAGETKMENU4'));
				?>

				<!-- ALIAS - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('entry_alias')
					->label(JText::translate('JFIELD_ALIAS_LABEL'));
				?>

				<!-- IMAGE - Media -->

				<?php
				echo $this->formFactory->createField()
					->type('media')
					->id('entry_img_path')
					->multiple(true)
					->label(JText::translate('VRMANAGETKMENU16'));
				?>

				<!-- DESCRIPTION - Editor -->

				<?php
				echo $this->formFactory->createField()
					->type('editor')
					->name('entry_description')
					->label(JText::translate('VRMANAGETKMENU2'))
					->render(function($data, $input) {
						// wrap editor within a form in order to avoid TinyMCE errors
						return '<form>' . $input . '</form>';
					});
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- PROPERTIES -->

		<?php echo $vik->bootAddTab('tkentry', 'tkentry_properties', JText::translate('VRMAPPROPERTIESBUTTON')); ?>

			<div class="inspector-fieldset">

				<!-- PRICE - Number -->

				<?php
				echo $this->formFactory->createField()
					->type('number')
					->id('entry_price')
					->label(JText::translate('VRMANAGETKMENU5'))
					->min(0)
					->step('any')
					->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
						'before' => VREFactory::getCurrency()->getSymbol(),
					]));
				?>

				<!-- TAXES - Dropdown -->

				<?php
				echo $this->formFactory->createField()
					->id('entry_id_tax')
					->label(JText::translate('VRETAXFIELDSET'))
					->allowClear(true)
					->placeholder(JText::translate('VRTKCONFIGITEMOPT0'))
					->control(['class' => 'taxes-control'])
					// do not allow taxes creation as the modal might be displayed within the inspector
					->create(false)
					->render(new E4J\VikRestaurants\Form\Renderers\TaxesFieldRenderer($this->formFactory));
				?>

				<!-- ATTRIBUTES - Dropdown -->

				<?php
				echo $this->formFactory->createField()
					->type('select')
					->id('entry_attributes')
					->label(JText::translate('VRMANAGETKMENU18'))
					->multiple(true)
					->options(array_merge([JHtml::fetch('select.option', '', '')], $attributes))
				?>

				<!-- STATUS - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('entry_published')
					->label(JText::translate('VRMANAGETKMENU12'));
				?>

				<!-- PREPARATION - Checkbox -->

				<?php
				echo $this->formFactory->createField()
					->type('checkbox')
					->id('entry_ready')
					->label(JText::translate('VRMANAGETKMENU9'))
					->description(JText::translate('VRMANAGETKMENU9_HELP'));
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- VARIATIONS -->

		<?php echo $vik->bootAddTab('tkentry', 'tkentry_variations', JText::translate('VRMANAGETKENTRYFIELDSET2')); ?>

			<div style="display:none;" id="product-var-repeat">

				<div class="inspector-repeatable-head">
					<span class="product-var-summary">
						<i class="fas fa-ellipsis-v big hndl" style="margin-right: 4px;"></i>

						<span class="badge badge-info var-name"></span>
						<span class="badge badge-important var-price"></span>
					</span>

					<span>
						<a href="javascript: void(0);" class="tkmenu-prod-edit-var no-underline">
							<i class="fas fa-pen-square big ok"></i>
						</a>

						<a href="javascript: void(0);" class="tkmenu-prod-trash-var no-underline">
							<i class="fas fa-minus-square big no"></i>
						</a>
					</span>
				</div>

				<div class="inspector-repeatable-body">

					<!-- NAME - Text -->

					<?php
					echo $this->formFactory->createField()
						->type('text')
						->name('prod_var_name')
						->class('prod_var_name')
						->id(false)
						->multiple(true)
						->label(JText::translate('VRMANAGETKSTOCK2'));
					?>

					<!-- PRICE - Number -->

					<?php
					echo $this->formFactory->createField()
						->type('text')
						->name('prod_var_inc_price')
						->class('prod_var_inc_price')
						->id(false)
						->multiple(true)
						->label(JText::translate('VRMANAGETKAREA4'))
						->description(JText::translate('VRE_PRODUCT_INC_PRICE_SHORT'))
						->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
							'before' => VREFactory::getCurrency()->getSymbol(),
						]));
					?>

					<input type="hidden" name="prod_var_id[]" class="prod_var_id" />

				</div>

			</div>

			<div class="inspector-repeatable-container" id="product-var-pool">
				
			</div>

			<?php
			echo $this->formFactory->createField()
				->type('button')
				->id('add-product-var-btn')
				->text('<i class="fas fa-plus-circle"></i> ' . JText::translate('VRMANAGETKMENUADDVAR'))
				->hiddenLabel(true);
			?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>

	<input type="hidden" id="entry_id" class="field" value="" />

</div>

<?php
JText::script('VRTKNOATTR');
JText::script('VRMANAGETKSTOCK2');
?>

<script>
	(function($, w) {
		'use strict';

		const ATTRIBUTES_LOOKUP = <?php echo json_encode($attr_icons); ?>;

		w.fillTkmenuProductForm = (data) => {
			const form = $('#inspector-tkmenu-product-form');

			// update name
			if (data.name === undefined) {
				data.name = '';
			}

			$('#entry_name').val(data.name);

			productValidator.unsetInvalid($('#entry_name'));

			// update alias
			if (data.alias === undefined) {
				data.alias = '';
			}

			$('#entry_alias').val(data.alias);

			// update price
			if (data.price === undefined) {
				data.price = 0;
			}

			$('#entry_price').val(data.price).trigger('change');

			// update taxes
			if (data.id_tax === undefined) {
				data.id_tax = 0;
			}

			$('#entry_id_tax').select2('val', data.id_tax);

			// update attributes
			if (data.attributes === undefined) {
				data.attributes = [];
			}

			$('#entry_attributes').select2('val', data.attributes);

			// update image
			let images = [];

			if (data.img_path) {
				images.push(data.img_path);
			}

			$('#entry_img_path').mediamanager('val', images.concat(data.img_extra || []));

			// update published
			if (data.published === undefined) {
				data.published = true;
			} else if (typeof data.published === 'string') {
				data.published = parseInt(data.published);
			}

			$('#entry_published').prop('checked', data.published);

			// update preparation
			if (data.ready === undefined) {
				data.ready = false;
			} else if (typeof data.ready === 'string') {
				data.ready = parseInt(data.ready);
			}

			$('#entry_ready').prop('checked', data.ready);

			// update description
			Joomla.editors.instances.entry_description.setValue(data.description ? data.description : '');

			$('#product-var-pool').html('');

			if (data.options !== undefined) {
				data.options.forEach((opt) => {
					addProductVariation(opt);
				});
			}
			
			// update ID
			$('#entry_id').val(data.id);

			// clear all deleted products on open
			form.find('input.product_var_deleted').remove();

			// always fallback to default details tab
			$('a[href="#tkentry_details"]').trigger('click');
			// for J4 compatibility
			$('body.com_vikrestaurants joomla-tab button[aria-controls="tkentry_details"]').trigger('click');
		}

		w.getTkmenuProductData = () => {
			const form = $('#inspector-tkmenu-product-form');

			let data = {};

			// set ID
			data.id = $('#entry_id').val();

			// set name
			data.name = $('#entry_name').val();

			// set alias
			data.alias = $('#entry_alias').val();

			// set price
			data.price = parseFloat($('#entry_price').val());
			data.price = isNaN(data.price) ? 0 : Math.abs(data.price);

			// set taxes
			data.id_tax = $('#entry_id_tax').select2('val');

			// set attributes
			data.attributes = $('#entry_attributes').select2('val');

			// set image
			data.img_path  = '';
			data.img_extra = [];

			let images = $('#entry_img_path').mediamanager('val');

			if (images && images.length) {
				data.img_path  = images.shift();
				data.img_extra = images;
			}

			// set published
			data.published = $('#entry_published').is(':checked') ? 1 : 0;

			// set preparation
			data.ready = $('#entry_ready').is(':checked') ? 1 : 0;

			// get description
			data.description = Joomla.editors.instances.entry_description.getValue();

			// set variations
			data.options = [];

			// iterate forms
			$('#product-var-pool .inspector-repeatable').each(function() {
				let tmp = {};

				// retrieve var ID
				tmp.id = parseInt($(this).find('input.prod_var_id').val());

				// retrieve var name
				tmp.name = $(this).find('input.prod_var_name').val();

				// retrieve variation incremental price
				tmp.inc_price = parseFloat($(this).find('input.prod_var_inc_price').val());
				tmp.inc_price = isNaN(tmp.inc_price) ? 0.0 : tmp.inc_price;

				data.options.push(tmp);
			});

			return data;
		}

		w.getDeletedProductVariations = () => {
			let list = [];

			$('#inspector-tkmenu-product-form')
				.find('input.product_var_deleted')
					.each(function() {
						list.push(parseInt($(this).val()));
					});

			return list;
		}

		const addProductVariation = (data) => {
			if (typeof data !== 'object') {
				data = {};
			}

			let form = $('#inspector-tkmenu-product-form');

			// get repeatable form of the inspector
			const repeatable = $(form).find('#product-var-repeat');
			// clone the form
			const clone = $('<div class="inspector-repeatable"></div>')
				.append(repeatable.clone().html());

			const nameInput = clone.find('input.prod_var_name');

			// set up variation name
			if (typeof data.name !== 'undefined') {
				nameInput.val(data.name);

				// auto-collapse existing blocks
				clone.addClass('collapsed');
			}

			// set up product incremental price
			const priceInput = clone.find('input.prod_var_inc_price');
			priceInput.val(data.inc_price || 0);

			// set up product ID
			const idInput = clone.find('input.prod_var_id');
			idInput.val(data.id || 0);

			// refresh head every time something changes
			$(nameInput).add(priceInput).on('change', function() {
				vreRefreshSummaryVariation(clone);
			});

			// set up summary head
			vreRefreshSummaryVariation(clone);

			// handle delete button
			clone.find('.tkmenu-prod-trash-var').on('click', () => {
				if (confirm(Joomla.JText._('VRSYSTEMCONFIRMATIONMSG'))) {
					deleteVariationBlock(clone);
				}
			});

			// handle edit button
			clone.find('.tkmenu-prod-edit-var').on('click', () => {
				clone.toggleClass('collapsed');
			});

			// append the clone to the document
			$('#product-var-pool').append(clone);

			// start by focusing "name" input
			nameInput.focus();

			clone.find('.vr-quest-popover').popover({
				sanitize: false,
				container: 'body',
				trigger: 'hover focus',
				html: true,
			});
		}

		const vreRefreshSummaryVariation = (block) => {
			const currency = Currency.getInstance();

			// extract name from block
			let name = block.find('input.prod_var_name').val() || Joomla.JText._('VRMANAGETKSTOCK2');

			// extract price from block
			let price = parseFloat(block.find('input.prod_var_inc_price').val());
			price = isNaN(price) ? 0 : price;

			// set badge within block head
			block.find('.product-var-summary').find('.var-name').text(name);
			block.find('.product-var-summary').find('.var-price').html(currency.format(price));
		}

		const deleteVariationBlock = (block) => {
			const id = parseInt($(block).find('.prod_var_id').val());

			if (!isNaN(id) && id > 0) {
				// register product to delete
				$('#inspector-tkmenu-product-form').append('<input type="hidden" class="product_var_deleted" value="' + id + '" />');
			}

			$(block).remove();
		}

		$(function() {
			w.productValidator = new VikFormValidator('#inspector-tkmenu-product-form');

			$('#add-product-var-btn').on('click', () => {
				addProductVariation();
			});

			$('#adminForm').on('submit', () => {
				const editor = Joomla.editors.instances.entry_description;

				if (editor.onSave) {
					editor.onSave();
				}
			});

			const formatAttributeOption = (attr) => {
				if (!attr.id) {
					// optgroup
					return attr.text;
				}

				if (!ATTRIBUTES_LOOKUP.hasOwnProperty(attr.id)) {
					// unsupported icon
					return attr.text;
				}

				return '<img class="vr-opt-tkattr" src="<?php echo VREMEDIA_URI; ?>' + ATTRIBUTES_LOOKUP[attr.id] + '" /> ' + attr.text;
			}

			$('#entry_attributes').select2({
				placeholder: Joomla.JText._('VRTKNOATTR'),
				allowClear: true,
				width: '100%',
				formatResult: formatAttributeOption,
				formatSelection: formatAttributeOption,
				escapeMarkup: m => m,
			});

			$('#entry_price').on('change', function() {
				const price = parseFloat($(this).val());

				if (!isNaN(price) && price > 0) {
					$('.taxes-control').show();
				} else {
					$('.taxes-control').hide();
				}
			});

			$('#product-var-pool').sortable({
				items:  '.inspector-repeatable',
				revert: false,
				axis:   'y',
				handle: '.hndl',
				cursor: 'move',
			});
		});
	})(jQuery, window);
</script>