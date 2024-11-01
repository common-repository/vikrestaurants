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

// fetch all the take-away status codes suitable for the default status setting
$statusCodes = JHtml::fetch('vrehtml.status.find', ['code', 'name', 'approved'], ['takeaway' => 1, 'reserved' => 1]);

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigtkGlobalShop". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalShop');

?>

<!-- ORDERS -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMENUTAKEAWAYRESERVATIONS'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- DEFAULT STATUS - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkdefstatus',
			'value'       => $params['tkdefstatus'],
			'class'       => 'medium',
			'label'       => JText::translate('VRMANAGECONFIG35'),
			'description' => JText::sprintf('VRMANAGECONFIG35_DESC', JHtml::fetch('vrehtml.status.pending', 'takeaway')->name),
			'options'     => array_map(function($status) {
				return JHtml::fetch('select.option', $status->code, $status->name);
			}, $statusCodes),
		]);
		?>

		<!-- SELF CONFIRMATION - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkselfconfirm',
			'checked'     => $params['tkselfconfirm'],
			'label'       => JText::translate('VRMANAGECONFIG91'),
			'description' => JText::translate('VRMANAGECONFIG91_HELP'),
			'control'     => [
				'class' => 'vr-tkdefstatus-child',
				'style' => JHtml::fetch('vrehtml.status.isapproved', 'takeaway', $params['tkdefstatus']) ? 'display: none;' : '',
			],
		]);
		?>

		<!-- ORDERS LOCKED FOR - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'tklocktime',
			'value'       => $params['tklocktime'],
			'label'       => JText::translate('VRMANAGECONFIGTK8'),
			'description' => JText::translate('VRMANAGECONFIGTK8_DESC'),
			'min'         => 5,
			'step'        => 5,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
		?>

		<!-- LOGIN REQUIREMENTS - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkloginreq',
			'value'       => $params['tkloginreq'],
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
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalShop","key":"orders","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Shop > Orders fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['orders']))
		{
			echo $forms['orders'];

			// unset details form to avoid displaying it twice
			unset($forms['orders']);
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
			'name'        => 'tkenablecanc',
			'checked'     => $params['tkenablecanc'],
			'label'       => JText::translate('VRMANAGECONFIG40'),
			'description' => JText::translate('VRMANAGECONFIG40_DESC'),
			'onchange'    => 'cancellationValueChanged(this.checked)',
		]);
		?>

		<!-- CANCELLATION REASON - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkcancreason',
			'value'       => $params['tkcancreason'],
			'class'       => 'medium-large',
			'label'       => JText::translate('VRMANAGECONFIG68'),
			'description' => JText::translate('VRMANAGECONFIG68_DESC'),
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGCANCREASONOPT0')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGCANCREASONOPT1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGCANCREASONOPT2')),
			],
			'control'     => [
				'class' => 'vr-tkenablecanc-child',
				'style' => $params['tkenablecanc'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- ACCEPT CANCELLATION BEFORE - Number -->

		<?php
		$cancUnitField = $this->formFactory->createField([
			'type'    => 'select',
			'name'    => 'tkcancunit',
			'value'   => $params['tkcancunit'],
			'class'   => 'short',
			'hidden'  => true,
			'options' => [
				'days'  => JText::translate('VRFORMATDAYS'),
				'hours' => JText::translate('VRFORMATHOURS'),
			],
		]);

		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'tkcanctime',
			'value'       => $params['tkcanctime'],
			'label'       => JText::translate('VRMANAGECONFIG41'),
			'description' => JText::translate('VRMANAGECONFIG41_HELP'),
			'min'         => 0,
			'step'        => 1,
			'control'     => [
				'class' => 'vr-tkenablecanc-child',
				'style' => $params['tkenablecanc'] ? '' : 'display: none;',
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
			'name'        => 'tkcancmins',
			'value'       => $params['tkcancmins'],
			'label'       => JText::translate('VRMANAGECONFIG90'),
			'description' => JText::translate('VRMANAGECONFIG90_HELP'),
			'min'         => 0,
			'step'        => 1,
			'control'     => [
				'class' => 'vr-tkenablecanc-child',
				'style' => $params['tkenablecanc'] ? '' : 'display: none;',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRSHORTCUTMINUTE')));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalShop","key":"cancellation","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Shop > Cancellation fieldset.
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

<!-- MENUS LIST -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRE_MENUSLIST_FIELDSET'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- SHOW IMAGES - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'    => 'checkbox',
			'name'    => 'tkshowimages',
			'checked' => $params['tkshowimages'],
			'label'   => JText::translate('VRMANAGECONFIGTK30'),
		]);
		?>

		<!-- SHOW TIMES - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkshowtimes',
			'checked'     => $params['tkshowtimes'],
			'label'       => JText::translate('VRMANAGECONFIGTK37'),
			'description' => JText::translate('VRMANAGECONFIGTK37_HELP'),
		]);
		?>

		<!-- TAKEAWAY REVIEWS - Checkbox -->

		<?php
		if ((int) $params['enablereviews'] === 1)
		{
			echo $this->formFactory->createField([
				'type'        => 'checkbox',
				'name'        => 'revtakeaway',
				'checked'     => $params['revtakeaway'],
				'label'       => JText::translate('VRMANAGECONFIG58'),
				'description' => JText::translate('VRMANAGECONFIG59_DESC'),
				'onchange'    => 'enableReviewsValueChanged(this.checked)',
			]);
		}
		?>

		<!-- PRODUCTS DESCRIPTION LENGTH - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'tkproddesclength',
			'value'       => $params['tkproddesclength'],
			'label'       => JText::translate('VRMANAGECONFIGTK29'),
			'description' => JText::translate('VRMANAGECONFIGTK29_DESC'),
			'min'         => 0,
			'step'        => 1,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRCHARS')));
		?>

		<!-- FRONT NOTES - Editor -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'editor',
			'name'        => 'tknote',
			'value'       => $params['tknote'],
			'label'       => JText::translate('VRMANAGECONFIGTK6'),
			'description' => JText::translate('VRMANAGECONFIGTK6_DESC'),
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
			$this->translations['tknote'], $params['multilanguage'], 'bottom'
		));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalShop","key":"menus","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Shop > Menus List fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['menus']))
		{
			echo $forms['menus'];

			// unset details form to avoid displaying it twice
			unset($forms['menus']);
		}
		?>

	</div>

