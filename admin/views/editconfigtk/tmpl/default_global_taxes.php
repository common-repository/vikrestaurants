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
 * called "onDisplayViewConfigtkGlobalTaxes". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalTaxes');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">

		<!-- DEFAULT TAX - Select + Button -->

		<?php
		echo $this->formFactory->createField([
			'name'        => 'tkdeftax',
			'value'       => $params['tkdeftax'],
			'label'       => JText::translate('VRMANAGECONFIG96'),
			'description' => JText::translate('VRMANAGECONFIG96_DESC'),
			'allowClear'  => true,
			'placeholder' => JText::translate('JGLOBAL_SELECT_AN_OPTION'),
		])->render(new E4J\VikRestaurants\Form\Renderers\TaxesFieldRenderer($this->formFactory));
		?>

		<!-- USE TAX BREAKDOWN - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'tkusetaxbd',
			'checked'     => $params['tkusetaxbd'],
			'label'       => JText::translate('VRMANAGECONFIG97'),
			'description' => JText::translate('VRMANAGECONFIG97_DESC'),
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalTaxes","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Taxes > Details fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigtkGlobalTaxes","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Taxes tab.
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
