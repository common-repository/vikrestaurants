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
 * called "onDisplayViewConfigReviewsDetails". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('ReviewsDetails');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- ENABLE REVIEWS - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'enablereviews',
			'checked'     => $params['enablereviews'],
			'label'       => JText::translate('VRMANAGECONFIG58'),
			'description' => JText::translate('VRMANAGECONFIG58_DESC'),
		]);
		?>
		
		<!-- REVIEWS LEAVE MODE - Dropdown -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'revleavemode',
			'value'       => $params['revleavemode'],
			'class'       => 'medium-large',
			'label'       => JText::translate('VRMANAGECONFIG60'),
			'description' => JText::translate('VRMANAGECONFIG60_DESC'),
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGREVLEAVEMODEOPT0')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGREVLEAVEMODEOPT1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGREVLEAVEMODEOPT2')),
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigReviewsDetails","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Reviews > Reviews > Details fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigReviewsDetails","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Reviews > Reviews tab.
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
