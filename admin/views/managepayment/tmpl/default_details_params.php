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

$vik = VREApplication::getInstance();

?>

<!-- PARAMETERS -->

<div class="vikpayparamdiv">
	<?php
	if ($payment->file)
	{
		// display payment driver parameters
		echo JLayoutHelper::render('form.fields', [
			'fields' => VREFactory::getPlatform()->getPaymentFactory()->getConfigurationForm($payment->file),
			'params' => $payment->params,
			'prefix' => 'gp_',
		]);
	}
	else
	{
		echo $vik->alert(JText::translate('VRMANAGEPAYMENT9'));
	}
	?>
</div>

<!-- CONNECTION ERROR -->

<div id="vikparamerr" style="display: none;">
	<?php echo $vik->alert(JText::translate('VRE_AJAX_GENERIC_ERROR'), 'error'); ?>
</div>

<?php
JText::script('JGLOBAL_SELECT_AN_OPTION');
?>

<script>
	(function($, w) {
		'use strict';

		w['vrRenderPaymentParams'] = () => {
			// render select
			$('.vikpayparamdiv select').each(function() {
				let option = $(this).find('option').first();

				let data = {
					// disable search for select with 3 or lower options
					minimumResultsForSearch: $(this).find('option').length > 3 ? 0 : -1,
					// allow clear selection in case the value of the first option is empty
					allowClear: option.val() || $(this).hasClass('required') ? false : true,
					// take the whole space
					width: '90%',
				};

				if (!option.val()) {
					// set placeholder by using the option text
					data.placeholder = option.text() || Joomla.JText._('JGLOBAL_SELECT_AN_OPTION');
					// unset the text from the option for a correct rendering
					option.text('');
				}

				$(this).select2(data);
			});

			// register form fields for validation
			w.validator.registerFields('.vikpayparamdiv .required');

			// init helpers
			$('.vikpayparamdiv .vr-quest-popover').popover({
				sanitize: false,
				container: 'body',
				trigger: 'hover focus',
				html: true,
			});
		}

		w['vrPaymentGatewayChanged'] = () => {
			const gp = $('#vr-file-sel').val();

			// destroy select2 
			$('.vikpayparamdiv select').select2('destroy');
			// unregister form fields
			w.validator.unregisterFields('.vikpayparamdiv .required');
			
			$('.vikpayparamdiv').html('');
			$('#vikparamerr').hide();

			UIAjax.do(
				'<?php echo $vik->ajaxUrl('index.php?option=com_vikrestaurants&task=payment.driverfields'); ?>',
				{
					driver: gp,
					id: <?php echo (int) $payment->id; ?>,
				},
				(html) => {
					$('.vikpayparamdiv').html(html);

					w.vrRenderPaymentParams();

					$('.vikpayparamdiv').trigger('payment.load');
				},
				(error) => {
					$('#vikparamerr').show();
				}
			);
		}

		$(function() {
			<?php if ($payment->file): ?>
				// wait until the form validator is ready
				onInstanceReady(() => {
					if (typeof w.validator === 'undefined') {
						return false;
					}

					return w.validator;
				}).then((validator) => {
					vrRenderPaymentParams();
				});
			<?php endif; ?>
		});
	})(jQuery, window);
</script>