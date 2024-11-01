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

$product = $this->product;

?>
						
<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($product->name)
	->label(JText::translate('VRMANAGEMENUSPRODUCT2'))
	->class('input-xxlarge input-large-text')
	->required(true);
?>

<!-- PRICE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('price')
	->value((float) $product->price)
	->label(JText::translate('VRMANAGEMENUSPRODUCT4'))
	->min(0)
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]))
?>

<!-- TAXES - Select -->

<?php
echo $this->formFactory->createField([
	'name'       => 'id_tax',
	'value'      => $product->id_tax,
	'label'      => JText::translate('VRETAXFIELDSET'),
	'allowClear' => true,
	'placeholder' => JText::translate('VRTKCONFIGITEMOPT0'),
	'control'    => [
		'class' => 'taxes-control',
		'style' => $product->price > 0 ? '' : 'display: none;',
	]
])->render(new E4J\VikRestaurants\Form\Renderers\TaxesFieldRenderer($this->formFactory));
?>
	
<!-- IMAGE - Media -->

<?php
echo $this->formFactory->createField()
	->type('media')
	->name('image')
	->value($product->image)
	->label(JText::translate('VRMANAGEMENUSPRODUCT5'));
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('input[name="price"]').on('change', function() {
				const price = parseFloat($(this).val());

				if (!isNaN(price) && price > 0) {
					$('.taxes-control').show();
				} else {
					$('.taxes-control').hide();
				}
			});
		});
	})(jQuery);
</script>