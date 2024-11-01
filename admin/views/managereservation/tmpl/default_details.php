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

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewReservationDetails".
 * It is also possible to use "onDisplayViewReservationDetailsSidebar"
 * to include any additional fieldsets within the right sidebar.
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$detailsForms = $this->onDisplayView('Details');
$sidebarForms = $this->onDisplayView('DetailsSidebar');

?>

<div class="row-fluid">

	<!-- MAIN -->

	<div class="span8">

		<!-- TABLES -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMENUTABLES'));
				echo $this->loadTemplate('details_tables');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"tables","type":"field"} -->

				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Tables" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewReservation" hook.
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

		<!-- MENUS -->

		<div class="row-fluid" id="menus-fieldset" style="<?php echo $this->menus ? '' : 'display: none;'; ?>">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMENUMENUS'));
				echo $this->loadTemplate('details_menus');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"menus","type":"field"} -->

				<?php   
				/**
				 * Look for any additional fields to be pushed within
				 * the "Menus" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewReservation" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['menus']))
				{
					echo $this->forms['menus'];

					// unset details form to avoid displaying it twice
					unset($this->forms['menus']);
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

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservationDetails","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the main panel.
		 *
		 * @since 1.9
		 */
		foreach ($detailsForms as $formName => $formHtml)
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

	<!-- SIDEBAR -->

	<div class="span4 full-width">

		<!-- BOOKING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRE_BOOKING_FIELDSET'), 'form-vertical');
				echo $this->loadTemplate('details_booking');
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

		<!-- ORDER -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRTKORDERCARTFIELDSET1'), 'form-vertical');
				echo $this->loadTemplate('details_order');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"order","type":"field"} -->

				<?php   
				/**
				 * Look for any additional fields to be pushed within
				 * the "Order" fieldset (right-side).
				 *
				 * NOTE: retrieved from "onDisplayViewReservation" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['order']))
				{
					echo $this->forms['order'];

					// unset details form to avoid displaying it twice
					unset($this->forms['order']);
				}
				
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- BILLING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGECUSTOMERTITLE2'), 'form-vertical');
				echo $this->loadTemplate('details_billing');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservation","key":"billing","type":"field"} -->

				<?php   
				/**
				 * Look for any additional fields to be pushed within
				 * the "Billing" fieldset (right-side).
				 *
				 * NOTE: retrieved from "onDisplayViewReservation" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['billing']))
				{
					echo $this->forms['billing'];

					// unset details form to avoid displaying it twice
					unset($this->forms['billing']);
				}
				
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservationDetailsSidebar","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the sidebar (below "Parameters" fieldset).
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

</div>
