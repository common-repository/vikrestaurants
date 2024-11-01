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
 * called "onDisplayViewConfigtkGlobalPurchase". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalPurchase');

?>

<!-- DATE & TIME -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_DATETIME_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- MINUTES INTERVALS - Select -->

		<?php
		$hint = [];

		$date = JFactory::getDate('now');
		$date->modify($date->format('H') . ':00');

		for ($m = 0; $m < 90; $m += $params['tkminint'])
		{
			$dt = clone $date;
			$dt->modify('+' . $m . ' minutes');
			$hint[] = '<em>' . JHtml::fetch('date', $dt, $params['timeformat']) . '</em>';
		}

		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkminint',
			'value'       => $params['tkminint'],
			'class'       => 'small-medium',
			'label'       => JText::translate('VRMANAGECONFIG11'),
			'description' => JText::sprintf('VRMANAGECONFIG11_DESC', $params['tkminint'], implode(', ', $hint)),
			'options'     => [5, 10, 15, 20, 30, 60],
		]);
		?>

		<!-- SOONEST DELIVERY AFTER - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'asapafter',
			'value'       => $params['asapafter'],
			'label'       => JText::translate('VRMANAGECONFIG24'),
			'description' => JText::translate('VRMANAGECONFIG24_DESC'),
			'step'        => 1,
			'min'         => 30,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
		?>	

		<!-- ALLOW DATE SELECTION - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkallowdate',
			'checked'     => $params['tkallowdate'],
			'label'       => JText::translate('VRMANAGECONFIGTK26'),
			'description' => JText::translate('VRMANAGECONFIGTK26_HELP'),
			'onchange'    => 'allowDateValueChanged(this.checked)',
		]);
		?>

		<!-- LIVE ORDERS - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkwhenopen',
			'checked'     => $params['tkwhenopen'],
			'label'       => JText::translate('VRMANAGECONFIGTK27'),
			'description' => JText::translate('VRMANAGECONFIGTK27_HELP'),
			'onchange'    => 'whenOpenValueChanged(this.checked)',
			'control'     => [
				'class' => 'vr-allowdate-child-off',
				'style' => $params['tkallowdate'] ? 'display: none;' : '',
			],
		]);
		?>

		<!-- PRE ORDERS - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkpreorder',
			'checked'     => $params['tkpreorder'],
			'label'       => JText::translate('VRMANAGECONFIGTK38'),
			'description' => JText::translate('VRMANAGECONFIGTK38_HELP'),
			'control'     => [
				'class' => 'vr-whenopen-child-off',
				'style' => $params['tkwhenopen'] ? 'display: none;' : '',
			],
		]);
		?>

		<!-- MIN DATE - Select -->

		<?php
		echo $this->formFactory->createField([
			'type' => 'select',
			'name' => 'tkmindate',
			'value' => $params['tkmindate'],
			'label' => JText::translate('VRMANAGECONFIGTK35'),
			'description' => JText::translate('VRMANAGECONFIGTK35_HELP'),
			'options' => [
				JHtml::fetch('select.option', '',                              ''),
				JHtml::fetch('select.option',  1, JText::plural( 'VRE_N_DAYS', 1)),
				JHtml::fetch('select.option',  2, JText::plural( 'VRE_N_DAYS', 2)),
				JHtml::fetch('select.option',  3, JText::plural( 'VRE_N_DAYS', 3)),
				JHtml::fetch('select.option',  4, JText::plural( 'VRE_N_DAYS', 4)),
				JHtml::fetch('select.option',  5, JText::plural( 'VRE_N_DAYS', 5)),
				JHtml::fetch('select.option',  6, JText::plural( 'VRE_N_DAYS', 6)),
				JHtml::fetch('select.option',  7, JText::plural('VRE_N_WEEKS', 1)),
				JHtml::fetch('select.option', 14, JText::plural('VRE_N_WEEKS', 2)),
			],
			'control' => [
				'class' => 'vr-allowdate-child-on',
				'style' => $params['tkallowdate'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- MAX DATE - Select -->

		<?php
		echo $this->formFactory->createField([
			'type' => 'select',
			'name' => 'tkmaxdate',
			'value' => $params['tkmaxdate'],
			'label' => JText::translate('VRMANAGECONFIGTK36'),
			'description' => JText::translate('VRMANAGECONFIGTK36_HELP'),
			'options' => [
				JHtml::fetch('select.option', '',                               ''),
				JHtml::fetch('select.option',  1, JText::plural(  'VRE_N_DAYS', 1)),
				JHtml::fetch('select.option',  2, JText::plural(  'VRE_N_DAYS', 2)),
				JHtml::fetch('select.option',  3, JText::plural(  'VRE_N_DAYS', 3)),
				JHtml::fetch('select.option',  4, JText::plural(  'VRE_N_DAYS', 4)),
				JHtml::fetch('select.option',  5, JText::plural(  'VRE_N_DAYS', 5)),
				JHtml::fetch('select.option',  6, JText::plural(  'VRE_N_DAYS', 6)),
				JHtml::fetch('select.option',  7, JText::plural( 'VRE_N_WEEKS', 1)),
				JHtml::fetch('select.option', 14, JText::plural( 'VRE_N_WEEKS', 2)),
				JHtml::fetch('select.option', 30, JText::plural('VRE_N_MONTHS', 1)),
				JHtml::fetch('select.option', 60, JText::plural('VRE_N_MONTHS', 2)),
			],
			'control' => [
				'class' => 'vr-allowdate-child-on',
				'style' => $params['tkallowdate'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalPurchase","key":"datetime","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Purchase > Date & Time fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['datetime']))
		{
			echo $forms['datetime'];

			// unset details form to avoid displaying it twice
			unset($forms['datetime']);
		}
		?>

	</div>

</div>

<!-- CART -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRTKORDERCARTFIELDSET3'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- MIN COST PER ORDER - Number -->

		<?php
		echo $this->formFactory->createField()
			->type('number')
			->name('mincostperorder')
			->value($params['mincostperorder'])
			->label(JText::translate('VRMANAGECONFIGTK5'))
			->description(JText::translate('VRMANAGECONFIGTK5_DESC'))
			->min(0)
			->step('any')
			->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
				'before' => $params['currencysymb'],
			]));
		?>

		<!-- MAX ITEMS IN CART - Number -->

		<?php
		echo $this->formFactory->createField()
			->type('number')
			->name('tkmaxitems')
			->value($params['tkmaxitems'])
			->label(JText::translate('VRMANAGECONFIGTK25'))
			->description(JText::translate('VRMANAGECONFIGTK25_DESC'))
			->min(1)
			->step(1);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalPurchase","key":"cart","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Purchase > Cart fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['cart']))
		{
			echo $forms['cart'];

			// unset details form to avoid displaying it twice
			unset($forms['cart']);
		}
		?>

	</div>

</div>

<!-- AVAILABILITY -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_AVAIL_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- ORDERS PER INTERVAL - Number + Select -->

		<?php
		$intervalOrdersSelect = $this->formFactory->createField()
			->type('select')
			->name('tkordmaxser')
			->value($params['tkordmaxser'])
			->class('small-medium')
			->hidden(true)
			->options([
				JHtml::fetch('select.option', 0, JText::translate('VRTKORDERPICKUPOPTION')),
				JHtml::fetch('select.option', 1, JText::translate('VRTKORDERDELIVERYOPTION')),
				JHtml::fetch('select.option', 2, JText::translate('VRTKCONFIGOVERLAYOPT2')),
			]);

		echo $this->formFactory->createField()
			->type('number')
			->name('tkordperint')
			->value($params['tkordperint'])
			->label(JText::translate('VRMANAGECONFIGTK39'))
			->description(JText::translate('VRMANAGECONFIGTK39_HELP'))
			->min(0)
			->step(1)
			->render(function($data, $input) use ($intervalOrdersSelect) {
				?>
				<div class="multi-field width-50">
					<?php echo $input; ?>
					<?php echo $intervalOrdersSelect; ?>
				</div>
				<?php
			});
		?>

		<!-- MEALS PER INTERVAL - Number -->

		<?php
		echo $this->formFactory->createField()
			->type('number')
			->name('mealsperint')
			->value($params['mealsperint'])
			->label(JText::translate('VRMANAGECONFIGTK2'))
			->description(JText::translate('VRMANAGECONFIGTK2_DESC'))
			->min(1)
			->step(1);
		?>

		<!-- TK MEALS SLOTS BACKWARD - Select -->

		<?php
		$options = [
			JHtml::fetch('select.option', '', ''),
		];

		for ($i = 1; $i <= 10; $i++)
		{
			$options[] = JHtml::fetch('select.option', $i, $i);
		}

		echo $this->formFactory->createField()
			->type('select')
			->name('tkmealsbackslots')
			->value($params['tkmealsbackslots'])
			->label(JText::translate('VRMANAGECONFIGTK34'))
			->description(JText::translate('VRMANAGECONFIGTK34_HELP'))
			->options($options)
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalPurchase","key":"availability","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Purchase > Availability fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['availability']))
		{
			echo $forms['availability'];

			// unset details form to avoid displaying it twice
			unset($forms['availability']);
		}
		?>

	</div>

