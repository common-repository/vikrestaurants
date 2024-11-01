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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.fontawesome');

$reservation = $this->reservation;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewReservationClosure". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$this->forms = $this->onDisplayView();
$sidebarForms = $this->onDisplayView('Closure');

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- MAIN -->

		<div class="span8">

			<!-- TABLES -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMENUTABLES'));

					// set the default layout to reuse the "tables" fieldset
					$this->setLayout('default');
					echo $this->loadTemplate('details_tables');

					// restore the "closure" layout
					$this->setLayout('closure');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"tables","type":"field"} -->

					<?php
					/**
					 * Look for any additional fields to be pushed within
					 * the "Tables" fieldset (left-side).
					 *
					 * @since 1.8
					 */
					if (isset($this->forms['tables']))
					{
						echo $this->forms['tables'];

						// unset details form to avoid displaying it twice
						unset($this->forms['tables']);
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
					
					echo $this->formFactory->createField()
						->type('editor')
						->name('notes')
						->value($this->reservation->notes)
						->hiddenLabel(true);
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"notes","type":"field"} -->

					<?php   
					/**
					 * Look for any additional fields to be pushed within
					 * the "Notes" fieldset (left-side).
					 *
					 * NOTE: retrieved from "onDisplayViewReservation" hook.
					 *
					 * @since 1.8
					 */
					if (isset($this->forms['notes']))
					{
						echo $this->forms['notes'];

						// unset details form to avoid displaying it twice
						unset($this->forms['notes']);
					}
						
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

		<!-- SIDEBAR -->

		<div class="span4 full-width">

			<!-- BOOKING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_BOOKING_FIELDSET'), 'form-vertical');
					echo $this->loadTemplate('booking');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"booking","type":"field"} -->

					<?php   
					/**
					 * Look for any additional fields to be pushed within
					 * the "Booking" fieldset (right-side).
					 *
					 * NOTE: retrieved from "onDisplayViewReservation" hook.
					 *
					 * @since 1.8
					 */
					if (isset($this->forms['booking']))
					{
						echo $this->forms['booking'];

						// unset details form to avoid displaying it twice
						unset($this->forms['booking']);
					}
					
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewReservationClosure","type":"fieldset"} -->

			<?php
			/**
			 * Iterate remaining forms to be displayed within the sidebar.
			 *
			 * @since 1.9
			 */
			foreach ($sidebarForms as $formName => $formHtml)
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

	<?php if ($this->returnTask): ?>
		<input type="hidden" name="from" value="<?php echo $this->escape($this->returnTask); ?>" />
	<?php endif; ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $reservation->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
JText::script('VRE_AJAX_GENERIC_ERROR');
?>

<script>
	(function($, w) {
		'use strict';

		w.IS_AJAX_CALLING = false;

		$(function() {
			w.reservationValidator = new VikFormValidator('#adminForm');

			// do not submit the form in case we have any pending requests
			w.reservationValidator.addCallback(function() {
				if (IS_AJAX_CALLING || UIAjax.isDoing()) {
					/**
					 * @todo 	Should we prompt an alert?
					 * 			e.g. "Please wait for the request completion."
					 */

					return false;
				}

				return true;
			});	

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || w.reservationValidator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>