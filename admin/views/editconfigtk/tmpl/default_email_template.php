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

$templates = JHtml::fetch('vrehtml.admin.mailtemplates', 'takeaway');

$mailTmplTip = JText::sprintf(
	'VRMANAGECONFIGMAILTMPL',
	'<i class="fas fa-pen"></i>',
	'<i class="fas fa-fill-drip"></i>'
);

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigtkEmailTemplate". The event method
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
			'name'        => 'tkmailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['tkmailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIG47'),
			'description' => JText::sprintf('VRMANAGECONFIG47_DESCTK', $mailTmplTip),
			'options'     => $templates,
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('tkmailtmpl', 'customer'));
		?>

		<!-- ADMIN EMAIL TEMPLATE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkadminmailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['tkadminmailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIG56'),
			'description' => JText::sprintf('VRMANAGECONFIG56_DESCTK', $mailTmplTip),
			'options'     => $templates,
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('tkadminmailtmpl', 'admin'));
		?>

		<!-- CANCELLATION EMAIL TEMPLATE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkcancmailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['tkcancmailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIG57'),
			'description' => JText::sprintf('VRMANAGECONFIG57_DESCTK', $mailTmplTip),
			'options'     => $templates,
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('tkcancmailtmpl', 'cancellation'));
		?>

		<!-- REVIEWS EMAIL TEMPLATE -->

		<?php
		if ($params['enablereviews'])
		{
			echo $this->formFactory->createField([
				'type'        => 'select',
				'name'        => 'tkreviewmailtmpl',
				'class'       => 'medium-large',
				'value'       => $params['tkreviewmailtmpl'],
				'label'       => JText::translate('VRMANAGECONFIG67'),
				'description' => JText::sprintf('VRMANAGECONFIG67_DESCTK', $mailTmplTip),
				'options'     => $templates,
				'control'     => [
					'class'   => 'vr-revtakeaway-child',
					'visible' =>  (int) $params['revtakeaway'] === 1,
				]
			])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('tkreviewmailtmpl', 'review'));
		}
		?>

		<!-- STOCKS EMAIL TEMPLATE -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'tkstockmailtmpl',
			'class'       => 'medium-large',
			'value'       => $params['tkstockmailtmpl'],
			'label'       => JText::translate('VRMANAGECONFIGTK17'),
			'description' => JText::sprintf('VRMANAGECONFIGTK17_DESC', $mailTmplTip),
			'options'     => $templates,
			'control'     => [
				'class' => 'vre-stock-child',
				'style' => $params['tkenablestock'] ? '' : 'display: none;',
			],
		])->render(new E4J\VikRestaurants\Form\Renderers\ConfigMailTemplateFieldRenderer('tkstockmailtmpl', 'stock'));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigtkEmailTemplate","key":"basic","type":"field"} -->

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
<!-- {"rule":"customizer","event":"onDisplayViewConfigtkEmailTemplate","type":"fieldset"} -->

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