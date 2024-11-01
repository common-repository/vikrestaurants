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
 * called "onDisplayViewReservationBill".
 * It is also possible to use "onDisplayViewReservationBillSidebar"
 * to include any additional fieldsets within the right sidebar.
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('Bill');
$sidebarForms = $this->onDisplayView('BillSidebar');

?>

<div class="row-fluid">

	<!-- MAIN -->

	<div class="span8">

		<!-- ITEMS -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGETKRES22'));
				echo $this->loadTemplate('bill_items');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservationBill","key":"items","type":"field"} -->

				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Items" fieldset (left-side).
				 *
				 * @since 1.9
				 */
				if (isset($forms['items']))
				{
					echo $forms['items'];

					// unset details form to avoid displaying it twice
					unset($forms['items']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservationBill","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the main panel.
		 *
		 * @since 1.9
		 */
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

	<!-- SIDEBAR -->

	<div class="span4 full-width">

		<!-- BOOKING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRTKORDERCARTFIELDSET1'), 'form-vertical');
				echo $this->loadTemplate('bill_order');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewReservationBill","key":"order","type":"field"} -->

				<?php   
				/**
				 * Look for any additional fields to be pushed within
				 * the "Order" fieldset (right-side).
				 *
				 * @since 1.9
				 */
				if (isset($forms['order']))
				{
					echo $forms['order'];

					// unset details form to avoid displaying it twice
					unset($forms['order']);
				}
				
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservationBillSidebar","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within the sidebar fieldset.
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

		<!-- PIN CODE -->

		<?php if ($this->reservation->id && VREFactory::getConfig()->getBool('orderfood')): ?>
			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_PINCODE_FIELDSET'), 'form-vertical');
					echo $this->loadTemplate('bill_pin');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewReservationBill","key":"pin","type":"field"} -->

					<?php   
					/**
					 * Look for any additional fields to be pushed within
					 * the "Pin Code" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['pin']))
					{
						echo $forms['pin'];

						// unset details form to avoid displaying it twice
						unset($forms['pin']);
					}
					
					echo $vik->closeFieldset();
					?>
				</div>
			</div>
		<?php endif; ?>

	</div>

</div>
