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
 * called "onDisplayViewConfigappApiApplications". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('ApiApplications');

?>

<!-- USERS -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRCONFIG_API_USERS'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<?php echo VREApplication::getInstance()->alert(JText::translate('VRCONFIG_API_USERS_DESC'), 'info'); ?>

		<!-- SEE LIST - Button -->

		<?php
		echo $this->formFactory->createField()
			->type('link')
			->href('index.php?option=com_vikrestaurants&view=apiusers')
			->class('vr-api-btn' . ($params['apifw'] ? '' : ' disabled'))
			->text(JText::translate('VRMANAGECONFIG71'))
			->hiddenLabel(true);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappApiApplications","key":"users","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the API > Applications > Users fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['users']))
		{
			echo $forms['users'];

			// unset details form to avoid displaying it twice
			unset($forms['users']);
		}
		?>

	</div>

</div>

<!-- PLUGINS -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRCONFIG_API_PLUGINS'); ?></h3>
	</div>

	<div class="config-fieldset-body">

		<?php echo VREApplication::getInstance()->alert(JText::translate('VRCONFIG_API_PLUGINS_DESC'), 'info'); ?>

		<!-- SEE LIST - Button -->

		<?php
		echo $this->formFactory->createField()
			->type('link')
			->href('index.php?option=com_vikrestaurants&view=apiplugins')
			->class('vr-api-btn' . ($params['apifw'] ? '' : ' disabled'))
			->text(JText::translate('VRMANAGECONFIG77'))
			->hiddenLabel(true);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappApiApplications","key":"plugins","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the API > Applications > Plugins fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['plugins']))
		{
			echo $forms['plugins'];

			// unset details form to avoid displaying it twice
			unset($forms['plugins']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigappApiApplications","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the API > Applications tab.
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
			$('.vr-api-btn').on('click', function(event) {
				if ($(this).hasClass('disabled')) {
					event.preventDefault();
					event.stopPropagation();
					return false;
				}

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