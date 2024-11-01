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

$config = VREFactory::getConfig();

$currency = VREFactory::getCurrency();

?>

<h3><?php echo JText::translate('VRMANAGERESERVATION20'); ?></h3>

<div class="order-fields">

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"payment.start","type":"field"} -->

	<?php
	// plugins can use the "payment.start" key to introduce custom
	// HTML before all the payment lines
	if (isset($this->addons['payment.start']))
	{
		echo $this->addons['payment.start'];

		// unset payment start form to avoid displaying it twice
		unset($this->addons['payment.start']);
	}
	?>

	<!-- Total Net -->

	<div class="order-field total-net">

		<label><?php echo JText::translate('VRMANAGETKORDDISC2'); ?></label>

		<div class="order-field-value">
			<b><?php echo $currency->format($this->order->total_net); ?></b>
		</div>

	</div>

	<!-- Delivery Charge -->

	<?php if ($this->order->delivery_charge > 0): ?>
		
		<div class="order-field delivery-charge">

			<label><?php echo JText::translate('VRMANAGETKRES31'); ?></label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->delivery_charge); ?></b>
			</div>

		</div>

	<?php endif; ?>

	<!-- Payment Charge -->

	<?php if ($this->order->payment_charge > 0): ?>

		<div class="order-field payment-charge">

			<label><?php echo JText::translate('VRINVPAYCHARGE'); ?></label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->payment_charge); ?></b>
			</div>

		</div>
	
	<?php endif; ?>

	<!-- Total Tax -->

	<?php if ($this->order->total_tax > 0): ?>

		<div class="order-field total-tax">

			<label><?php echo JText::translate('VRINVTAXES'); ?></label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->total_tax); ?></b>
			</div>

		</div>
	
	<?php endif; ?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"payment.init","type":"field"} -->

	<?php
	// plugins can use the "payment.init" key to introduce custom
	// HTML after the total net and the taxes
	if (isset($this->addons['payment.init']))
	{
		echo $this->addons['payment.init'];

		// unset payment init form to avoid displaying it twice
		unset($this->addons['payment.init']);
	}
	?>

	<!-- Discount -->

	<?php if ($this->order->discount_val > 0): ?>

		<div class="order-field total-discount">

			<label><?php echo JText::translate('VRINVDISCOUNTVAL'); ?></label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->discount_val * -1); ?></b>
			</div>

		</div>
	
	<?php endif; ?>

	<!-- Tip -->

	<?php if ($this->order->tip_amount > 0): ?>
		
		<div class="order-field total-tax">

			<label><?php echo JText::translate('VRINVTIP'); ?></label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->tip_amount); ?></b>
			</div>

		</div>
	
	<?php endif; ?>

	<!-- Paid -->

	<?php if ($this->order->payment || $this->order->tot_paid > 0): ?>
		
		<div class="order-field total-paid">

			<label>
				<?php
				if ($this->order->payment)
				{
					?><i class="<?php echo $this->order->payment->fontIcon; ?> hasTooltip" title="<?php echo $this->escape($this->order->payment->name); ?>" style="margin-right: 4px;"></i><?php
				}

				echo JText::translate('VRORDERPAID');
				?>
			</label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->tot_paid); ?></b>
			</div>

		</div>

	<?php endif; ?>

	<!-- Due -->

	<?php if (!$this->order->paid): ?>

		<div class="order-field total-due">

			<label><?php echo JText::translate('VRORDERINVDUE'); ?></label>

			<div class="order-field-value">
				<b><?php echo $currency->format($this->order->total_due); ?></b>
			</div>

		</div>
	
	<?php endif; ?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"payment.total","type":"field"} -->
	
	<?php
	// plugins can use the "payment.total" key to introduce custom
	// HTML before the grand total
	if (isset($this->addons['payment.total']))
	{
		echo $this->addons['payment.total'];

		// unset payment total form to avoid displaying it twice
		unset($this->addons['payment.total']);
	}
	?>

	<!-- Total Cost -->

	<div class="order-field total-cost">

		<label><?php echo JText::translate('VRMANAGETKORDDISC1'); ?></label>

		<div class="order-field-value">
			<b><?php echo $currency->format($this->order->total_to_pay); ?></b>
		</div>

	</div>

