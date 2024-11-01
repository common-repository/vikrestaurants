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
 * called "onDisplayViewConfigReviewsPublishing". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('ReviewsPublishing');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- REVIEWS LIST LIMIT - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'revlimlist',
			'value'       => $params['revlimlist'],
			'label'       => JText::translate('VRMANAGECONFIG64'),
			'description' => JText::translate('VRMANAGECONFIG64_DESC'),
			'min'         => 1,
			'step'        => 1,
		]);
		?>
		
		<!-- AUTO PUBLISHED - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'revautopublished',
			'checked'     => $params['revautopublished'],
			'label'       => JText::translate('VRMANAGECONFIG65'),
			'description' => JText::translate('VRMANAGECONFIG65_DESC'),
		]);
		?>
		
		<!-- FILTER BY LANGUAGE - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'revlangfilter',
			'checked'     => $params['revlangfilter'],
			'label'       => JText::translate('VRMANAGECONFIG66'),
			'description' => JText::translate('VRMANAGECONFIG66_DESC'),
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigReviewsPublishing","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Reviews > Publishing > Publishing fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigReviewsPublishing","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Reviews > Publishing tab.
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
