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
 * called "onDisplayViewConfigGlobalSystem". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalSystem');

?>

<!-- SYSTEM -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGECONFIGGLOBSECTION1'); ?></h3>
	</div>

	<div class="config-fieldset-body">
	
		<!-- RESTAURANT NAME - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'text',
			'name'  => 'restname',
			'value' => $params['restname'],
			'label' => JText::translate('VRMANAGECONFIG0'),
		]);
		?>
		
		<!-- LOGO IMAGE - Media -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'media',
			'name'  => 'companylogo',
			'value' => $params['companylogo'],
			'label' => JText::translate('VRMANAGECONFIG4'),
		]);
		?>

		<!-- ENABLE RESTAURANT - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'    => 'checkbox',
			'name'    => 'enablerestaurant',
			'checked' => $params['enablerestaurant'],
			'label'   => JText::translate('VRMANAGECONFIG54'),
		]);
		?>

		<!-- ENABLE TAKEAWAY - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'    => 'checkbox',
			'name'    => 'enabletakeaway',
			'checked' => $params['enabletakeaway'],
			'label'   => JText::translate('VRMANAGECONFIGTK0'),
		]);
		?>
		
		<!-- ENABLE MULTILANGUAGE - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'multilanguage',
			'checked'     => $params['multilanguage'],
			'label'       => JText::translate('VRMANAGECONFIG50'),
			'description' => JText::translate('VRMANAGECONFIG50_DESC'),
			'onchange'    => 'multilangValueChanged(this.checked)',
		]);
		?>
		
		<!-- DISPLAY FOOTER - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'showfooter',
			'checked'     => $params['showfooter'],
			'label'       => JText::translate('VRMANAGECONFIG23'),
			'description' => JText::translate('VRMANAGECONFIG23_DESC'),
		]);
		?>

		<!-- DASHBOARD REFRESH TIME - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'refreshdash',
			'value'       => $params['refreshdash'],
			'label'       => JText::translate('VRMANAGECONFIG37'),
			'description' => JText::translate('VRMANAGECONFIG37_DESC'),
			'step'        => 5,
			'min'         => 15,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTSECOND')));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalSystem","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > System > System fieldset.
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

		<!-- RESTORE WIZARD - Button -->

		<?php
		if (isset($params['wizardstate']) && (int) $params['wizardstate'])
		{
			echo $this->formFactory->createField([
				'type'   => 'link',
				'href'   => 'index.php?option=com_vikrestaurants&task=wizard.restore',
				'text'   => JText::translate('VRWIZARDBTNREST'),
				'target' => '_blank',
			]);
		}
		?>

	</div>

</div>