</div>

<!-- Coupon -->

<?php if ($this->order->coupon): ?>

	<div class="coupon-box">

		<span class="coupon-code">
			<i class="fas fa-ticket-alt hasTooltip" title="<?php echo $this->escape(JText::translate('VRMANAGERESERVATION8')); ?>"></i>
			<b><?php echo $this->order->coupon->code; ?></b>
		</span>

		<span class="coupon-amount">
			<?php
			if ($this->order->coupon->type == 1)
			{
				echo $currency->format($this->order->coupon->amount, [
					'symbol'     => '%',
					'position'   => 1,
					'space'      => false,
					'no_decimal' => true,
				]);
			}
			else
			{
				echo $currency->format($this->order->coupon->amount);
			}
			?>
		</span>

	</div>

<?php endif; ?>

<!-- Invoice -->

<?php if ($this->order->invoice): ?>

	<hr />

	<div class="invoice-record">

		<!-- Invoice Number -->

		<div class="invoice-id">
			<b><?php echo $this->order->invoice->number; ?></b>
		</div>

		<!-- Invoice Creation Date -->

		<div class="invoice-date">
			<?php echo JHtml::fetch('date', $this->order->invoice->createdon, JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'), date_default_timezone_get()); ?>
		</div>

		<!-- Invoice File -->

		<div class="invoice-download">
			<?php
			if (is_file($this->order->invoice->path))
			{
				?>
				<a href="<?php echo $this->order->invoice->uri; ?>" target="_blank">
					<i class="fas fa-file-pdf"></i>
				</a>
				<?php
			}
			else
			{
				?><i class="fas fa-file-pdf"></i><?php
			}
			?>
		</div>

	</div>

<?php endif; ?>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"payment.middle","type":"field"} -->

<?php
// plugins can use the "payment.middle" key to introduce custom
// HTML between the coupon code and the status codes history
if (isset($this->addons['payment.middle']))
{
	echo $this->addons['payment.middle'];

	// unset payment middle form to avoid displaying it twice
	unset($this->addons['payment.middle']);
}
?>

<!-- Order Status Codes History -->

<?php if ($this->order->history): ?>

	<hr />

	<h3><?php echo JText::translate('VRORDERSTATUSES'); ?></h3>

	<div class="order-status-history">

		<?php
		foreach ($this->order->history as $status)
		{
			?>
			<div class="order-status-block">

				<?php if ($status->icon): ?>
					<div class="code-icon">
						<img src="<?php echo $status->iconURL; ?>" />
					</div>
				<?php endif; ?>

				<div class="code-text">
					<div class="code-title">
						<div class="code-name">
							<?php echo $status->code; ?>
						</div>

						<div class="code-date">
							<?php echo VikRestaurants::formatTimestamp($config->get('dateformat') . ' ' . $config->get('timeformat'), $status->createdon); ?>
						</div>
					</div>

					<?php if ($status->notes || $status->codeNotes): ?>
						<div class="code-notes">
							<?php echo $status->notes ? $status->notes : '<em>' . $status->codeNotes . '</em>'; ?>
						</div>
					<?php endif; ?>
				</div>

			</div>
			<?php
		}
		?>

	</div>

<?php endif; ?>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"payment.bottom","type":"field"} -->

<?php
// plugins can use the "payment.bottom" key to introduce custom
// HTML after the status codes history
if (isset($this->addons['payment.bottom']))
{
	echo $this->addons['payment.bottom'];

	// unset payment bottom form to avoid displaying it twice
	unset($this->addons['payment.bottom']);
}
