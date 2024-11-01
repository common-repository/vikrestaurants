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

/**
 * VikRestaurants - Take-Away Customer E-Mail Template
 *
 * @var object  $order  It is possible to use this variable to 
 * 						access the details of the order.
 *
 * @see the bottom of the page to check the available TAGS to use.
 */

?>

<style>
	@media print {
		.no-printable {
			display: none;
		}
	}
</style>

<div style="background:#f6f6f6; color: #666; width: 100%; padding: 10px 0; table-layout: fixed;" class="vreBackground">
	<div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 25px 0;" class="vreBody">

		<!--[if (gte mso 9)|(IE)]>
		<table width="800" align="center">
		<tr>
		<td>
		<![endif]-->

		<table align="center" style="border-collapse: separate; margin: 0 auto; width: 100%; max-width: 600px; border-spacing: 0; font-family: sans-serif;">
			
			<!-- TOP BOX [company logo and name] -->

			<tr>
				<td style="padding: 0 25px;" class="heading-block">
					<div style="display: inline-block; float: left; max-width: 150px;" class="heading-left-side">{logo}</div>
					<h1 style="display: inline-block; float: right; margin: 0;" class="heading-right-side">{company_name}</h1>
				</td>
			</tr>

			<!-- CUSTOM POSITION TOP -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-top">
					{custom_position_top}
				</td>
			</tr>

			<!-- ORDER NUMBER BOX -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 10px auto 0; padding: 15px 15px 5px;" class="top-table">
						<tr>
							<td style="padding: 0 10px; text-align: center;" class="top-block">
								<h2><?php echo JText::translate('VRORDERNUMBER'); ?> #{order_number}</h2>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- ORDER KEY AND STATUS -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 10px auto 0; padding: 10px 25px; border-top: 2px solid #ddd;" class="order-summary-table">
						<tr>
							<td style="line-height: 1.4em; font-size: 14px; text-align: left;" class="order-summary-block">
								<div style="float:left; display:inline-block;" class="summary-left-side">
									{order_key}
								</div>
								<div style="float:right; display:inline-block;" class="summary-right-side">
									{order_status}
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CHECK-IN DATE AND DELIVERY SERVICE -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 0 auto 0; padding: 10px 25px;" class="order-summary-table">
						<tr>
							<td style="line-height: 1.4em; font-size: 14px; text-align: left;" class="order-summary-block">
								<div style="float:left; display:inline-block;" class="summary-left-side">
									{order_date_time}
								</div>
								<div style="float:right; display:inline-block;" class="summary-right-side">
									{order_delivery_service}
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- TOTAL COST AND PAYMENT GATEWAY -->

			<?php if ($order->total_to_pay > 0): ?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 0 auto 0; padding: 10px 25px;" class="order-summary-table">
							<tr>
								<td style="line-height: 1.4em; font-size: 14px; text-align: left;" class="order-summary-block">
									<div style="float:left; display:inline-block;" class="summary-left-side">
										{order_payment}
									</div>
									<div style="float:right; display:inline-block;" class="summary-right-side">
										{order_total_cost}
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>

			<!-- COUPON CODE -->

			<?php if (!empty($order->coupon)): ?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 0 auto 0; padding: 10px 25px;" class="order-summary-table">
							<tr>
								<td style="line-height: 1.4em; font-size: 14px; text-align: left;" class="order-summary-block">
									<div style="float:left; display:inline-block;" class="summary-left-side">
										<?php echo JText::translate('VRORDERCOUPON'); ?>
									</div>
									<div style="float:right; display:inline-block;" class="summary-right-side">
										{order_coupon_code}
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>

			<!-- TRACKING LINK -->

			<?php if ($order->statusRole == 'APPROVED'): ?>
				<tr class="no-printable">
					<td style="padding: 14px 0; text-align: center; border-top: 2px solid #ddd;">
						{track_order_link}
					</td>
				</tr>
			<?php endif; ?>

			<!-- CUSTOM POSITION MIDDLE -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-middle">
					{custom_position_middle}
				</td>
			</tr>

			<!-- PURCHASED ITEMS -->

			<tr>
				<td style="padding: 0; font-size: 14px; line-height: 1.4em; text-align: center; border-top: 2px solid #ddd;" class="order-item-line">
					<?php foreach ($order->items as $item): ?>
						<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 10px 25px;" class="order-item-table">
							<tr>
								<td style="text-align: left; width: 1%;" class="item-units">
									<small><?php echo $item->quantity; ?>x</small>
								</td>
								<td style="text-align: left; width: 74%;" class="item-name">
									<?php echo $item->name; ?>
								</td>
								<td style="text-align: right; width: 25%;" class="item-price">
									<?php echo $item->formattedPrice; ?>
								</td>
							</tr>
							
							<?php foreach ($item->toppings as $group): ?>
								<tr>
									<td colspan="3" style="text-align: left; font-size: smaller; padding: 5px 0 0 10px;" class="item-topping">
										<span><?php echo $group->title; ?>:</span>
										<em><?php echo $group->str; ?></em>
									</td>
								</tr>
							<?php endforeach; ?>

							<?php if ($item->notes): ?>
								<tr>
									<td colspan="3" style="text-align: left; font-size: 12px; padding-top: 5px;" class="item-notes">
										<em><?php echo $item->notes; ?></em>
									</td>
								</tr>
							<?php endif; ?>
						</table>
					<?php endforeach; ?>
				</td>
			</tr>

			<!-- GRAND TOTAL -->

			<tr>
				<td style="padding: 10px 25px; font-size: 12px; border-top: 2px solid #ddd; text-align: center;" class="order-totals">
					<table width="100%" style="border-collapse: separate; border-spacing: 0;">
						<tr>
							<td style="text-align: right; width: 80%; padding: 4px 0;" class="total-label">
								<?php echo JText::translate('VRTKCARTTOTALNET'); ?>
							</td>
							<td style="text-align: right; width: 20%; padding: 4px 0;" class="total-net">
								{order_total_net}
							</td>
						</tr>

						<?php if ($order->delivery_charge != 0): ?>
							<tr>
								<td style="text-align: right; width: 80%; padding: 4px 0;" class="total-label">
									<?php echo JText::translate('VRTKCARTDELIVERYCHARGE'); ?>
								</td>
								<td style="text-align: right; width: 20%; padding: 4px 0;" class="service-charge">
									{order_delivery_charge}
								</td>
							</tr>
						<?php endif; ?>

						<?php if ($order->tip_amount > 0): ?>
							<tr>
								<td style="text-align: right; width: 80%; padding: 4px 0;" class="total-label">
									<?php echo JText::translate('VRTKCARTTOTALTIP'); ?>
								</td>
								<td style="text-align: right; width: 20%; padding: 4px 0;" class="tip-amount">
									{order_total_tip}
								</td>
							</tr>
						<?php endif; ?>

						<?php if ($order->total_tax > 0): ?>
							<tr>
								<td style="text-align: right; width: 80%; padding: 4px 0;" class="total-label">
									<?php echo JText::translate('VRTKCARTTOTALTAXES'); ?>
								</td>
								<td style="text-align: right; width: 20%; padding: 4px 0;" class="total-tax">
									{order_total_tax}
								</td>
							</tr>
						<?php endif; ?>

						<tr>
							<td style="text-align: right; width: 80%; padding: 4px 0;font-size: 18px;" class="grand-total-label">
								<?php echo JText::translate('VRTKCARTTOTALPRICE'); ?>
							</td>
							<td style="text-align: right; width: 20%; padding: 4px 0;font-size: 18px;" class="grand-total-value">
								{order_total_cost}
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CUSTOMER DETAILS -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 0; border-top: 2px solid #ddd;" class="customer-table">
						<tr>
							<td style="padding: 0; line-height: 1.4em; font-size: 14px; text-align: left;" class="customer-block">
								<h3 style="padding: 12px 25px 0; margin: 0;">
									<?php echo JText::translate('VRPERSONALDETAILS'); ?>
								</h3>
								<div style="padding: 10px 25px;" class="customer-details">
									<?php foreach ($order->displayFields as $label => $value): ?>
										<div style="padding: 2px 0;" class="customer-info">
											<div style="display: inline-block; width: 180px; vertical-align: top;" class="customer-info-label">
												<?php echo $label; ?>
											</div>
											<div style="display: inline-block;" class="customer-info-value">
												<?php echo nl2br($value); ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CUSTOM POSITION BOTTOM -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-bottom">
					{custom_position_bottom}
				</td>
			</tr>

			<!-- ORDER LINK -->

			<tr class="no-printable">
				<td style="padding: 0; text-align: center; border-top: 2px solid #ddd;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 0;" class="link-table">
						<tr>
							<td style="padding: 0; line-height: 1.4em; font-size: 14px; text-align: left;" class="link-block">
								<h3 style="padding: 12px 25px 0; margin: 0;">
									<?php echo JText::translate('VRORDERLINK'); ?>
								</h3>
								<div style="padding: 10px 25px;" class="link-container">
									<a href="{order_link}" target="_blank" style="word-break: break-word;">{order_link}</a>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CANCELLATION LINK -->

			<?php if (VikRestaurants::canUserCancelOrder($order)): ?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 5px auto 0; padding: 0;" class="link-table">
							<tr>
								<td style="padding: 0; line-height: 1.4em; font-size: 14px; text-align: left;" class="link-block">
									<h3 style="padding: 12px 25px 0; margin: 0;">
										<?php echo JText::translate('VRCANCELORDERTITLE'); ?>
									</h3>
									<div style="padding: 10px 25px;" class="link-container">
										<a href="{cancellation_link}" target="_blank" style="word-break: break-word;">{cancellation_link}</a>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>

			<!-- CONFIRMATION LINK -->

			<?php if (VikRestaurants::canUserApproveOrder($order)): ?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 5px auto 0; padding: 0;" class="link-table">
							<tr>
								<td style="padding: 0; line-height: 1.4em; font-size: 14px; text-align: left;" class="link-block">
									<h3 style="padding: 12px 25px 0; margin: 0;">
										<?php echo JText::translate('VRCONFIRMATIONLINK'); ?>
									</h3>
									<div style="padding: 10px 25px;" class="link-container">
										<a href="{confirmation_link}" target="_blank" style="word-break: break-word;">{confirmation_link}</a>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>

			<!-- CUSTOM POSITION FOOTER -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-footer">
					{custom_position_footer}
				</td>
			</tr>

		</table>

		<!--[if (gte mso 9)|(IE)]>
		</td>
		</tr>
		</table>
		<![endif]-->

	</div>
