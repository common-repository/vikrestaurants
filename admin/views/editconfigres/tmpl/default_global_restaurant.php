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

// fetch all the restaurant status codes suitable for the default status setting
$statusCodes = JHtml::fetch('vrehtml.status.find', ['code', 'name', 'approved'], ['restaurant' => 1, 'reserved' => 1]);

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigresGlobalRestaurant". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalRestaurant');

?>

<!-- SYSTEM -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGECONFIGGLOBSECTION1'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- DISPLAY ON DASHBOARD - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'ondashboard',
			'checked'     => $params['ondashboard'],
			'label'       => JText::translate('VRMANAGECONFIG36'),
			'description' => JText::translate('VRMANAGECONFIG36_DESC'),
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalRestaurant","key":"system","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Restaurant > System fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['system']))
		{
			echo $forms['system'];

			// unset details form to avoid displaying it twice
			unset($forms['system']);
		}
		?>

	</div>

</div>

<!-- RESERVATIONS -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMENURESERVATIONS'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- DEFAULT STATUS - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'defstatus',
			'value'       => $params['defstatus'],
			'class'       => 'medium',
			'label'       => JText::translate('VRMANAGECONFIG35'),
			'description' => JText::sprintf('VRMANAGECONFIG35_DESC', JHtml::fetch('vrehtml.status.pending', 'restaurant')->name),
			'options'     => array_map(function($status) {
				return JHtml::fetch('select.option', $status->code, $status->name);
			}, $statusCodes),
		]);
		?>

		<!-- SELF CONFIRMATION - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'selfconfirm',
			'checked'     => $params['selfconfirm'],
			'label'       => JText::translate('VRMANAGECONFIG91'),
			'description' => JText::translate('VRMANAGECONFIG91_HELP'),
			'control'     => [
				'class' => 'vr-defstatus-child',
				'style' => JHtml::fetch('vrehtml.status.isapproved', 'restaurant', $params['defstatus']) ? 'display: none;' : '',
			],
		]);
		?>

		<!-- AVERAGE TIME STAY - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'averagetimestay',
			'value'       => $params['averagetimestay'],
			'label'       => JText::translate('VRMANAGECONFIG12'),
			'description' => JText::translate('VRMANAGECONFIG12_DESC'),
			'min'         => 5,
			'step'        => 5,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
		?>

		<!-- TABLES LOCKED FOR - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'tablocktime',
			'value'       => $params['tablocktime'],
			'label'       => JText::translate('VRMANAGECONFIG20'),
			'description' => JText::translate('VRMANAGECONFIG20_DESC'),
			'min'         => 5,
			'step'        => 5,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
		?>

		<!-- LOGIN REQUIREMENTS - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'loginreq',
			'value'       => $params['loginreq'],
			'class'       => 'medium',
			'label'       => JText::translate('VRMANAGECONFIG33'),
			'description' => JText::translate('VRMANAGECONFIG33_DESC'),
			'options'     => [
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGLOGINREQ1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGLOGINREQ2')),
				JHtml::fetch('select.option', 3, JText::translate('VRCONFIGLOGINREQ3')),
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalRestaurant","key":"reservations","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Restaurant > Reservations fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['reservations']))
		{
			echo $forms['reservations'];

			// unset details form to avoid displaying it twice
			unset($forms['reservations']);
		}
		?>

	</div>

</div>

<!-- DEPOSIT -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGERESERVATION9'); ?></h3>
	</div>

	<div class="config-fieldset-body">
		
		<!-- ASK FOR DEPOSIT - Select + Number -->

		<?php
		$askDepositThresholdField = $this->formFactory->createField([
			'type'  => 'number',
			'name'  => 'askdeposit',
			'value' => $params['askdeposit'],
			'min'   => min(2, $params['askdeposit']),
			'step'  => 1,
			// display only the field without the label
			'hiddenLabel' => true,
			// this field appears only in case the "askdeposit" setting is equals or higher than 2
			'control' => [
				'class' => 'ask-deposit-child',
				'style' => $params['askdeposit'] < 2 ? 'display: none;' : '',
			],
		]);

		echo $this->formFactory->createField([
			'type'        => 'select',
			'id'          => 'vr-askdeposit-sel',
			'value'       => min(2, $params['askdeposit']),
			'class'       => 'small-medium',
			'label'       => JText::translate('VRMANAGECONFIG89'),
			'description' => JText::translate('VRMANAGECONFIG89_HELP'),
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGLOGINREQ1')),
				JHtml::fetch('select.option', 1, JText::translate('VRTKCONFIGOVERLAYOPT2')),
				JHtml::fetch('select.option', 2, JText::translate('VRPEOPLEALLOPT2')),
			],
		])->render(function($data, $input) use ($askDepositThresholdField) {
			?>
			<div class="multi-field width-50">
				<?php
				// display the select first
				echo $input;

				// then display the deposit threshold (people)
				echo $askDepositThresholdField->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(
					strtolower(JText::translate('VRORDERPEOPLE'))
				));
				?>
			</div>
			<?php
		});
		?>
		
		<!-- RESERVATION DEPOSIT - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'resdeposit',
			'value'       => $params['resdeposit'],
			'label'       => JText::translate('VRMANAGECONFIG18'),
			'description' => JText::translate('VRMANAGECONFIG18_DESC'),
			'min'         => 0,
			'step'        => 'any',
			'control'     => [
				'class' => 'vr-deposit-child',
				'style' => $params['askdeposit'] < 1 ? 'display: none;' : '',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
			'before' => $params['currencysymb'],
		]));
		?>
		
		<!-- COST PER PERSON - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'costperperson',
			'checked'     => $params['costperperson'],
			'label'       => JText::translate('VRMANAGECONFIG19'),
			'description' => JText::translate('VRMANAGECONFIG19_DESC'),
			'control'     => [
				'class' => 'vr-deposit-child',
				'style' => $params['askdeposit'] < 1 ? 'display: none;' : '',
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalRestaurant","key":"deposit","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Restaurant > Deposit fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['deposit']))
		{
			echo $forms['deposit'];

			// unset details form to avoid displaying it twice
			unset($forms['deposit']);
		}
		?>

	</div>

