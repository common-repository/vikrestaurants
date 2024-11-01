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

$order = $this->order;

$currency = VREFactory::getCurrency();

?>
	
<div class="vrorderboxcontent">

	<h3 class="vrorderheader"><?php echo JText::translate('VRTKORDERTITLE1'); ?></h3>

	<div class="vrordercontentinfo">

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERNUMBER'); ?></span>
			<span class="orderinfo-value"><?php echo $order->id; ?></span>
		</div>

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERKEY'); ?></span>
			<span class="orderinfo-value"><?php echo $order->sid; ?></span>
		</div>

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERSTATUS'); ?></span>
			<span class="orderinfo-value">
				<?php
				echo JHtml::fetch('vrehtml.status.display', $order->status);

				/**
				 * Check whether the user is able to self-confirm the order.
				 * 
				 * @since 1.8
				 */
				if (VikRestaurants::canUserApproveOrder($order))
				{
					// display a tooltip to inform the user that the order should
					// be confirmed by clicking the apposite link received via e-mail
					JHtml::fetch('bootstrap.tooltip', '.status-help');

					?>
					<i class="fas fa-question-circle status-help" title="<?php echo $this->escape(JText::translate('VRE_ORDER_APPROVE_HELP')); ?>"></i>
					<?php
				}
				?>
			</span>
		</div>
		
		<?php if ($order->payment): ?>
			<br clear="all"/>

			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo JText::translate('VRORDERPAYMENT'); ?></span>
				<span class="orderinfo-value">
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

		<?php if ($order->total_to_pay): ?>

			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo JText::translate('VRTKORDERTOTALTOPAY'); ?></span>
				<span class="orderinfo-value"><?php echo $currency->format($order->total_to_pay); ?></span>
			</div>

			<?php if ($order->tot_paid > 0): ?>
				<div class="vrorderinfo">
					<span class="orderinfo-label"><?php echo JText::translate('VRORDERDEPOSIT'); ?></span>
					<span class="orderinfo-value"><?php echo $currency->format($order->tot_paid); ?></span>
				</div>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ($order->coupon): ?>
			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo JText::translate('VRORDERCOUPON'); ?></span>
				<span class="orderinfo-value">
					<?php 
					echo $order->coupon->code;

					if ($order->coupon->amount > 0)
					{
						if ($order->coupon->type == 1)
						{
							echo ' ' . $order->coupon->amount . '%';
						}
						else
						{
							echo ' ' . $currency->format($order->coupon->amount);
						}
					}
					?>
				</span>
			</div>
		<?php endif; ?>

	</div>

</div>