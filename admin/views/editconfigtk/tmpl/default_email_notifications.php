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

// fetch all available status codes for the take-away section
$statusCodesOptions = JHtml::fetch('vrehtml.admin.statuscodes', $group = 'takeaway');

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigtkEmailNotifications". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('EmailNotifications');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- CUSTOMERS - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkmailcustwhen',
			'class'       => 'medium-large',
			'value'       => (array) json_decode($params['tkmailcustwhen']),
			'multiple'    => true,
			'label'       => JText::translate('VRMENUCUSTOMERS'),
			'description' => JText::translate('VRMANAGECONFIG44_DESC'),
			'options'     => $statusCodesOptions,
		]);
		?>

		<!-- OPERATORS - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkmailoperwhen',
			'class'       => 'medium-large',
			'value'       => (array) json_decode($params['tkmailoperwhen']),
			'multiple'    => true,
			'label'       => JText::translate('VRMENUOPERATORS'),
			'description' => JText::translate('VRMANAGECONFIG45_DESC'),
			'options'     => $statusCodesOptions,
		]);
		?>

		<!-- ADMINISTRATORS - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkmailadminwhen',
			'class'       => 'medium-large',
			'value'       => (array) json_decode($params['tkmailadminwhen']),
			'multiple'    => true,
			'label'       => JText::translate('VRMANAGECONFIG46'),
			'description' => JText::translate('VRMANAGECONFIG46_DESC'),
			'options'     => $statusCodesOptions,
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkEmailNotifications","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the E-mail > Notifications > Details fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigtkEmailNotifications","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the E-mail > Notifications tab.
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
