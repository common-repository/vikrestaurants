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

$topping = $this->topping;

?>
				
<!-- PRICE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('price')
	->value($topping->price)
	->label(JText::translate('VRMANAGETKTOPPING2'))
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- PRICE QUICK UPDATE - Button -->

<?php
if ($this->products)
{
	echo $this->formFactory->createField()
		->type('button')
		->id('price-quick-update-btn')
		->text(JText::translate('VRMANAGETKTOPPING6'))
		->hiddenLabel(true)
		->description(JText::translate('VRMANAGETKTOPPING6_HELP'))
		->style('margin-bottom: 10px; width: 100%;')
		->control([
			'class' => 'price-quick-update',
			'style' => 'display: none;',
		]);

	echo $this->formFactory->createField()
		->type('hidden')
		->name('price_quick_update')
		->value('{}');
}
?>

<?php
JText::script('VRTKCONFIGITEMOPT0');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			<?php if ($this->products): ?>
				const PRICE_START_VAL = <?php echo (float) $topping->price; ?>;

				$('input[name="price"]').on('change', function() {
					if (parseFloat($(this).val()) != PRICE_START_VAL) {
						$('.price-quick-update').show();
					} else {
						$('.price-quick-update').hide();
					}
				});

				$('#price-quick-update-btn').on('click', () => {
					vreOpenInspector('price-quick-update-inspector');
				});

				// fill the form before showing the inspector
				$('#price-quick-update-inspector').on('inspector.show', function() {
					let data = $('#adminForm input[name="price_quick_update"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}

					// fill the form with the retrieved data
					setSelectedToppings(data);
				});

				$('#price-quick-update-inspector').on('inspector.save', function() {
					const data = getSelectedToppings();

					// register changes
					$('#adminForm input[name="price_quick_update"]').val(JSON.stringify(data));

					// auto-close on save
					$(this).inspector('dismiss');
				});
			<?php endif; ?>
		});
	})(jQuery);
</script>