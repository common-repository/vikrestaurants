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

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($payment->name)
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGEPAYMENT1'));
?>

<!-- File - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('file')
	->value($payment->file)
	->id('vr-file-sel')
	->required(true)
	->label(JText::translate('VRMANAGEPAYMENT2'))
	->options(array_merge(
		[
			// add placeholder
			JHtml::fetch('select.option', '', '')
		],
		// add supported drivers
		JHtml::fetch('vrehtml.admin.paymentdrivers')
	));
?>

<!-- CHARGE - Number -->

<?php
$chargeTypeSelect = $this->formFactory->createField()
	->type('select')
	->name('percentot')
	->value($payment->percentot)
	->hidden(true)
	->options([
		JHtml::fetch('select.option', 1,                                    '%'),
		JHtml::fetch('select.option', 2, VREFactory::getCurrency()->getSymbol()),
	]);

echo $this->formFactory->createField()
	->type('number')
	->name('charge')
	->value($payment->charge)
	->step('any')
	->label(JText::translate('VRMANAGEPAYMENT4'))
	->render(function($data, $input) use ($chargeTypeSelect) {
		?>
		<div class="multi-field">
			<?php echo $input; ?>
			<?php echo $chargeTypeSelect; ?>
		</div>
		<?php
	});
?>

<!-- TAXES - Select -->

<?php
echo $this->formFactory->createField()
	->name('id_tax')
	->value($payment->id_tax)
	->label(JText::translate('VRETAXFIELDSET'))
	->allowClear(true)
	->placeholder(JText::translate('VRTKCONFIGITEMOPT0'))
	->control([
		'class' => 'taxes-control',
		'style' => $payment->charge > 0 ? '' : 'display: none;',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\TaxesFieldRenderer($this->formFactory));
?>

<!-- SET CONFIRMED - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('setconfirmed')
	->checked($payment->setconfirmed)
	->label(JText::translate('VRMANAGEPAYMENT5'))
	->description(JText::translate('VRMANAGEPAYMENT5_DESC'))
	->onchange('setConfirmedValueChanged(this.checked)');
?>

<!-- SELF CONFIRMATION - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('selfconfirm')
	->checked($payment->selfconfirm)
	->label(JText::translate('VRMANAGECONFIG91'))
	->description(JText::translate('VRMANAGECONFIG91_HELP2'))
	->control([
		'class' => 'vr-confirm-field',
		'style' => $payment->setconfirmed ? '' : 'display: none;',
	]);
?>

<?php
JText::script('VRE_FILTER_SELECT_DRIVER');
?>

<script>
	(function($, w) {
		'use strict';

		w.setConfirmedValueChanged = (checked) => {
			if (checked) {
				$('.vr-confirm-field').show();
			} else {
				$('.vr-confirm-field').hide();
				$('input[name="selfconfirm"]').prop('checked', false);
			}
		}

		$(function() {
			$('#vr-file-sel').select2({
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_DRIVER'),
				allowClear: false,
				width: '100%',
			});

			$('select[name="percentot"]').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 75,
			});

			$('input[name="charge"]').on('change', function() {
				const charge = parseFloat($(this).val());

				if (!isNaN(charge) && charge > 0) {
					$('.taxes-control').show();
				} else {
					$('.taxes-control').hide();
				}
			});

			$('#vr-file-sel').on('change', w.vrPaymentGatewayChanged);
		});
	})(jQuery, window);
</script>