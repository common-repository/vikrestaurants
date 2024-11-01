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
 * called "onDisplayViewConfigappApiSettings". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('ApiSettings');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- ENABLE API - Checkbox -->

		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->name('apifw')
			->checked($params['apifw'])
			->label(JText::translate('VRMANAGECONFIG69'))
			->description(JText::translate('VRMANAGECONFIG69_DESC'))
			->onchange('apiFrameworkValueChanged(this.checked)');
		?>

		<!-- MAX FAILURE ATTEMPTS - Number -->

		<?php
		echo $this->formFactory->createField()
			->type('number')
			->name('apimaxfail')
			->value($params['apimaxfail'])
			->label(JText::translate('VRMANAGECONFIG74'))
			->description(JText::translate('VRMANAGECONFIG75'))
			->min(1)
			->step(1)
			->setControlAttribute('class', 'vr-api-child')
			->setControlAttribute('style', $params['apifw'] ? '' : 'display: none;');
		?>

		<!-- LOGGING MODE - Select -->

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('apilogmode')
			->value($params['apilogmode'])
			->class('medium')
			->label(JText::translate('VRMANAGECONFIG72'))
			->description(JText::translate('VRMANAGECONFIG72_DESC'))
			->setControlAttribute('class', 'vr-api-child')
			->setControlAttribute('style', $params['apifw'] ? '' : 'display: none;')
			->options([
				JHtml::fetch('select.option', 0, JText::translate('VRCONFIGAPIREGLOGOPT0')),
				JHtml::fetch('select.option', 1, JText::translate('VRCONFIGAPIREGLOGOPT1')),
				JHtml::fetch('select.option', 2, JText::translate('VRCONFIGAPIREGLOGOPT2')),
			]);
		?>

		<!-- FLUSH LOGS - Select -->

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('apilogflush')
			->value($params['apilogflush'])
			->class('medium')
			->label(JText::translate('VRMANAGECONFIG73'))
			->description(JText::translate('VRMANAGECONFIG73_DESC'))
			->setControlAttribute('class', 'vr-api-child')
			->setControlAttribute('style', $params['apifw'] ? '' : 'display: none;')
			->options([
				JHtml::fetch('select.option',  1, JText::translate('VRCONFIGAPIFLUSHLOGOPT1')),
				JHtml::fetch('select.option',  7, JText::translate('VRCONFIGAPIFLUSHLOGOPT2')),
				JHtml::fetch('select.option', 30, JText::translate('VRCONFIGAPIFLUSHLOGOPT3')),
				JHtml::fetch('select.option',  0, JText::translate('VRCONFIGAPIFLUSHLOGOPT0')),
			]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappApiSettings","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the API > Settings > Details fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigappApiSettings","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the API > Settings tab.
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

		w.apiFrameworkValueChanged = (checked) => {
			if (checked) {
				$('.vr-api-child').show();
				$('.vr-api-btn').removeClass('disabled');
			} else {
				$('.vr-api-child').hide();
				$('.vr-api-btn').addClass('disabled');
			}
		}
	})(jQuery, window);
</script>