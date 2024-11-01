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

$reservation = $this->reservation;

$config   = VREFactory::getConfig();
$currency = VREFactory::getCurrency();

?>
	
<div class="vrorderboxcontent">

	<h3 class="vrorderheader"><?php echo JText::translate('VRORDERTITLE1'); ?></h3>

	<div class="vrordercontentinfo">

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERNUMBER'); ?></span>
			<span class="orderinfo-value"><?php echo $reservation->id; ?></span>
		</div>

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERKEY'); ?></span>
			<span class="orderinfo-value"><?php echo $reservation->sid; ?></span>
		</div>

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERSTATUS'); ?></span>
			<span class="orderinfo-value">
				<?php
				echo JHtml::fetch('vrehtml.status.display', $reservation->status);

				/**
				 * Check whether the user is able to self-confirm the reservation.
				 * 
				 * @since 1.8
				 */
				if (VikRestaurants::canUserApproveOrder($reservation))
				{
					// display a tooltip to inform the user that the reservation should
					// be confirmed by clicking the apposite link received via e-mail
					JHtml::fetch('bootstrap.tooltip', '.status-help');

					?>
					<i class="fas fa-question-circle status-help" title="<?php echo $this->escape(JText::translate('VRE_RESERVATION_APPROVE_HELP')); ?>"></i>
					<?php
				}
				?>
			</span>
		</div>
		
		<?php if ($reservation->payment): ?>
			<br clear="all"/>

			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo JText::translate('VRORDERPAYMENT'); ?></span>
				<span class="orderinfo-value">
					<?php
					echo $reservation->payment->name;

					if ($reservation->payment_charge > 0)
					{
						echo ' (' . $currency->format($reservation->payment_charge + $reservation->payment_tax) . ')';
					}
					?>
				</span>
			</div>
		<?php endif; ?>

		<?php
		$total = (float) max($reservation->bill_value, $reservation->deposit);
		if ($total): ?>

			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo JText::translate($reservation->bill_value > 0 ? 'VREORDERFOOD_BILL_AMOUNT' : 'VRORDERRESERVATIONCOST'); ?></span>
				<span class="orderinfo-value"><?php echo $currency->format($total); ?></span>
			</div>

			<?php if ($reservation->tot_paid > 0): ?>
				<div class="vrorderinfo">
					<span class="orderinfo-label"><?php echo JText::translate('VRORDERDEPOSIT'); ?></span>
					<span class="orderinfo-value"><?php echo $currency->format($reservation->tot_paid); ?></span>
				</div>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ($reservation->coupon): ?>
			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo JText::translate('VRORDERCOUPON'); ?></span>
				<span class="orderinfo-value">
					<?php 
					echo $reservation->coupon->code;

					if ($reservation->coupon->amount > 0)
					{
						if ($reservation->coupon->type == 1)
						{
							echo ' ' . $reservation->coupon->amount . '%';
						}
						else
						{
							echo ' ' . $currency->format($reservation->coupon->amount);
						}
					}
					?>
				</span>
			</div>
		<?php endif; ?>

		<?php
		// check if the customer is allowed to order the dishes online
		if ($config->getUint('orderfood')): ?>
			<br clear="all"/>

			<div id="vr-order-dishes-button">
				<button type="button" class="vre-btn primary large">
					<i class="fas fa-shopping-basket"></i>
					<span><?php echo JText::translate('VREORDERFOOD'); ?></span>
				</button>
			</div>

			<script>
				(function($) {
					'use strict';

					$(function() {
						// get button to start ordering the dishes
						const btn = $('#vr-order-dishes-button').find('button');

						btn.on('click', () => {
							<?php
							/**
							 * Check if the customer can currently order food.
							 *
							 * @since 1.8.1  Still allow access to order view in case the bill has been closed
							 * 				 and the payment method haven't been yet selected.
							 */
							$canOrderFood = VikRestaurants::canUserOrderFood($reservation, $errmsg)
								|| (VikRestaurants::hasPayment(1) && $reservation->bill_closed && $reservation->id_payment <= 0);

							if ($canOrderFood): ?>
								document.location.href = '<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=orderdishes&ordnum=' . $reservation->id . '&ordkey=' . $reservation->sid . ($this->itemid ? '&Itemid=' . $this->itemid : ''), false); ?>';
							<?php else: ?>
								alert('<?php echo addslashes($errmsg); ?>');
							<?php endif; ?>
						});
					});
				})(jQuery);
			</script>
		<?php endif; ?>

	</div>

</div>