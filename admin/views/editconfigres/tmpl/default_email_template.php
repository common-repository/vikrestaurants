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

$templates = JHtml::fetch('vrehtml.admin.mailtemplates', 'restaurant');

$mailTmplTip = JText::sprintf(
	'VRMANAGECONFIGMAILTMPL',
	'<i class="fas fa-pen"></i>',
	'<i class="fas fa-fill-drip"></i>'
);

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigresEmailTemplate". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('EmailTemplate');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
	
		<!-- CUSTOMER EMAIL TEMPLATE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'mailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['mailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIG47'),
			'description' => JText::sprintf('VRMANAGECONFIG47_DESC', $mailTmplTip),
			'options'     => $templates,
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('mailtmpl', 'customer'));
		?>

		<!-- ADMIN EMAIL TEMPLATE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'adminmailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['adminmailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIG56'),
			'description' => JText::sprintf('VRMANAGECONFIG56_DESC', $mailTmplTip),
			'options'     => $templates,
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('adminmailtmpl', 'admin'));
		?>

		<!-- CANCELLATION EMAIL TEMPLATE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'cancmailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['cancmailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIG57'),
			'description' => JText::sprintf('VRMANAGECONFIG57_DESC', $mailTmplTip),
			'options'     => $templates,
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('cancmailtmpl', 'cancellation'));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresEmailTemplate","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the E-mail > Templates > Details fieldset.
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

		<hr style="margin: 30px 0;" />

		<!-- CONDITIONAL TEXTS MANAGEMENT - Button -->

		<?php
		echo $this->formFactory->createField()
			->type('link')
			->href('index.php?option=com_vikrestaurants&view=mailtexts')
			->text(JText::translate('VRE_MAILTEXT_MANAGE_BTN'))
			->id('manage-mailtext-btn')
			->hiddenLabel(true);

		echo $this->formFactory->createField()
			->type('alert')
			->style('info')
			->text(JText::translate('VRE_MAILTEXT_MANAGE_BTN_DESC'))
			->hiddenLabel(true);
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigresEmailTemplate","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the E-mail > Templates tab.
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

		$(function() {
			$('#manage-mailtext-btn').on('click', (event) => {
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
		});
	})(jQuery, window);
</script>