</div>

<!-- FOOD -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRCONFIGFIELDSETFOOD'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- USE OVERLAY - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkuseoverlay',
			'value'       => $params['tkuseoverlay'],
			'class'       => 'medium-large',
			'label'       => JText::translate('VRMANAGECONFIGTK28'),
			'description' => JText::translate('VRMANAGECONFIGTK28_DESC'),
			'options'     => [
				JHtml::fetch('select.option', 2, JText::translate('VRTKCONFIGOVERLAYOPT2')),
				JHtml::fetch('select.option', 1, JText::translate('VRTKCONFIGOVERLAYOPT1')),
				JHtml::fetch('select.option', 0, JText::translate('VRTKCONFIGOVERLAYOPT0')),
			],
		]);
		?>

		<!-- ENABLE STOCK SYSTEM - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkenablestock',
			'checked'     => $params['tkenablestock'],
			'label'       => JText::translate('VRMANAGECONFIGTK16'),
			'description' => JText::translate('VRMANAGECONFIGTK16_DESC'),
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalPurchase","key":"food","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Purchase > Food fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['food']))
		{
			echo $forms['food'];

			// unset details form to avoid displaying it twice
			unset($forms['food']);
		}
		?>

	</div>

</div>

<!-- GRATUITY -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_GRATUITY_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- ENABLE GRATUITY - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkenablegratuity',
			'checked'     => $params['tkenablegratuity'],
			'label'       => JText::translate('VRMANAGECONFIGTK32'),
			'description' => JText::translate('VRMANAGECONFIGTK32_HELP'),
			'onchange'    => 'enableGratuityValueChanged(this.checked)',
		]);
		?>

		<!-- SUGGESTED GRATUITY - Number -->

		<?php
		$def_gratuity  = 0;
		$gratuity_type = 1;

		if (preg_match("/^([\d.,]+):([12])$/", $params['tkdefgratuity'], $matches))
		{
			$def_gratuity  = (float) $matches[1];
			$gratuity_type = (int) $matches[2];
		}

		// create field to define the default gratuity amount
		$tipAmounField = $this->formFactory->createField([
			'type'   => 'number',
			'name'   => 'tkdefgrat_amount',
			'value'  => $def_gratuity,
			'min'    => 0,
			'step'   => 'any',
			'hidden' => true,
		]);

		// create field to choose whether the tip amount should be fixed or percentage
		$percentOrTotalField = $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'tkdefgrat_percentot',
			'value'   => $gratuity_type,
			'class'   => 'short',
			'hidden'  => true,
			'options' => [
				JHtml::fetch('select.option', 1, '%'),
				JHtml::fetch('select.option', 2, $params['currencysymb']),
			],
		]);

		// merge the previous fields together
		echo $this->formFactory->createField([
			'label'       => JText::translate('VRMANAGECONFIGTK33'),
			'description' => JText::translate('VRMANAGECONFIGTK33_DESC'),
			'control'     => [
				'class' => 'vr-tip-child',
				'style' => $params['tkenablegratuity'] != 0 ? '' : 'display: none;',
			],
		])->render(function($data, $input) use ($tipAmounField, $percentOrTotalField) {
			?>
			<div class="multi-field width-80-20">
				<div><?php echo $tipAmounField; ?></div>
				<div><?php echo $percentOrTotalField; ?></div>
			</div>
			<?php
		});
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalPurchase","key":"gratuity","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Purchase > Gratuity fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['gratuity']))
		{
			echo $forms['gratuity'];

			// unset details form to avoid displaying it twice
			unset($forms['gratuity']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalPurchase","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Purchase tab.
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

JText::script('VRMANAGECONFIG32');
?>

<script>
	(function($, w) {
		'use strict';

		w.enableGratuityValueChanged = (checked) => {
			if (checked) {
				$('.vr-tip-child').show();
			} else {
				$('.vr-tip-child').hide();
			}
		}

		w.allowDateValueChanged = (checked) => {
			if (checked) {
				$('.vr-allowdate-child-on').show();
				$('.vr-allowdate-child-off').hide();
				$('input[name="tkwhenopen"]').prop('checked', false).trigger('change');
			} else {
				$('.vr-allowdate-child-on').hide();
				$('.vr-allowdate-child-off').show();
			}
		}

		w.whenOpenValueChanged = (checked) => {
			if (checked) {
				$('.vr-whenopen-child-off').hide();
			} else {
				$('.vr-whenopen-child-off').show();
			}
		}

		$(function() {
			$('select[name="tkmealsbackslots"],select[name="tkmindate"],select[name="tkmaxdate"]').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRMANAGECONFIG32'),
				allowClear: true,
				width: 150,
			});
		});
	})(jQuery, window);
</script>