<!-- DATES & TIMES -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_DATETIME_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- DATE FORMAT - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'dateformat',
			'value'   => $params['dateformat'],
			'label'   => JText::translate('VRMANAGECONFIG5'),
			'class'   => 'medium-large',
			'options' => [
				JHtml::fetch('select.option', 'Y/m/d', JText::translate('VRCONFIGDATEFORMAT1')),
				JHtml::fetch('select.option', 'm/d/Y', JText::translate('VRCONFIGDATEFORMAT2')),
				JHtml::fetch('select.option', 'd/m/Y', JText::translate('VRCONFIGDATEFORMAT3')),
				JHtml::fetch('select.option', 'Y-m-d', JText::translate('VRCONFIGDATEFORMAT4')),
				JHtml::fetch('select.option', 'm-d-Y', JText::translate('VRCONFIGDATEFORMAT5')),
				JHtml::fetch('select.option', 'd-m-Y', JText::translate('VRCONFIGDATEFORMAT6')),
				JHtml::fetch('select.option', 'Y.m.d', JText::translate('VRCONFIGDATEFORMAT7')),
				JHtml::fetch('select.option', 'm.d.Y', JText::translate('VRCONFIGDATEFORMAT8')),
				JHtml::fetch('select.option', 'd.m.Y', JText::translate('VRCONFIGDATEFORMAT9')),
			],
		]);
		?>
		
		<!-- TIME FORMAT - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'timeformat',
			'value'   => $params['timeformat'],
			'label'   => JText::translate('VRMANAGECONFIG6'),
			'class'   => 'medium-large',
			'options' => [
				JHtml::fetch('select.option', 'h:i A', JText::translate('VRCONFIGTIMEFORMAT1')),
				JHtml::fetch('select.option',   'H:i', JText::translate('VRCONFIGTIMEFORMAT2')),
				JHtml::fetch('select.option', 'g:i A', JText::translate('VRCONFIGTIMEFORMAT3')),
				JHtml::fetch('select.option',   'G:i', JText::translate('VRCONFIGTIMEFORMAT4')),
			],
		]);
		?>

		<!-- WORKING TIME MODE - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'opentimemode',
			'value'       => $params['opentimemode'],
			'label'       => JText::translate('VRMANAGECONFIG10'),
			'description' => JText::translate('VRMANAGECONFIG10_DESC'),
			'class'       => 'medium-large',
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGOPENTIME1')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGOPENTIME2')),
			],
		]);
		?>

		<!-- CONTINUOUS OPENING HOUR - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'number',
			'name'  => 'hourfrom',
			'value' => $params['hourfrom'],
			'label' => JText::translate('VRMANAGESHIFT2'),
			'min'   => 0,
			'max'   => 23,
			'step'  => 1,
			'control' => [
				'class' => 'opening-cont-field',
				'style' => $params['opentimemode'] == 0 ? '' : 'display: none;',
			],
		]);
		?>

		<!-- CONTINUOUS CLOSING HOUR - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'number',
			'name'  => 'hourto',
			'value' => $params['hourto'],
			'label' => JText::translate('VRMANAGESHIFT3'),
			'min'   => 1,
			'max'   => 24,
			'step'  => 1,
			'control' => [
				'class' => 'opening-cont-field',
				'style' => $params['opentimemode'] == 0 ? '' : 'display: none;',
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalSystem","key":"datetime","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > System > Date & Time fieldset.
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

		<!-- CURRENT TIMEZONE - Label -->

		<?php
		echo $this->formFactory->createField([
			'label'       => JText::translate('VRMANAGECONFIG79'),
			'description' => JText::sprintf('VRMANAGECONFIG79_HELP', JFactory::getApplication()->get('offset', 'UTC')),
		])->render(function() {
			?>
			<span class="badge badge-info">
				<?php echo str_replace('_', ' ', date_default_timezone_get()); ?>
			</span>

			<span class="badge badge-important">
				<?php echo date('Y-m-d H:i:s T'); ?>
			</span>
			<?php
		});
		?>

	</div>

</div>

<!-- BOOKING -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_BOOKING_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- ENABLE USER REGISTRATION - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'enablereg',
			'checked'     => $params['enablereg'],
			'label'       => JText::translate('VRMANAGECONFIG34'),
			'description' => JText::translate('VRMANAGECONFIG34_DESC'),
		]);
		?>
		
		<!-- SHOW PHONE PREFIX - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'phoneprefix',
			'checked'     => $params['phoneprefix'],
			'label'       => JText::translate('VRMANAGECONFIG80'),
			'description' => JText::translate('VRMANAGECONFIG80_DESC'),
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalSystem","key":"booking","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > System > Booking fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['booking']))
		{
			echo $forms['booking'];

			// unset details form to avoid displaying it twice
			unset($forms['booking']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalSystem","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > System tab.
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
		// toggle translation link
		w.multilangValueChanged = (is) => {
			if (is) {
				$('.config-trx').show();
			} else {
				$('.config-trx').hide();
			}
		}

		$(function() {
			// handle opening time mode
			$('select[name="opentimemode"]').on('change', function() {
				if ($(this).val() == 0) {
					$('.opening-cont-field').show();
				} else {
					$('.opening-cont-field').hide();
				}
			});
		});
	})(jQuery, window);
</script>