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

<!-- USAGES - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('usages')
	->value($coupon->usages)
	->label(JText::translate('VRMANAGECOUPON14'))
	->description(JText::translate('VRMANAGECOUPON14_DESC'))
	->min(0)
	->step(1);
?>

<!-- MAX USAGES - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('maxusages')
	->value(max(1, $coupon->maxusages))
	->label(JText::translate('VRMANAGECOUPON12'))
	->description(JText::translate('VRMANAGECOUPON12_DESC'))
	->min(1)
	->step(1)
	->control([
		// display only for "GIFT" coupon types
		'visible' => $coupon->type == 2,
		'class'   => 'vr-gift-child',
	]);
?>

<!-- MAX USAGES PER CUSTOMER - Select + Number -->

<?php
$maxUsagesInput = $this->formFactory->createField()
	->type('number')
	->name('maxperuser')
	->value($coupon->maxperuser)
	->min($coupon->maxperuser ? 1 : 0)
	->step(1)
	->hiddenLabel(true)
	->control([
		'visible' => $coupon->maxperuser > 0,
		'class'   => 'max-usages-child',
	]);

echo $this->formFactory->createField()
	->type('select')
	->id('vr-maxperuser-sel')
	->value(min($coupon->maxperuser, 1))
	->label(JText::translate('VRMANAGECOUPON13'))
	->description(JText::translate('VRMANAGECOUPON13_DESC'))
	->options([
		JHtml::fetch('select.option', 0, JText::translate('VRPEOPLEALLOPT1')),
		JHtml::fetch('select.option', 1, JText::translate('VRPEOPLEALLOPT2')),
	])
	->render(function($data, $input) use ($maxUsagesInput) {
		?>
		<div class="multi-field width-50">
			<?php
			echo $input;
			echo $maxUsagesInput;
			?>
		</div>
		<?php
	});
?>

<!-- REMOVE GIFT - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('remove_gift')
	->checked($coupon->remove_gift)
	->label(JText::translate('VRMANAGECOUPON15'))
	->description(JText::translate('VRMANAGECOUPON15_DESC'))
	->control([
		// display only for "GIFT" coupon types
		'visible' => $coupon->type == 2,
		'class'   => 'vr-gift-child',
	]);
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vr-maxusages-sel, #vr-maxperuser-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '90%',
			});

			$('#vr-maxusages-sel, #vr-maxperuser-sel').on('change', function() {
				// get selected value
				let sel = parseInt($(this).val());
				// get input
				const control = $(this).next('.max-usages-child');

				if (sel == 0) {
					// hide input and update value
					control.find('input').attr('min', 0).val(0);
					control.hide();
				} else {
					// show input and update value
					control.find('input').attr('min', 1).val(1);
					control.show();
				}
			});
		});
	})(jQuery);
</script>