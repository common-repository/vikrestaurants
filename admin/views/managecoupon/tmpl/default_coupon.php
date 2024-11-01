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

$coupon = $this->coupon;

?>

<!-- GROUP - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('group')
	->id('vr-group-sel')
	->value($coupon->group)
	->label(JText::translate('VRMANAGECOUPON10'))
	->options(JHtml::fetch('vrehtml.admin.groups'));
?>

<!-- CODE - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('code')
	->value($coupon->code)
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGECOUPON1'));
?>

<!-- TYPE - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('type')
	->value($coupon->type)
	->label(JText::translate('VRMANAGECOUPON2'))
	->description(JText::translate('VRMANAGECOUPON2_DESC'))
	->options([
		JHtml::fetch('select.option', 1, JText::translate('VRCOUPONTYPEOPTION1')),
		JHtml::fetch('select.option', 2, JText::translate('VRCOUPONTYPEOPTION2')),
	]);
?>

<!-- AMOUNT - Number + Dropdown -->

<?php
$couponAmountTypeSelect = $this->formFactory->createField()
	->type('select')
	->name('percentot')
	->value($coupon->percentot)
	->hidden(true)
	->options([
		JHtml::fetch('select.option', 1,                                    '%'),
		JHtml::fetch('select.option', 2, VREFactory::getCurrency()->getSymbol()),
	]);

echo $this->formFactory->createField()
	->type('number')
	->name('value')
	->value($coupon->value)
	->required(true)
	->min(0)
	->step('any')
	->label(JText::translate('VRMANAGECOUPON4'))
	->description(JText::translate('VRMANAGECOUPON4_DESC'))
	->render(function($data, $input) use ($couponAmountTypeSelect) {
		?>
		<div class="multi-field">
			<?php echo $input; ?>
			<?php echo $couponAmountTypeSelect; ?>
		</div>
		<?php
	});
?>

<!-- MINIMUM COST - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('mincost')
	->value($coupon->mincost)
	->min(0)
	->step('any')
	->label(JText::translate('VRMANAGECOUPON9'))
	->description(JText::translate('VRMANAGECOUPON9_DESC'))
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- MINIMUM PEOPLE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('minpeople')
	->value($coupon->minpeople)
	->min(0)
	->step(1)
	->label(JText::translate('VRMANAGECOUPON8'))
	->description(JText::translate('VRMANAGECOUPON8_DESC'))
	->control([
		// display only for "restaurant" group
		'visible' => $coupon->group == 0,
		'class'   => 'vr-restaurant-child',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('<i class="fas fa-users"></i>'));
?>

<!-- CATEGORY - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('id_category')
	->value($coupon->id_category ?: null)
	->label(JText::translate('VRMANAGECOUPON16'))
	->description(JText::translate('VRMANAGECOUPON16_DESC'))
	->options(array_merge(
		[
			JHtml::fetch('select.option', '',                         ''),
			JHtml::fetch('select.option',  0, JText::translate('VRCREATENEWOPT')),
		],
		JHtml::fetch('vrehtml.admin.coupongroups')
	));
?>

<!-- CATEGORY NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('category_name')
	->placeholder(JText::translate('VRMANAGELANG2'))
	->control([
		'visible' => false,
		'class'   => 'vr-category-child',
	]);
?>

<?php
JText::script('VRE_FILTER_SELECT_CATEGORY');
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			$('select[name="group"], select[name="type"], select[name="percentot"]').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			$('select[name="group"], select[name="type"], select[name="percentot"], select[name="id_category"]').select2({
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_CATEGORY'),
				allowClear: true,
				width: 200,
			});

			$('select[name="group"]').on('change', function() {
				if ($(this).val() == 0) {
					$('.vr-restaurant-child').show();
				} else {
					$('.vr-restaurant-child').hide();
				}
			});

			$('select[name="type"]').on('change', function() {
				if ($(this).val() == 2) {
					$('.vr-gift-child').show();
				} else {
					$('.vr-gift-child').hide();
				}
			});

			$('select[name="id_category"]').on('change', function() {
				const categoryInput = $('input[name="category_name"]');

				if (parseInt($(this).val()) === 0) {
					$('.vr-category-child').show();
					categoryInput.focus();
					w.validator.registerFields(categoryInput);
				} else {
					$('.vr-category-child').hide();
					categoryInput.val('');
					w.validator.unregisterFields(categoryInput);
				}
			});
		});
	})(jQuery, window);
</script>