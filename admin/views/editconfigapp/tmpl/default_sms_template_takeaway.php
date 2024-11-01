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
 * called "onDisplayViewConfigappSmsTemplatesTakeaway". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('SmsTemplatesTakeaway');

?>

<!-- CUSTOMER -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRCONFIG_SMSTMPL_TAKEAWAY_CUSTOMER'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- TOOLBAR -->

		<div style="display: inline-block; width: 100%;">
			<div class="btn-group pull-left">
				<?php
				foreach (['total_cost', 'checkin', 'service', 'customer', 'company', 'created_on'] as $tag)
				{
					echo $this->formFactory->createField()
						->type('button')
						->class('sms-put-tag')
						->text('{' . $tag . '}')
						->hidden(true);
				}
				
				/**
				 * Look for any additional fields to be pushed within
				 * the SMS > Templates > Take-Away Customer > Toolbar fieldset.
				 *
				 * @since 1.9
				 */
				if (isset($forms['customer.toolbar']))
				{
					echo $forms['customer.toolbar'];

					// unset details form to avoid displaying it twice
					unset($forms['customer.toolbar']);
				}
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsTemplatesTakeaway","key":"customer.toolbar","type":"field"} -->

		<?php
		echo $this->formFactory->createField()
			->type('textarea')
			->name('smstmpltkcust')
			->value($params['smstmpltkcust'])
			->placeholder(JText::translate('VRSMSMESSAGETKCUSTOMER'))
			->height(200)
			->hidden(true)
			->render(new E4J\VikRestaurants\Form\Renderers\ConfigTranslatableFieldRenderer(
				$this->translations['smstmpltkcust'], $params['multilanguage'], 'bottom'
			));
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsTemplatesTakeaway","key":"customer","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the SMS > Templates > Take-Away Customer fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['customer']))
		{
			echo $forms['customer'];

			// unset details form to avoid displaying it twice
			unset($forms['customer']);
		}
		?>

	</div>

</div>

<!-- ADMIN -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRCONFIG_SMSTMPL_TAKEAWAY_ADMIN'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<!-- TOOLBAR -->

		<div style="display: inline-block; width: 100%;">
			<div class="btn-group pull-left">
				<?php
				foreach (['total_cost', 'checkin', 'service', 'customer', 'company', 'created_on'] as $tag)
				{
					echo $this->formFactory->createField()
						->type('button')
						->class('sms-put-tag')
						->text('{' . $tag . '}')
						->hidden(true);
				}
				
				/**
				 * Look for any additional fields to be pushed within
				 * the SMS > Templates > Take-Away Administrator > Toolbar fieldset.
				 *
				 * @since 1.9
				 */
				if (isset($forms['admin.toolbar']))
				{
					echo $forms['admin.toolbar'];

					// unset details form to avoid displaying it twice
					unset($forms['admin.toolbar']);
				}
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsTemplatesTakeaway","key":"admin.toolbar","type":"field"} -->

		<?php
		echo $this->formFactory->createField()
			->type('textarea')
			->name('smstmpltkadmin')
			->value($params['smstmpltkadmin'])
			->placeholder(JText::translate('VRSMSMESSAGETKADMIN'))
			->height(200)
			->hidden(true);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsTemplatesTakeaway","key":"admin","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the SMS > Templates > Take-Away Administrator fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['admin']))
		{
			echo $forms['admin'];

			// unset details form to avoid displaying it twice
			unset($forms['admin']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsTemplatesTakeaway","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the SMS > Templates tab (after take-away).
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