</div>

<?php
/**
 * @var string|null  {logo}                    The logo image of your company. Null if not specified.
 * @var integer      {order_number}            The unique ID of the order.
 * @var string       {order_key}               The serial key of the order.
 * @var string       {order_date_time}         The checkin date and time of the order.
 * @var string       {order_people}            The party size of the reservaion.
 * @var string       {order_status}            The status of the order [CONFIRMED, PENDING, REMOVED or CANCELLED].
 * @var string       {order_status_color}      The color related to the selected status.
 * @var string|null  {order_payment}           The name of the payment processor selected (*), otherwise NULL.
 * @var string|null  {order_payment_notes}     The notes of the payment processor selected, otherwise NULL.
 * @var string       {order_total_cost}        The formatted total cost of the order.
 * @var string       {order_total_net}         The formatted total net of the order.
 * @var string       {order_delivery_charge}   The formatted delivery charge of the order.
 * @var string       {order_total_tip}         The formatted total tip left for the order.
 * @var string       {order_total_tax}         The formatted total taxes of the order.
 * @var string       {order_delivery_service}  The service of the order: delivery or pickup.
 * @var string       {order_coupon_code}       The coupon code used for the order.
 * @var string       {order_link}              The direct url to the page of the order.
 * @var string       {cancellation_link}       The direct url to cancel the order. Available only if the cancellation is allowed and the status of the order is CONFIRMED.
 * @var string       {confirmation_link}       The direct url to confirm the order. Available only in case the self-confirmation is allowed.
 * @var string       {track_order_link}        The text and the link to see the page to track the order.
 * @var string|null  {company_name}            The name of the company.
 * @var string|null  {user_name}               The name of the user account.
 * @var string|null  {user_username}           The username of the user account.
 * @var string|null  {user_email}              The e-mail address of the user account.
 */
