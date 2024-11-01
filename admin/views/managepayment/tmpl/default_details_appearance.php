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

<!-- ICON - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('icontype')
	->value($payment->icontype)
	->id('vr-icontype-sel')
	->label(JText::translate('VRMANAGEPAYMENT12'))
	->options([
		JHtml::fetch('select.option', '',                            ''),
		JHtml::fetch('select.option',  1, JText::translate('VRPAYMENTICONOPT1')),
		JHtml::fetch('select.option',  2, JText::translate('VRPAYMENTICONOPT2')),
	]);
?>

<!-- FONT ICON - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('font_icon')
	->value($payment->icontype == 1 ? $payment->icon : null)
	->hiddenLabel(true)
	->options([
		JHtml::fetch('select.option', '', ''),
		JHtml::fetch('select.option', 'fab fa-paypal',                    'PayPal'),
		JHtml::fetch('select.option', 'fab fa-cc-paypal',              'PayPal #2'),
		JHtml::fetch('select.option', 'fas fa-credit-card',          'Credit Card'),
		JHtml::fetch('select.option', 'far fa-credit-card',       'Credit Card #2'),
		JHtml::fetch('select.option', 'fab fa-cc-visa',                     'Visa'),
		JHtml::fetch('select.option', 'fab fa-cc-mastercard',         'Mastercard'),
		JHtml::fetch('select.option', 'fab fa-cc-amex',         'American Express'),
		JHtml::fetch('select.option', 'fab fa-cc-discover',            'Discovery'),
		JHtml::fetch('select.option', 'fab fa-cc-jcb',                       'JCB'),
		JHtml::fetch('select.option', 'fab fa-cc-diners-club',       'Diners Club'),
		JHtml::fetch('select.option', 'fab fa-stripe',                    'Stripe'),
		JHtml::fetch('select.option', 'fab fa-cc-stripe',              'Stripe #2'),
		JHtml::fetch('select.option', 'fab fa-stripe-s',              'Stripe (S)'),
		JHtml::fetch('select.option', 'fas fa-euro-sign',                   'Euro'),
		JHtml::fetch('select.option', 'fas fa-dollar-sign',               'Dollar'),
		JHtml::fetch('select.option', 'fas fa-pound-sign',                 'Pound'),
		JHtml::fetch('select.option', 'fas fa-yen-sign',                     'Yen'),
		JHtml::fetch('select.option', 'fas fa-won-sign',                     'Won'),
		JHtml::fetch('select.option', 'fas fa-rupee-sign',                 'Rupee'),
		JHtml::fetch('select.option', 'fas fa-ruble-sign',                 'Ruble'),
		JHtml::fetch('select.option', 'fas fa-lira-sign',                   'Lira'),
		JHtml::fetch('select.option', 'fas fa-shekel-sign',               'Shekel'),
		JHtml::fetch('select.option', 'fas fa-money-bill',                 'Money'),
		JHtml::fetch('select.option', 'fas fa-money-bill-wave',         'Money #2'),
		JHtml::fetch('select.option', 'fas fa-money-check-alt',         'Money #3'),
	])
	->control([
		'class' => 'vr-fonticon-child',
		'style' => $payment->icontype == 1 ? '' : 'display: none;'
	]);
?>

<!-- IMAGE - Image -->

<?php
echo $this->formFactory->createField()
	->name('upload_icon')
	->value($payment->icontype == 2 ? $payment->icon : '')
	->hiddenLabel(true)
	->control([
		'class' => 'vr-uploadimage-child',
		'style' => $payment->icontype == 2 ? '' : 'display: none;'
	])
	->render(function($data, $input) {
		return VREApplication::getInstance()->getMediaField($data->get('name'), $data->get('value'));
	});
?>

<!-- POSITION - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('position')
	->value($payment->position)
	->label(JText::translate('VRMANAGEPAYMENT13'))
	->description(JText::translate('VRMANAGEPAYMENT13_DESC'))
	->options([
		JHtml::fetch('select.option',                           '',                           ''),
		JHtml::fetch('select.option',    'vr-payment-position-top', JText::translate('VRPAYMENTPOSOPT2')),
		JHtml::fetch('select.option', 'vr-payment-position-bottom', JText::translate('VRPAYMENTPOSOPT3')),
	]);
?>

<?php
JText::script('VRPAYMENTICONOPT0');
JText::script('VRPAYMENTPOSOPT1');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('select[name="position"]').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRPAYMENTPOSOPT1'),
				allowClear: true,
				width: '90%',
			});

			$('#vr-icontype-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: true,
				placeholder: Joomla.JText._('VRPAYMENTICONOPT0'),
				width: '90%',
			});

			$('select[name="font_icon"]').select2({
				placeholder: '--',
				allowClear: false,
				width: '90%',
				formatResult: (opt) => {
					// Use a minimum width for the icons shown within the dropdown options
					// in order to have the texts properly aligned.
					// At the moment, the largest width of the icon seems to be 17px.
					return '<i class="' + opt.id + '" style="min-width:18px;"></i> ' + opt.text;
				},
				formatSelection: (opt) => {
					// Do not use a minimum width for the icon shown within the selection label.
					// Here we don't need to have a large space between the icon and the text.
					return '<i class="' + opt.id + '"></i> ' + opt.text;
				},
			});

			$('#vr-icontype-sel').on('change', function() {
				var val = $(this).val();

				if (val == 1) {
					$('.vr-fonticon-child').show();
					$('.vr-uploadimage-child').hide();
				} else if (val == 2) {
					$('.vr-fonticon-child').hide();
					$('.vr-uploadimage-child').show();
				} else {
					$('.vr-fonticon-child').hide();
					$('.vr-uploadimage-child').hide();
				}
			});
		});
	})(jQuery);
</script>