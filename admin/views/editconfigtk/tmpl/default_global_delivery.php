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
 * called "onDisplayViewConfigtkGlobalDelivery". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalDelivery');

?>

<!-- DELIVERY -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRCONFIGFIELDSETDELIVERY'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- ENABLE DELIVERY SERVICE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'enabledelivery',
			'value'       => 'delivery',
			'checked'     => $params['deliveryservice'] != 0,
			'label'       => JText::translate('VRMANAGECONFIGTK3'),
			'description' => JText::translate('VRMANAGECONFIGTK3_DESC'),
			'onchange'    => 'enableDeliveryValueChanged(this)',
			'class'       => 'service-option',
		]);
		?>
		
		<!-- DELIVERY COST - Number -->

		<?php
		// create field to collect the delivery charge amount
		$deliveryChargeField = $this->formFactory->createField([
			'type'    => 'number',
			'name'    => 'dsprice',
			'value'   => $params['dsprice'],
			'min'     => 0,
			'step'    => 'any',
			'hidden'  => true,
		]);

		// create field to choose whether the delivery charge should be fixed or percentage
		$percentOrTotalField = $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'dspercentot',
			'value'   => $params['dspercentot'],
			'class'   => 'short',
			'hidden'  => true,
			'options' => [
				JHtml::fetch('select.option', 1, '%'),
				JHtml::fetch('select.option', 2, $params['currencysymb']),
			],
		]);

		// merge the previous fields together
		echo $this->formFactory->createField([
			'label'       => JText::translate('VRMANAGECONFIGTK4'),
			'description' => JText::translate('VRMANAGECONFIGTK4_DESC'),
			'control'     => [
				'class' => 'vr-delivery-child',
				'style' => $params['deliveryservice'] != 0 ? '' : 'display: none;',
			],
		])->render(function($data, $input) use ($deliveryChargeField, $percentOrTotalField) {
			?>
			<div class="multi-field width-80-20">
				<div><?php echo $deliveryChargeField; ?></div>
				<div><?php echo $percentOrTotalField; ?></div>
			</div>
			<?php
		});
		?>
		
		<!-- FREE DELIVERY WITH - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'freedelivery',
			'value'       => $params['freedelivery'],
			'label'       => JText::translate('VRMANAGECONFIGTK7'),
			'description' => JText::translate('VRMANAGECONFIGTK7_DESC'),
			'min'         => 0,
			'step'        => 'any',
			'control'     => [
				'class' => 'vr-delivery-child',
				'style' => $params['deliveryservice'] != 0 ? '' : 'display: none;',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
			'before' => $params['currencysymb'],
		]));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalDelivery","key":"delivery","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Delivery > Delivery fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['delivery']))
		{
			echo $forms['delivery'];

			// unset details form to avoid displaying it twice
			unset($forms['delivery']);
		}
		?>

	</div>

</div>

<!-- TAKEAWAY -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRTKORDERPICKUPOPTION'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- ENABLE TAKEAWAY SERVICE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'enablepickup',
			'value'       => 'pickup',
			'checked'     => $params['deliveryservice'] != 1,
			'label'       => JText::translate('VRMANAGECONFIGTK40'),
			'description' => JText::translate('VRMANAGECONFIGTK40_DESC'),
			'onchange'    => 'enablePickupValueChanged(this)',
			'class'       => 'service-option',
		]);
		?>

		<!-- TAKEAWAY CHARGE/DISCOUNT - Number -->

		<?php
		// create field to collect the takeaway charge/discount amount
		$takeawayChargeField = $this->formFactory->createField([
			'type'   => 'number',
			'name'   => 'pickupprice',
			'value'  => $params['pickupprice'],
			'step'   => 'any',
			'hidden' => true,
		]);

		// create field to choose whether the takeaway charge should be fixed or percentage
		$percentOrTotalField = $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'pickuppercentot',
			'value'   => $params['pickuppercentot'],
			'class'   => 'short',
			'hidden'  => true,
			'options' => [
				JHtml::fetch('select.option', 1, '%'),
				JHtml::fetch('select.option', 2, $params['currencysymb']),
			],
		]);

		// merge the previous fields together
		echo $this->formFactory->createField([
			'label'       => JText::translate('VRMANAGECONFIGTK18'),
			'description' => JText::translate('VRMANAGECONFIGTK18_DESC'),
			'control'     => [
				'class' => 'vr-pickup-child',
				'style' => $params['deliveryservice'] != 1 ? '' : 'display: none;',
			],
		])->render(function($data, $input) use ($takeawayChargeField, $percentOrTotalField) {
			?>
			<div class="multi-field width-80-20">
				<div><?php echo $takeawayChargeField; ?></div>
				<div><?php echo $percentOrTotalField; ?></div>
			</div>
			<?php
		});
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalDelivery","key":"takeaway","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Delivery > Takeaway fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['takeaway']))
		{
			echo $forms['takeaway'];

			// unset details form to avoid displaying it twice
			unset($forms['takeaway']);
		}
		?>

	</div>

</div>

<!-- SERVICE -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRTKORDERDELIVERYSERVICE'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- DEFAULT SERVICE - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkdefaultservice',
			'value'       => $params['tkdefaultservice'],
			'class'       => 'medium',
			'label'       => JText::translate('VRMANAGECONFIGTK31'),
			'description' => JText::translate('VRMANAGECONFIGTK31_DESC'),
			'disabled'    => $params['deliveryservice'] != 2,
			'options'     => [
				JHtml::fetch('select.option', 'delivery', JText::translate('VRTKORDERDELIVERYOPTION')),
				JHtml::fetch('select.option', 'pickup', JText::translate('VRTKORDERPICKUPOPTION')),
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalDelivery","key":"service","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Delivery > Service fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['service']))
		{
			echo $forms['service'];

			// unset details form to avoid displaying it twice
			unset($forms['service']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalDelivery","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Delivery tab.
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
?>

<script>
	(function($, w) {
		'use strict';

		const enableAtLeastOneService = (checkbox) => {
			if ($('input[type="checkbox"].service-option:checked').length) {
				// another service has been selected
				return;
			}

			// take all the fields used to toggle a service
			$('input[type="checkbox"].service-option')
				// exclude the one that has just been turned off
				.not(checkbox)
				// take the first available one
				.first()
				// turn on the checkbox
				.prop('checked', true)
				// propagate change event
				.trigger('change');
		}

		const toggleDefaultServiceStatus = () => {
			const select = $('select[name="tkdefaultservice"]');
			const checkbox = $('input[type="checkbox"].service-option:checked');

			if (checkbox.length > 1) {
				select.prop('disabled', false);
			} else {
				select.prop('disabled', true);
				select.select2('val', checkbox.val());
			}
		}

		w.enableDeliveryValueChanged = (checkbox) => {
			if (checkbox.checked) {
				$('.vr-delivery-child').show();
			} else {
				$('.vr-delivery-child').hide();
				enableAtLeastOneService(checkbox);
			}

			toggleDefaultServiceStatus();
		}

		w.enablePickupValueChanged = (checkbox) => {
			if (checkbox.checked) {
				$('.vr-pickup-child').show();
			} else {
				$('.vr-pickup-child').hide();
				enableAtLeastOneService(checkbox);
			}

			toggleDefaultServiceStatus();
		}
	})(jQuery, window);
</script>