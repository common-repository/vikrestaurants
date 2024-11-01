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
 * called "onDisplayViewConfigGlobalGDPR". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalGDPR');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">

		<!-- GDPR - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'name'        => 'gdpr',
			'type'        => 'checkbox',
			'label'       => JText::translate('VRMANAGECONFIG82'),
			'description' => JText::translate('VRMANAGECONFIG82_HELP'),
			'checked'     => (bool) $params['gdpr'],
			'onchange'    => 'onGDPRValueChanged(this.checked)'
		])->render();
		?>
		
		<!-- PRIVACY POLICY - text -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'text',
			'name'  => 'policylink',
			'label' => JText::translate('VRMANAGECONFIG83'),
			'value' => $params['policylink'],
			'control' => [
				'class' => 'gdpr-child',
				'style' => $params['gdpr'] ? '' : 'display: none;',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
			$this->translations['policylink'], $params['multilanguage']
		));
		?>
	
		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalGDPR","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > GDPR > GDPR fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalGDPR","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > GDPR tab.
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
		// toggle visibility of the GDPR-related fields
		w.onGDPRValueChanged = (checked) => {
			if (checked) {
				$('.gdpr-child').show();
			} else {
				$('.gdpr-child').hide();
			}
		}
	})(jQuery, window);
</script>