</div>

<!-- CANCELLATION -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_CANCELLATION_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- ENABLE CANCELLATION - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'enablecanc',
			'checked'     => $params['enablecanc'],
			'label'       => JText::translate('VRMANAGECONFIG40'),
			'description' => JText::translate('VRMANAGECONFIG40_DESC'),
			'onchange'    => 'cancellationValueChanged(this.checked)',
		]);
		?>

		<!-- CANCELLATION REASON - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'cancreason',
			'value'       => $params['cancreason'],
			'class'       => 'medium-large',
			'label'       => JText::translate('VRMANAGECONFIG68'),
			'description' => JText::translate('VRMANAGECONFIG68_DESC'),
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGCANCREASONOPT0')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGCANCREASONOPT1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGCANCREASONOPT2')),
			],
			'control'     => [
				'class' => 'vr-enablecanc-child',
				'style' => $params['enablecanc'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- ACCEPT CANCELLATION BEFORE - Number -->

		<?php
		$cancUnitField = $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'cancunit',
			'value'   => $params['cancunit'],
			'class'   => 'short',
			'hidden'  => true,
			'options' => [
				'days'  => JText::translate('VRFORMATDAYS'),
				'hours' => JText::translate('VRFORMATHOURS'),
			],
		]);

		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'canctime',
			'value'       => $params['canctime'],
			'label'       => JText::translate('VRMANAGECONFIG41'),
			'description' => JText::translate('VRMANAGECONFIG41_HELP'),
			'min'         => 0,
			'step'        => 1,
			'control'     => [
				'class' => 'vr-enablecanc-child',
				'style' => $params['enablecanc'] ? '' : 'display: none;',
			],
		])->render(function($data, $input) use ($cancUnitField) {
			?>
			<div class="multi-field width-50">
				<?php
				// display the input first
				echo $input;

				// then display the cancellation unit
				echo $cancUnitField->render();
				?>
			</div>
			<?php
		});
		?>

		<!-- ACCEPT CANCELLATION WITHIN - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'cancmins',
			'value'       => $params['cancmins'],
			'label'       => JText::translate('VRMANAGECONFIG90'),
			'description' => JText::translate('VRMANAGECONFIG90_HELP'),
			'min'         => 0,
			'step'        => 1,
			'control'     => [
				'class' => 'vr-enablecanc-child',
				'style' => $params['enablecanc'] ? '' : 'display: none;',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalRestaurant","key":"cancellation","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Restaurant > Cancellation fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['cancellation']))
		{
			echo $forms['cancellation'];

			// unset details form to avoid displaying it twice
			unset($forms['cancellation']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalRestaurant","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Restaurant tab.
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

		const statusCodes = <?php echo json_encode($statusCodes); ?>;

		w.cancellationValueChanged = (checked) => {
			if (checked) {
				$('.vr-enablecanc-child').show();
			} else {
				$('.vr-enablecanc-child').hide();
			}
		}

		$(function() {
			// handle self confirmation
			$('select[name="defstatus"]').on('change', function() {
				const val = $(this).val();

				let code = statusCodes.filter((data) => {
					return data.code === val;
				});

				if (code.length && code[0].approved == 0) {
					$('.vr-defstatus-child').show();
				} else {
					$('.vr-defstatus-child').hide();
				}
			});

			// handle ask deposit
			$('#vr-askdeposit-sel').on('change', function() {
				const value = parseInt($(this).val());

				$('input[name="askdeposit"]').attr('min', value).val(value);

				if (value > 0) {
					$('.vr-deposit-child').show();
				} else {
					$('.vr-deposit-child').hide();
				}

				if (value > 1) {
					$('.ask-deposit-child').show();
				} else {
					$('.ask-deposit-child').hide();
				}
			});
		})
	})(jQuery, window);
</script>