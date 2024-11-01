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
 * called "onDisplayViewConfigGlobalCurrency". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('CurrencyDetails');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- CURRENCY SYMBOL - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'text',
			'name'  => 'currencysymb',
			'value' => $params['currencysymb'],
			'label' => JText::translate('VRMANAGECONFIG7'),
		]);
		?>
		
		<!-- CURRENCY NAME - Text -->
		
		<?php
		echo $this->formFactory->createField([
			'type'        => 'text',
			'name'        => 'currencyname',
			'value'       => $params['currencyname'],
			'label'       => JText::translate('VRMANAGECONFIG8'),
			'description' => JText::translate('VRMANAGECONFIG8_DESC'),
		]);
		?>
		
		<!-- CURRENCY SYMBOL POSITION - Select -->

		<?php
		$self = $this;

		echo $this->formFactory->createField([
			'type'  => 'select',
			'name'  => 'symbpos',
			'class' => 'medium',
			'value' => $params['symbpos'],
			'label' => JText::translate('VRMANAGECONFIG25'),
			'options' => [
				JHtml::fetch('select.option',  1, JText::translate('VRCONFIGSYMBPOSITION1')),
				JHtml::fetch('select.option', -1, JText::translate('VRCONFIGSYMBPOSITION3')),
				JHtml::fetch('select.option',  2, JText::translate('VRCONFIGSYMBPOSITION2')),
				JHtml::fetch('select.option', -2, JText::translate('VRCONFIGSYMBPOSITION4')),
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
			$this->translations['symbpos'], $params['multilanguage']
		));
		?>

		<!-- CURRENCY DECIMAL SEPARATOR - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'text',
			'name'  => 'currdecimalsep',
			'value' => $params['currdecimalsep'],
			'label' => JText::translate('VRMANAGECONFIG51'),
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
			$this->translations['currdecimalsep'], $params['multilanguage']
		));
		?>

		<!-- CURRENCY THOUSANDS SEPARATOR - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'text',
			'name'  => 'currthousandssep',
			'value' => $params['currthousandssep'],
			'label' => JText::translate('VRMANAGECONFIG52'),
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
			$this->translations['currthousandssep'], $params['multilanguage']
		));
		?>

		<!-- CURRENCY NUMBER OF DECIMALS - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'number',
			'name'  => 'currdecimaldig',
			'value' => $params['currdecimaldig'],
			'label' => JText::translate('VRMANAGECONFIG53'),
			'step'  => 1,
			'min'   => 0,
			'max'   => 9999,
		]);
		?>

		<!-- FINAL RESULT - LABEL -->

		<?php
		echo $this->formFactory->createField()->render(function($data) {
			?>
			<span id="currency-sample-price">
				<?php echo VREFactory::getCurrency()->format(1234.56); ?>
			</span>
			<?php
		});
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalCurrency","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Currency > Currency > Details fieldset.
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

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalCurrency","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Currency > Currency tab.
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
	(function($) {
		'use strict';

		// create helper function to format the demo currency
		const formatSamplePrice = () => {
			// get currency instance
			const currency = Currency.getInstance();
		
			// update currency configuration
			currency.decimals  = $('input[name="currdecimalsep"]').val();
			currency.digits    = parseInt($('input[name="currdecimaldig"]').val());
			currency.position  = parseInt($('select[name="symbpos"]').val());
			currency.symbol    = $('input[name="currencysymb"]').val();
			currency.thousands = $('input[name="currthousandssep"]').val();

			$('#currency-sample-price').html(currency.format(1234.56));
		};

		$(function() {
			$('input[name="currencysymb"]')
				.add('input[name="currdecimalsep"]')
				.add('input[name="currthousandssep"]')
				.add('input[name="currdecimaldig"]')
				.add('select[name="symbpos"]')
				.on('change', formatSamplePrice);
		});
	})(jQuery);
</script>