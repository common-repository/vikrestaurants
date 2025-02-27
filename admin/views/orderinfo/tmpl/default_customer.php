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

?>

<h3><?php echo JText::translate('VRMANAGERESERVATION17'); ?></h3>

<div class="order-fields">

	<!-- Nominative -->

	<?php if ($this->order->purchaser_nominative): ?>

		<div class="order-field">

			<label><?php echo JText::translate('VRMANAGECUSTOMER2'); ?></label>

			<div class="order-field-value">
				<b><?php echo $this->order->purchaser_nominative; ?></b>

				<?php
				// plugins can use the "customer.name" key to introduce custom
				// HTML next to the purchaser nominative
				if (isset($this->addons['customer.name']))
				{
					echo $this->addons['customer.name'];

					// unset details form to avoid displaying it twice
					unset($this->addons['customer.bottom']);
				}
				?>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"customer.name","type":"field"} -->

		</div>

	<?php endif; ?>

	<!-- E-mail -->

	<?php if ($this->order->purchaser_mail): ?>

		<div class="order-field">

			<label><?php echo JText::translate('VRMANAGECUSTOMER3'); ?></label>

			<div class="order-field-value">
				<a href="mailto:<?php echo $this->order->purchaser_mail; ?>">
					<?php echo $this->order->purchaser_mail; ?>
				</a>

				<?php
				// plugins can use the "customer.email" key to introduce custom
				// HTML next to the purchaser e-mail
				if (isset($this->addons['customer.email']))
				{
					echo $this->addons['customer.email'];

					// unset details form to avoid displaying it twice
					unset($this->addons['customer.email']);
				}
				?>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"customer.email","type":"field"} -->

		</div>
	<?php endif; ?>

	<!-- Phone Number -->

	<?php if ($this->order->purchaser_phone): ?>

		<div class="order-field">

			<label><?php echo JText::translate('VRMANAGECUSTOMER4'); ?></label>

			<div class="order-field-value">
				<a href="tel:<?php echo $this->order->purchaser_phone; ?>">
					<?php echo $this->order->purchaser_phone; ?>
				</a>

				<?php
				// plugins can use the "customer.phone" key to introduce custom
				// HTML next to the purchaser phone number
				if (isset($this->addons['customer.phone']))
				{
					echo $this->addons['customer.phone'];

					// unset details form to avoid displaying it twice
					unset($this->addons['customer.phone']);
				}
				?>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"customer.phone","type":"field"} -->

		</div>
	<?php endif; ?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"customer.custom","type":"field"} -->

	<?php
	// plugins can use the "customer.custom" key to introduce custom
	// HTML at the end of the block
	if (isset($this->addons['customer.custom']))
	{
		echo $this->addons['customer.custom'];

		// unset details form to avoid displaying it twice
		unset($this->addons['customer.custom']);
	}
	?>

</div>
