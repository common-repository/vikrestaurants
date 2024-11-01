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
 * called "onDisplayViewConfigReviewsComment". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('ReviewsComment');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- REVIEW COMMENT REQUIRED - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'revcommentreq',
			'checked'     => $params['revcommentreq'],
			'label'       => JText::translate('VRMANAGECONFIG61'),
			'description' => JText::translate('VRMANAGECONFIG61_DESC'),
		]);
		?>
		
		<!-- MIN COMMENT LENGTH - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'revminlength',
			'value'       => $params['revminlength'],
			'label'       => JText::translate('VRMANAGECONFIG62'),
			'description' => JText::translate('VRMANAGECONFIG62_DESC'),
			'step'        => 1,
			'min'         => 0,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRCHARS')));
		?>
		
		<!-- MAX COMMENT LENGTH - Number -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'number',
			'name'        => 'revmaxlength',
			'value'       => $params['revmaxlength'],
			'label'       => JText::translate('VRMANAGECONFIG63'),
			'description' => JText::translate('VRMANAGECONFIG63_DESC'),
			'step'        => 1,
			'min'         => 32,
		])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(JText::translate('VRCHARS')));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigReviewsComment","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Reviews > Comment > Comment fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigReviewsComment","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Reviews > Comment tab.
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
