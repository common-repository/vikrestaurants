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
 * called "onDisplayViewConfigresGlobalSearch". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalSearch');

?>

<!-- DATE AND TIME -->

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

		for ($m = 0; $m < 90; $m += $params['minuteintervals'])
		{
			$dt = clone $date;
			$dt->modify('+' . $m . ' minutes');
			$hint[] = '<em>' . JHtml::fetch('date', $dt, $params['timeformat']) . '</em>';
		}

		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'minuteintervals',
			'value'       => $params['minuteintervals'],
			'class'       => 'small-medium',
			'label'       => JText::translate('VRMANAGECONFIG11'),
			'description' => JText::sprintf('VRMANAGECONFIG11_DESC', $params['minuteintervals'], implode(', ', $hint)),
			'options'     => [5, 10, 15, 20, 30, 60],
		]);
		?>

		<!-- BOOKING MINUTES RETRICTIONS - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'bookrestr',
			'value'       => $params['bookrestr'],
			'label'       => JText::translate('VRMANAGECONFIG24'),
			'description' => JText::translate('VRMANAGECONFIG24_DESC'),
			'min'         => 0,
			'step'        => 5,
		]);
		?>

		<!-- MIN DATE - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'mindate',
			'value'       => $params['mindate'],
			'label'       => JText::translate('VRMANAGECONFIG87'),
			'description' => JText::translate('VRMANAGECONFIG87_HELP'),
			'options'     => [
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
		]);
		?>

		<!-- MAX DATE - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'maxdate',
			'value'       => $params['maxdate'],
			'label'       => JText::translate('VRMANAGECONFIG88'),
			'description' => JText::translate('VRMANAGECONFIG88_HELP'),
			'options'     => [
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
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalSearch","key":"datetime","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Search > Date & Time fieldset.
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

<!-- PEOPLE -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGERESERVATION4'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- MINIMUM PEOPLE - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'minimumpeople',
			'value'       => $params['minimumpeople'],
			'label'       => JText::translate('VRMANAGECONFIG13'),
			'description' => JText::translate('VRMANAGECONFIG13_DESC'),
			'min'         => 1,
			'step'        => 1,
		]);
		?>
		
		<!-- MAXIMUM PEOPLE - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'maximumpeople',
			'value'       => $params['maximumpeople'],
			'label'       => JText::translate('VRMANAGECONFIG14'),
			'description' => JText::translate('VRMANAGECONFIG14_DESC'),
			'min'         => 1,
			'step'        => 1,
		]);
		?>
		
		<!-- LARGE PARTY LABEL - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'largepartylbl',
			'checked'     => $params['largepartylbl'],
			'label'       => JText::translate('VRMANAGECONFIG48'),
			'description' => JText::translate('VRMANAGECONFIG48_DESC'),
			'onchange'    => 'largePartyLabelValueChanged(this.checked)',
		]);
		?>
		
		<!-- LARGE PARTY URL - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'text',
			'name'        => 'largepartyurl',
			'value'       => $params['largepartyurl'],
			'label'       => JText::translate('VRMANAGECONFIG49'),
			'description' => JText::translate('VRMANAGECONFIG49_DESC'),
			'control'     => [
				'class' => 'vr-largeparty-child',
				'style' => $params['largepartylbl'] ? '' : 'display: none;',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
			$this->translations['largepartyurl'], $params['multilanguage']
		));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalSearch","key":"people","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Search > People fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['people']))
		{
			echo $forms['people'];

			// unset details form to avoid displaying it twice
			unset($forms['people']);
		}
		?>

	</div>

</div>

<!-- TABLES -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMENUTABLES'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- RESERVATION REQUIREMENTS - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'reservationreq',
			'value'       => $params['reservationreq'],
			'class'       => 'medium-large',
			'label'       => JText::translate('VRMANAGECONFIG16'),
			'description' => JText::translate('VRMANAGECONFIG16_DESC'),
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGRESREQ0')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGRESREQ1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGRESREQ2')),
			],
		]);
		?>

	</div>

</div>

<!-- SAFETY -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_SAFETY_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">
		
		<!-- SAFE DISTANCE - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'safedistance',
			'checked'     => $params['safedistance'],
			'label'       => JText::translate('VRMANAGECONFIG92'),
			'description' => JText::translate('VRMANAGECONFIG92_HELP'),
			'onchange'    => 'safeDistanceValueChanged(this.checked)',
		]);
		?>

		<!-- SAFE FACTOR - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'safefactor',
			'value'       => $params['safefactor'],
			'label'       => JText::translate('VRMANAGECONFIG93'),
			'description' => JText::sprintf('VRMANAGECONFIG93_HELP', ceil(4 * $params['safefactor']), 4, $params['safefactor']),
			'min'         => 1,
			'step'        => 'any',
			'control'     => [
				'class' => 'vr-safedistance-child',
				'style' => $params['safedistance'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalSearch","key":"safety","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Search > Safety fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['safety']))
		{
			echo $forms['safety'];

			// unset details form to avoid displaying it twice
			unset($forms['safety']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalSearch","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Food tab.
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

		w.safeDistanceValueChanged = (checked) => {
			if (checked) {
				$('.vr-safedistance-child').show();
			} else {
				$('.vr-safedistance-child').hide();
			}
		}

		w.largePartyLabelValueChanged = (checked) => {
			if (checked) {
				$('.vr-largeparty-child').show();
			} else {
				$('.vr-largeparty-child').hide();
			}
		}

		$(function($) {
			$('select[name="mindate"],select[name="maxdate"]').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRMANAGECONFIG32'),
				allowClear: true,
				width: 150,
			});
		})
	})(jQuery, window);
</script>