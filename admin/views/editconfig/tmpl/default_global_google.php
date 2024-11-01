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
 * called "onDisplayViewConfigGlobalGoogle". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalGoogle');

?>

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGECONFIGGLOBSECTION4'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- API KEY - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'text',
			'name'        => 'googleapikey',
			'value'       => $params['googleapikey'],
			'label'       => JText::translate('VRMANAGECONFIG55'),
			'description' => JText::translate('VRMANAGECONFIG55_HELP'),
		])->render(new E4J\VikRestaurants\Form\Renderers\LockableFieldRenderer);
		?>

		<!-- PLACES API - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'googleapiplaces',
			'checked'     => $params['googleapiplaces'],
			'label'       => JText::translate('VRMANAGECONFIG84'),
			'description' => JText::translate('VRMANAGECONFIG84_HELP'),
			'control'     => [
				'class' => 'google-api-field',
				'style' => $params['googleapikey'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- DIRECTIONS API - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'googleapidirections',
			'checked'     => $params['googleapidirections'],
			'label'       => JText::translate('VRMANAGECONFIG85'),
			'description' => JText::translate('VRMANAGECONFIG85_HELP'),
			'control'     => [
				'class' => 'google-api-field',
				'style' => $params['googleapikey'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- MAPS STATIC API - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'googleapistaticmap',
			'checked'     => $params['googleapistaticmap'],
			'label'       => JText::translate('VRMANAGECONFIG86'),
			'description' => JText::translate('VRMANAGECONFIG86_HELP'),
			'control'     => [
				'class' => 'google-api-field',
				'style' => $params['googleapikey'] ? '' : 'display: none;',
			],
		]);
		?>
	
		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalGoogle","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Google > Google fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalGoogle","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Google tab.
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
	(function($) {
		'use strict';

		$(function() {
			// toggle Google API Key sub-children
			$('input[name="googleapikey"]').on('keyup', function() {
				if ($(this).val().length) {
					$('.google-api-field').show();
				} else {
					$('.google-api-field').hide();
				}
			});
		})
	})(jQuery);
</script>