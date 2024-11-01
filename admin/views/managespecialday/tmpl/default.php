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
JHtml::fetch('vrehtml.assets.fancybox');

$specialday = $this->specialday;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewSpecialday". The event method receives the
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

			<!-- SPECIAL DAY -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGEMENU2'));
					echo $this->loadTemplate('specialday');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","key":"specialday","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Special Day" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['specialday']))
					{
						echo $forms['specialday'];

						// unset details form to avoid displaying it twice
						unset($forms['specialday']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- DEPOSIT (RESTAURANT) -->

			<div class="row-fluid restaurant-params" style="<?php echo $specialday->group == 1 ? '' : 'display:none;'; ?>">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGERESERVATION9'));
					echo $this->loadTemplate('restaurant_deposit');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","key":"restaurant.deposit","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the restaurant "Deposit" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['restaurant.deposit']))
					{
						echo $forms['restaurant.deposit'];

						// unset details form to avoid displaying it twice
						unset($forms['restaurant.deposit']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- BOOKING (RESTAURANT) -->

			<div class="row-fluid restaurant-params" style="<?php echo $specialday->group == 1 ? '' : 'display:none;'; ?>">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_BOOKING_FIELDSET'));
					echo $this->loadTemplate('restaurant_booking');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","key":"restaurant.booking","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the restaurant "Booking" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['restaurant.booking']))
					{
						echo $forms['restaurant.booking'];

						// unset details form to avoid displaying it twice
						unset($forms['restaurant.booking']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- BOOKING (TAKE-AWAY) -->

			<div class="row-fluid takeaway-params" style="<?php echo $specialday->group == 2 ? '' : 'display:none;'; ?>">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_BOOKING_FIELDSET'));
					echo $this->loadTemplate('takeaway_booking');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","key":"takeaway.booking","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the take-away "Booking" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['takeaway.booking']))
					{
						echo $forms['takeaway.booking'];

						// unset details form to avoid displaying it twice
						unset($forms['takeaway.booking']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","type":"fieldset"} -->

			<?php
			// iterate forms to be displayed within the main panel
			foreach ($forms as $formName => $formHtml)
			{
				$title = JText::translate($formName);
				?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset($title);
						echo $formHtml;
						echo $vik->closeFieldset();
						?>
					</div>
				</div>
				<?php
			}
			?>

		</div>

		<!-- RIGHT SIDE -->
	
		<div class="span4 full-width">

			<!-- PUBLISHING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
					echo $this->loadTemplate('publishing');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","key":"publishing","type":"field"} -->

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

			<!-- OPTIONS -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_BASIC'), 'form-vertical');
					echo $this->loadTemplate('options');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewSpecialday","key":"options","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Options" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['options']))
					{
						echo $forms['options'];

						// unset details form to avoid displaying it twice
						unset($forms['options']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $specialday->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<script>
	(function($) {
		'use strict';

		let validator;

		$(function() {
			validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery);
</script>