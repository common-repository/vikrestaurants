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

$params = $this->params;

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigappSmsSettings". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('SmsSettings');

?>

<!-- GLOBAL -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMENUTITLEHEADER4'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- SMS FILE - Select -->
		
		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('smsapi')
			->value($params['smsapi'])
			->label(JText::translate('VRMANAGECONFIGSMS1'))
			->description(JText::translate('VRMANAGECONFIGSMS1_DESC'))
			->options(JHtml::fetch('vrehtml.admin.smsdrivers', $blank = ''));
		?>

		<!-- GROUP - Section -->
		<?php
		$elements = [];
		
		if ($params['enablerestaurant'])
		{ 
			$elements[] = JHtml::fetch('select.option', 0, JText::translate('VRCONFIGSMSAPIWHEN0'));
		}

		if ($params['enabletakeaway'])
		{
			$elements[] = JHtml::fetch('select.option', 1, JText::translate('VRCONFIGSMSAPIWHEN1'));
		}

		if ($params['enablerestaurant'] && $params['enabletakeaway'])
		{
			$elements[] = JHtml::fetch('select.option', 2, JText::translate('VRCONFIGSMSAPIWHEN2'));
		}

		$elements[] = JHtml::fetch('select.option', 3, JText::translate('VRCONFIGSMSAPIWHEN3'));
		
		echo $this->formFactory->createField()
			->type('select')
			->name('smsapiwhen')
			->value($params['smsapiwhen'])
			->class('medium-large')
			->label(JText::translate('VRMANAGECONFIGSMS2'))
			->description(JText::translate('VRMANAGECONFIGSMS2_DESC'))
			->options($elements);
		?>
			
		<!-- RECIPIENT - Select -->

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('smsapito')
			->value($params['smsapito'])
			->class('medium-large')
			->label(JText::translate('VRMANAGECONFIGSMS3'))
			->description(JText::translate('VRMANAGECONFIGSMS3_DESC'))
			->options([
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGSMSAPITO0')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGSMSAPITO1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGSMSAPITO2')),
			]);
		?>

		<!-- ADMIN PHONE - Tel -->

		<?php
		echo $this->formFactory->createField()
			->type('tel')
			->name('smsapiadminphone')
			->value($params['smsapiadminphone'])
			->label(JText::translate('VRMANAGECONFIGSMS4'))
			->description(JText::translate('VRMANAGECONFIGSMS4_DESC'));
		?>
			
		<!-- CREDIT - Form -->

		<?php
		try
		{
			$driver = VREApplication::getInstance()->getSmsInstance($params['smsapi']);

			if (method_exists($driver, 'estimate'))
			{
				// create button used to estimate the user credit
				$estimateButton = $this->formFactory->createField()
					->type('button')
					->id('sms-estimate-btn')
					->text(JText::translate('VRMANAGECONFIGSMS8'))
					->hidden(true);

				// create input to display the remaining balance
				$creditInput = $this->formFactory->createField()
					->type('text')
					->id('sms-remaining-balance')
					->value('/')
					->readonly(true)
					->hidden(true)
					->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
						'before' => $params['currencysymb'],
					]));

				echo $this->formFactory->createField()
					->label(JText::translate('VRMANAGECONFIGSMS7'))
					->description(JText::translate('VRMANAGECONFIGSMS7_DESC'))
					->render(function($data, $input) use ($creditInput, $estimateButton) {
						?>
						<div class="multi-field first-flex">
							<?php echo $creditInput; ?>
							<?php echo $estimateButton; ?>
						</div>
						<?php
					});
			}
		}
		catch (Exception $e)
		{
			// no SMS driver
		}
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsSettings","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the SMS > Settings > Global fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['basic']))
		{
			echo $forms['basic'];

			// unset details form to avoid displaying it twice
			unset($forms['basic']);
		}
		?>

	</div>

</div>

<!-- PARAMETERS -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGEPAYMENT8'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<div id="vr-smsapi-params-table">
			<?php
			if (!empty($params['smsapi']))
			{
				// display sms provider parameters
				echo JLayoutHelper::render('form.fields', [
					'fields' => VREApplication::getInstance()->getSmsConfig($params['smsapi']),
					'params' => json_decode($params['smsapifields'], true),
					'prefix' => 'smsparam_',
				]);
			}
			else
			{
				echo VREApplication::getInstance()->alert(JText::translate('VRMANAGEPAYMENT9'));
			}
			?>
		</div>

		<!-- CONNECTION ERROR -->

		<div id="smsapi-connection-err" style="display:none;">
			<?php echo VREApplication::getInstance()->alert(JText::translate('VRE_AJAX_GENERIC_ERROR'), 'error'); ?>
		</div>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsSettings","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the SMS > Settings tab.
 *
 * @since 1.9
 */
foreach ($forms as $formTitle => $formHtml)
{
	?>
	<div class="config-fieldset">
		
		<div class="config-fieldset-head">
			<h3><?php echo JText::translate($formTitle); ?></h3>
		</div>

		<div class="config-fieldset-body">
			<?php echo $formHtml; ?>
		</div>
		
	</div>
	<?php
}

JText::script('JGLOBAL_SELECT_AN_OPTION');
JText::script('VRE_FILTER_SELECT_DRIVER');
JText::script('VRE_AJAX_GENERIC_ERROR');
?>

<script>
	(function($) {
		'use strict';

		const renderSmsParams = () => {
			// render select
			$('#vr-smsapi-params-table select').each(function() {
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

			// init helpers
			$('#vr-smsapi-params-table .vr-quest-popover').popover({
				sanitize: false,
				container: 'body',
				trigger: 'hover focus',
				html: true,
			});
		}

		const smsDriverChanged = () => {
			const driver = $('select[name="smsapi"]').val();

			// destroy select2 
			$('#vr-smsapi-params-table select').select2('destroy');
			
			$('#vr-smsapi-params-table').html('');
			$('#smsapi-connection-err').hide();

			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=configapp.smsfields'); ?>',
				{
					driver: driver,
				},
				(html) => {
					$('#vr-smsapi-params-table').html(html);

					renderSmsParams();

					$('#vr-smsapi-params-table').trigger('smsapi.load');
				},
				(error) => {
					$('#smsapi-connection-err').show();
				}
			);
		}

		const estimateSmsCredit = () => {
			return new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=configapp.smscredit'); ?>',
					{
						driver: '<?php echo $params['smsapi']; ?>',
						phone:  $('input[name="smsapiadminphone"]').val(),
					},
					(credit) => {
						resolve(parseFloat(credit));
					},
					(error) => {
						reject(error.responseText);
					}
				);
			});
		}

		$(function() {
			const driverSelect = $('select[name="smsapi"]');

			driverSelect.select2({
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_DRIVER'),
				allowClear: true,
				width: 300,
			});

			driverSelect.on('change', () => {
				// refresh parameters
				smsDriverChanged();
			});

			<?php if (!empty($params['smsapi'])): ?>
				renderSmsParams();
			<?php endif; ?>

			$('#sms-estimate-btn').on('click', function() {
				$(this).prop('disabled', true);

				estimateSmsCredit().then((credit) => {
					if (isNaN(credit)) {
						credit = 0;
					}

					if (credit > 0) {
						$('#sms-remaining-balance').removeClass('invalid');
					} else {
						$('#sms-remaining-balance').addClass('invalid');
					}

					$('#sms-remaining-balance').val(credit.toFixed(2));
				}).catch((error) => {
					alert(error || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));

					$('#sms-remaining-balance').html('/');
				}).finally(() => {
					$(this).prop('disabled', false);
				});
			});
		});
	})(jQuery, window);
</script>