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

/**
 * Layout variables
 * -----------------
 * @var  object  $product  The product details.
 * @var  object  $item     The selected cart item data.
 */
extract($displayData);

/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
$formFactory = VREFactory::getPlatform()->getFormFactory();

?>

<div class="inspector-fieldset">

	<!-- NAME - Text -->

	<?php
	echo $formFactory->createField()
		->type('text')
		->id('item_name')
		->value($product->name)
		->readonly($product->id)
		->required(!$product->id)
		->label(JText::translate('VRMANAGEMENUSPRODUCT2'));
	?>

	<!-- VARIATION - Select -->

	<?php
	$optionsPricesLookup = [];
	$selectedOptionPrice = 0;

	if ($product->options)
	{
		foreach ($product->options as $opt)
		{
			$optionsPricesLookup[$opt->id] = (float) $opt->inc_price;

			if ($opt->id == $item->id_product_option)
			{
				$selectedOptionPrice = (float) $opt->inc_price;
			}
		}

		echo $formFactory->createField()
			->type('select')
			->id('item_id_product_option')
			->value($item->id_product_option)
			->required(true)
			->label(JText::translate('VRTKCARTOPTION5'))
			->options(array_map(function($opt)
			{
				return JHtml::fetch('select.option', $opt->id, $opt->name);
			}, $product->options));
	}
	?>

	<!-- SERVING NUMBER - Select -->

	<?php
	if (VREFactory::getConfig()->getBool('servingnumber'))
	{
		echo $formFactory->createField()
			->type('select')
			->id('item_serving_number')
			->value($item->servingnumber)
			->label(JText::translate('VRE_ORDERDISH_SERVING_NUMBER_LABEL_SHORT'))
			->options([
				JHtml::fetch('select.option', 0, JText::translate('VRE_ORDERDISH_SERVING_NUMBER_0')),
				JHtml::fetch('select.option', 1, JText::translate('VRE_ORDERDISH_SERVING_NUMBER_1')),
				JHtml::fetch('select.option', 2, JText::translate('VRE_ORDERDISH_SERVING_NUMBER_2')),
			]);
	}
	?>

	<!-- UNITS - Number -->

	<?php
	echo $formFactory->createField()
		->type('number')
		->id('item_quantity')
		->value($item->quantity)
		->label(JText::translate('VRMANAGETKRES20'))
		->min(1)
		->step(1)
		->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRE_PIECES_SHORT')))
	?>

	<!-- PRICE - Number -->

	<?php
	echo $formFactory->createField()
		->type('number')
		->id('item_price')
		->value($item->price)
		->label(JText::translate('VRMANAGEMENUSPRODUCT4'))
		->min(0)
		->step('any')
		->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
			'before' => VREFactory::getCurrency()->getSymbol(),
		]));
	?>

	<input type="hidden" id="item_discount" value="<?php echo (float) $item->discount; ?>" />

	<!-- TAXES -  -->

	<?php
	if (!$product->id)
	{
		echo $formFactory->createField()
			->id('item_id_tax')
			->label(JText::translate('VRETAXFIELDSET'))
			->allowClear(true)
			->placeholder(JText::translate('VRTKCONFIGITEMOPT0'))
			// do not allow taxes creation as the modal might be displayed within the inspector
			->create(false)
			->render(new E4J\VikRestaurants\Form\Renderers\TaxesFieldRenderer($formFactory));
	}
	?>

	<!-- NOTES - Textarea -->

	<?php
	echo $formFactory->createField()
		->type('textarea')
		->id('item_notes')
		->value($item->notes)
		->label(JText::translate('VRMANAGETKRESTITLE4'))
		->height(100)
		->style('resize: vertical;');
	?>

	<input type="hidden" id="item_id" value="<?php echo (int) $item->id; ?>" />
	<input type="hidden" id="item_id_product" value="<?php echo (int) $item->id_product; ?>" />

</div>

<script>
	(function($) {
		'use strict';

		const optionsPricesLookup = <?php echo json_encode($optionsPricesLookup); ?>;
		let selectedOptionPrice = <?php echo (float) $selectedOptionPrice; ?>;

		$('select#item_id_product_option, select#item_serving_number').select2({
			allowClear: false,
			width: '100%',
		});

		$('select#item_id_product_option').on('change', function() {
			let optionId = parseInt($(this).val());
			let itemPrice = parseFloat($('#item_price').val());

			if (isNaN(itemPrice)) {
				itemPrice = 0;
			}

			// subtract the cost of the previously selected option
			itemPrice = Math.max(0, itemPrice - selectedOptionPrice);

			if (optionsPricesLookup.hasOwnProperty(optionId)) {
				// track the new option cost 
				selectedOptionPrice = optionsPricesLookup[optionId];

				// increase price by the cost of the newly selected option
				itemPrice = Math.max(0, itemPrice + selectedOptionPrice);
			} else {
				// option not found (?)
				selectedOptionPrice = 0;
			}

			$('#item_price').val(itemPrice);
		});
	})(jQuery);
</script>