</div>

<!-- ORIGIN ADDRESSES -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGECONFIGTK19'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<?php
		echo VREApplication::getInstance()->alert(JText::translate('VRE_CONFIG_ORIGINS_SCOPE'), 'info');

		echo $this->formFactory->createField([
			'type'        => 'link',
			'href'        => 'index.php?option=com_vikrestaurants&view=origins',
			'text'        => JText::translate('VRMANAGECONFIGTK21'),
			'id'          => 'manage-origins-btn',
			'hiddenLabel' => true,
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalShop","key":"origins","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Shop > Origin Addresses fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['origins']))
		{
			echo $forms['origins'];

			// unset details form to avoid displaying it twice
			unset($forms['origins']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalShop","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Shop tab.
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

JText::script('VRE_CONFIRM_MESSAGE_UNSAVE');
?>

<script>
	(function($, w) {
		'use strict';

		const statusCodes = <?php echo json_encode($statusCodes); ?>;

		w.cancellationValueChanged = (checked) => {
			if (checked) {
				$('.vr-tkenablecanc-child').show();
			} else {
				$('.vr-tkenablecanc-child').hide();
			}
		}

		w.enableReviewsValueChanged = (checked) => {
			if (checked) {
				$('.vr-revtakeaway-child').show();
			} else {
				$('.vr-revtakeaway-child').hide();
			}
		}

		$(function() {
			if (!w.configObserver) {
				// register page observer
				w.configObserver = new VikFormObserver('#adminForm');

				setTimeout(() => {
					// wait some seconds in order to let TinyMCE completes the initialization
					w.configObserver.freeze();
				}, 256);
			}

			$('#manage-origins-btn').on('click', function(event) {
				if (!w.configObserver.isChanged()) {
					// nothing has changed, go ahead
					return true;
				}

				// ask for a confirmation
				if (!confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'))) {
					// do not leave the page
					event.preventDefault();
					event.stopPropagation();
					return false;
				}
			});

			// handle self confirmation
			$('select[name="tkdefstatus"]').on('change', function() {
				const val = $(this).val();

				let code = statusCodes.filter((data) => {
					return data.code === val;
				});

				if (code.length && code[0].approved == 0) {
					$('.vr-tkdefstatus-child').show();
				} else {
					$('.vr-tkdefstatus-child').hide();
				}
			});
		})
	})(jQuery, window);
</script>