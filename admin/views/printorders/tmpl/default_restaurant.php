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

$order = $this->orderDetails;

$config = VREFactory::getConfig();

$date_format = $config->get('dateformat');
$time_format = $config->get('timeformat');

$currency = VREFactory::getCurrency();

$useServingNumber = VREFactory::getConfig()->getBool('servingnumber');

// extract names from tables list
$tables = array_map(function($t)
{
	return $t->name;
}, $order->tables);

?>

<style>
	.order-note {
		margin-top: 10px;
		padding: 10px;
		border: 1px dashed #ccc;
	}
	.order-note *:first-child {
		margin-top: 0;
	}
	.order-note *:last-child {
		margin-bottom: 0;
	}
</style>

<div class="vr-print-order-wrapper">

	<!-- HEAD -->

	<div class="tk-print-box">

		<div class="tk-field">
			<span class="tk-label"><?php echo JText::translate('VRORDERNUMBER'); ?></span>
			<span class="tk-value"><?php echo $order->id . ' - ' . $order->sid; ?></span>
		</div>

		<div class="tk-field">
			<span class="tk-label"><?php echo JText::translate('VRORDERSTATUS'); ?></span>
			<span class="tk-value">
				<?php echo JHtml::fetch('vrehtml.status.display', $order->status); ?>
			</span>
		</div>

		<div class="tk-field">
			<span class="tk-label"><?php echo JText::translate('VRORDERDATETIME'); ?></span>
			<span class="tk-value"><?php echo date($date_format . ' ' . $time_format, $order->checkin_ts); ?></span>
		</div>

		<div class="tk-field">
			<span class="tk-label"><?php echo JText::translate('VRORDERPEOPLE'); ?></span>
			<span class="tk-value"><?php echo $order->people; ?></span>
		</div>

		<div class="tk-field">
			<span class="tk-label"><?php echo JText::translate('VRORDERTABLE'); ?></span>
			<span class="tk-value"><?php echo $order->room->name . ' - ' . implode(', ', $tables); ?></span>
		</div>

		<?php if ($order->payment): ?>
			<div class="tk-field">
				<span class="tk-label"><?php echo JText::translate('VRORDERPAYMENT'); ?></span>
				<span class="tk-value">
					<?php
					echo $order->payment->name;

					if ($order->payment_charge > 0)
					{
						echo ' (' . $currency->format($order->payment_charge + $order->payment_tax) . ')';
					}
					?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ($order->deposit > 0): ?>
			<div class="tk-field">
				<span class="tk-label"><?php echo JText::translate('VRMANAGERESERVATION9'); ?></span>
				<span class="tk-value">
					<?php echo $currency->format($order->deposit); ?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ($order->coupon): ?>
			<div class="tk-field">
				<span class="tk-label"><?php echo JText::translate('VRORDERCOUPON'); ?></span>
				<span class="tk-value">
					<?php
					echo $order->coupon->code . ' : ';

					if ($order->coupon->type == 1)
					{
						echo $currency->format($order->coupon->amount, [
							'symbol'     => '%',
							'position'   => 1,
							'space'      => false,
							'no_decimal' => true,
						]);
					}
					else
					{
						echo $currency->format($order->coupon->amount);
					}
					?>
				</span>
			</div>
		<?php endif; ?>

		<?php if ($order->notes): ?>
			<div class="order-note">
				<?php echo $order->notes; ?>
			</div>
		<?php endif; ?>
		
	</div>

	<!-- CUSTOMER DETAILS -->

	<?php if ($order->hasFields): ?>
		<div class="tk-print-box">
			<?php foreach ($order->displayFields as $k => $v): ?>
				<div class="tk-field">
					<span class="tk-label"><?php echo $k; ?></span>
					<span class="tk-value"><?php echo nl2br($v); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- ITEMS -->

	<?php if ($order->items): ?>
		<div class="tk-print-box">
			<?php foreach ($order->items as $item): ?>
				<div class="tk-item">
					<div class="tk-details">
						<span class="name">
							<span class="quantity"><?php echo $item->quantity; ?>x</span>
							<?php echo $item->name; ?>
						</span>
						<span class="price"><?php echo $currency->format($item->gross); ?></span>
					</div>

					<?php if ($item->notes || $useServingNumber): ?>
						<div class="tk-notes">
							<?php
							echo implode(' | ', array_filter([
								$useServingNumber ? JText::translate('VRE_ORDERDISH_SERVING_NUMBER_' . $item->servingnumber) : '',
								$item->notes,
							]));
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- ORDER TOTAL -->

	<?php if ($order->bill_value > 0): ?>
		
		<div class="tk-print-box">

			<!-- TOTAL NET -->

			<?php if ($order->tip_amount || $order->discount_val || $order->payment_charge || $order->total_tax): ?>
				<div class="tk-total-row">
					<span class="tk-label"><?php echo JText::translate('VRTKCARTTOTALNET'); ?></span>
					<span class="tk-amount"><?php echo $currency->format($order->total_net + $order->discount_val + $order->payment_charge); ?></span>
				</div>
			<?php endif; ?>

			<!-- DISCOUNT -->

			<?php if ($order->discount_val > 0): ?>
				<div class="tk-total-row">
					<span class="tk-label"><?php echo JText::translate('VRTKCARTTOTALDISCOUNT'); ?></span>
					<span class="tk-amount"><?php echo $currency->format($order->discount_val * -1); ?></span>
				</div>
			<?php endif; ?>

			<!-- TOTAL TAX -->

			<?php if ($order->total_tax > 0): ?>
				<div class="tk-total-row">
					<span class="tk-label"><?php echo JText::translate('VRINVTAXES'); ?></span>
					<span class="tk-amount"><?php echo $currency->format($order->total_tax); ?></span>
				</div>
			<?php endif; ?>

			<!-- TIP -->

			<?php if ($order->tip_amount > 0): ?>
				<div class="tk-total-row">
					<span class="tk-label"><?php echo JText::translate('VRTKCARTTOTALTIP'); ?></span>
					<span class="tk-amount"><?php echo $currency->format($order->tip_amount); ?></span>
				</div>
			<?php endif; ?>

			<!-- GRAND TOTAL -->

			<div class="tk-total-row">
				<span class="tk-label"><?php echo JText::translate('VRTKCARTTOTALPRICE'); ?></span>
				<span class="tk-amount"><?php echo $currency->format($order->bill_value); ?></span>
			</div>

		</div>
	
	<?php endif; ?>

</div>