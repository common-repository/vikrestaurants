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

$entry = $this->entry;

?>

<!-- PRICE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('price')
	->value($entry->price)
	->label(JText::translate('VRMANAGETKMENU5'))
	->min(0)
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]))
?>

<!-- TAXES - Select -->

<?php
echo $this->formFactory->createField()
	->name('id_tax')
	->value($entry->id_tax)
	->label(JText::translate('VRETAXFIELDSET'))
	->allowClear(true)
	->placeholder(JText::translate('VRTKCONFIGITEMOPT0'))
	->control([
		'class' => 'taxes-control',
		'style' => $entry->price > 0 ? '' : 'display: none;',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\TaxesFieldRenderer($this->formFactory));
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