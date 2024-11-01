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

$totalCostBeforeDiscount = 0;

?>

<div class="vrorderboxcontent">

	<h3 class="vrorderheader"><?php echo JText::translate('VRTKORDERTITLE3'); ?></h3>

	<!-- ITEMS -->

	<div class="vrordercontentinfo">

		<?php foreach ($order->items as $item): ?>
			<div class="vrtk-order-food">

				<div class="vrtk-order-food-details">

					<div class="vrtk-order-food-details-left">
						<span class="vrtk-order-food-details-name"><?php echo $item->name; ?></span>
					</div>

					<div class="vrtk-order-food-details-right">
						<span class="vrtk-order-food-details-quantity">x<?php echo $item->quantity; ?></span>

						<span class="vrtk-order-food-details-price">
							<?php
							$totalCostBeforeDiscount += $item->price * $item->quantity;
							echo $currency->format($item->price * $item->quantity);
							?>
						</span>
					</div>

				</div>

				<?php if ($item->toppings): ?>
					<div class="vrtk-order-food-middle">
						<?php foreach ($item->toppings as $group): ?>
							<div class="vrtk-order-food-group">
								<span class="vrtk-order-food-group-title"><?php echo $group->title; ?>:</span>
								
								<span class="vrtk-order-food-group-toppings">
									<?php echo $group->str; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				
				<?php if (!empty($item->notes)): ?>
					<div class="vrtk-order-food-notes">
						<?php echo $item->notes; ?>
					</div>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>

	</div>

	<?php if ($order->total_to_pay > 0): ?>

		<div class="vrorder-grand-total">

			<!-- DISCOUNT -->

			<?php if ($order->discount_val > 0): ?>
				<div class="grand-total-row total-before-discount">
					<span class="label">&nbsp;</span>
					<span class="amount"><?php echo $currency->format($totalCostBeforeDiscount); ?></span>
				</div>

				<div class="grand-total-row total-discount red">
					<span class="label"><?php echo JText::translate('VRTKCARTTOTALDISCOUNT'); ?></span>
					<span class="amount"><?php echo $currency->format($order->discount_val * -1); ?></span>
				</div>
			<?php endif; ?>

			<!-- NET -->
			
			<?php if ($order->total_net != $order->total_to_pay): ?>
				<div class="grand-total-row total-net">
					<span class="label"><?php echo JText::translate('VRTKCARTTOTALNET'); ?></span>
					<span class="amount"><?php echo $currency->format($order->total_net); ?></span>
				</div>
			<?php endif; ?>

			<!-- SERVICE -->
			
			<?php if ($order->delivery_charge != 0): ?>
				<div class="grand-total-row service-charge">
					<span class="label"><?php echo JText::translate('VRTKCARTTOTALSERVICE'); ?></span>
					<span class="amount"><?php echo $currency->format($order->delivery_charge); ?></span>
				</div>
			<?php endif; ?>

			<!-- PAYMENT -->

			<?php if ($order->payment_charge != 0): ?>
				<div class="grand-total-row payment-charge">
					<span class="label"><?php echo JText::translate('VRTKCARTTOTALPAYCHARGE'); ?></span>
					<span class="amount"><?php echo $currency->format($order->payment_charge); ?></span>
				</div>
			<?php endif; ?>

			<!-- TAXES -->
			
			<?php if ($order->total_tax > 0): ?>
				<div class="grand-total-row total-taxes red">
					<span class="label"><?php echo JText::translate('VRTKCARTTOTALTAXES'); ?></span>
					<span class="amount"><?php echo $currency->format($order->total_tax); ?></span>
				</div>
			<?php endif; ?>

			<!-- GRATUITY -->

			<?php if ($order->tip_amount > 0): ?>
				<div class="grand-total-row tip-amount">
					<span class="label"><?php echo JText::translate('VRTKCARTTOTALTIP'); ?></span>
					<span class="amount"><?php echo $currency->format($order->tip_amount); ?></span>
				</div>
			<?php endif; ?>

			<!-- GRAND TOTAL -->

			<div class="grand-total-row grand-total">
				<span class="label"><?php echo JText::translate('VRTKCARTTOTALPRICE'); ?></span>
				<span class="amount"><?php echo $currency->format($order->total_to_pay); ?></span>
			</div>

		</div>

	<?php endif; ?>

</div>