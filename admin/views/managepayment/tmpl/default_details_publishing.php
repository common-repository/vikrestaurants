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

$payment = $this->payment;

?>

<!-- PUBLISHED - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('published')
	->checked($payment->published)
	->label(JText::translate('VRMANAGEPAYMENT3'));
?>

<!-- TRUST - Number -->

<?php
$trustValueInput = $this->formFactory->createField()
	->type('number')
	->name('trust')
	->value($payment->trust)
	->min($payment->trust ? 1 : 0)
	->step(1)
	->hiddenLabel(true)
	->control([
		'class' => 'vr-trust-child',
		'style' => $payment->trust ? '' : 'display: none;',
	]);

echo $this->formFactory->createField()
	->type('checkbox')
	->name('trust_check')
	->checked($payment->trust >= 1)
	->label(JText::translate('VRMANAGEPAYMENT14'))
	->description(JText::translate('VRMANAGEPAYMENT14_DESC'))
	->onchange('trustValueChanged(this.checked)')
	->render(function($data, $input) use ($trustValueInput) {
		?>
		<div class="multi-field last-flex first-stretch">
			<?php echo $input; ?>

			<?php echo $trustValueInput; ?>
		</div>
		<?Php
	});
?>

<!-- RESTRICTIONS - Select -->

<?php
if ($payment->enablecost > 0)
{
	$factor = 1;
}
else if ($payment->enablecost < 0)
{
	$factor = -1;
}
else
{
	$factor = 0;
}

echo $this->formFactory->createField()
	->type('select')
	->name('enablecost_factor')
	->value($factor)
	->id('vr-enablecost-sel')
	->label(JText::translate('VRMANAGEPAYMENT10'))
	->options([
		JHtml::fetch('select.option',  0, JText::translate('VRPAYRESTROPT1')),
		JHtml::fetch('select.option',  1, JText::translate('VRPAYRESTROPT2')),
		JHtml::fetch('select.option', -1, JText::translate('VRPAYRESTROPT3')),
	]);

echo $this->formFactory->createField()
	->type('number')
	->name('enablecost_amount')
	->value(abs($payment->enablecost))
	->min(0)
	->step('any')
	->control([
		'class' => 'vrenablecost-amount',
		'style' => $payment->enablecost == 0 ? 'display: none;' : '',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- GROUP - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('group')
	->value($payment->group)
	->label(JText::translate('VRMANAGECUSTOMF7'))
	->options(JHtml::fetch('vrehtml.admin.groups', [1, 2], true, ''))
?>

<?php
JText::script('VRE_FILTER_SELECT_GROUP');
?>

<script>
	(function($, w) {
		'use strict';

		w.trustValueChanged = (is) => {
			if (is) {
				$('.vr-trust-child').show().find('input').attr('min', 1).val(1);
			} else {
				$('.vr-trust-child').hide().find('input').attr('min', 0).val(0);
			}
		}

		$(function() {
			$('select[name="group"]').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_GROUP'),
				allowClear: true,
				width: '100%',
			});

			$('#vr-enablecost-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: '100%',
			});

			$('#vr-enablecost-sel').on('change', function() {
				if ($(this).val() == 0) {
					$('.vrenablecost-amount').hide();
				} else {
					$('.vrenablecost-amount').show();
				}
			});
		});
	})(jQuery, window);
</script>