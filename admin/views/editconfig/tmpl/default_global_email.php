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
 * called "onDisplayViewConfigGlobalEmail". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalEmail');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
	
		<!-- ADMIN EMAIL - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'text',
			'name'        => 'adminemail',
			'value'       => $params['adminemail'],
			'required'    => true,
			'label'       => JText::translate('VRMANAGECONFIG1'),
			'description' => JText::translate('VRMANAGECONFIG1_DESC'),
		]);
		?>
		
		<!-- SENDER EMAIL - Text -->

		<?php
		echo $this->formFactory->createField([
			'type'  => 'email',
			'name'  => 'senderemail',
			'value' => $params['senderemail'],
			'label' => JText::translate('VRMANAGECONFIG43'),
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalEmail","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > E-mail > E-mail fieldset.
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
<!-- {"rule":"customizer","event":"onDisplayViewConfigGlobalEmail","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > E-mail tab.
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