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

JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.fontawesome');

$coupon = $this->coupon;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewCoupon". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$forms = $this->onDisplayView();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- LEFT SIDE -->
	
		<div class="span8 full-width">

			<!-- COUPON -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JDETAILS'));
					echo $this->loadTemplate('coupon');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewCoupon","key":"coupon","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Details" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['coupon']))
					{
						echo $forms['coupon'];

						// unset details form to avoid displaying it twice
						unset($forms['coupon']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- NOTES -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGERESERVATIONTITLE3'));
					echo $this->loadTemplate('notes');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewCoupon","key":"notes","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed below
					 * the "Notes" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['notes']))
					{
						echo $forms['notes'];

						// unset details form to avoid displaying it twice
						unset($forms['notes']);
					}
					
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

		<!-- RIGHT SIDE -->
	
		<div class="span4 full-width">

			<!-- USAGES -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_USAGES_FIELDSET'), 'form-vertical');
					echo $this->loadTemplate('usages');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewCoupon","key":"usages","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Usages" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['usages']))
					{
						echo $forms['usages'];

						// unset details form to avoid displaying it twice
						unset($forms['usages']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- PUBLISHING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
					echo $this->loadTemplate('publishing');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewCoupon","key":"publishing","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Publishing" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['publishing']))
					{
						echo $forms['publishing'];

						// unset details form to avoid displaying it twice
						unset($forms['publishing']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewCoupon","type":"fieldset"} -->

			<?php
			// iterate forms to be displayed within the sidebar panel
			foreach ($forms as $formName => $formHtml)
			{
				$title = JText::translate($formName);
				?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset($title, 'form-vertical');
						echo $formHtml;
						echo $vik->closeFieldset();
						?>
					</div>
				</div>
				<?php
			}
			?>

		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $coupon->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			w.validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>