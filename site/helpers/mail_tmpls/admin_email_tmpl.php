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
 * VikRestaurants - Restaurant Administrator E-Mail Template
 *
 * @var object  $reservation  It is possible to use this variable to 
 * 							  access the details of the reservation.
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

			<!-- CHECK-IN DATE AND PEOPLE -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 0 auto 0; padding: 10px 25px;" class="order-summary-table">
						<tr>
							<td style="line-height: 1.4em; font-size: 14px; text-align: left;" class="order-summary-block">
								<div style="float:left; display:inline-block;" class="summary-left-side">
									{order_date_time}
								</div>
								<div style="float:right; display:inline-block;" class="summary-right-side">
									{order_people}
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- TOTAL COST AND PAYMENT GATEWAY -->

			<?php if ($reservation->deposit > 0): ?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 0 auto 0; padding: 10px 25px;" class="order-summary-table">
							<tr>
								<td style="line-height: 1.4em; font-size: 14px; text-align: left;" class="order-summary-block">
									<div style="float:left; display:inline-block;" class="summary-left-side">
										{order_payment}
									</div>
									<div style="float:right; display:inline-block;" class="summary-right-side">
										{order_deposit}
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>

			<!-- COUPON CODE -->

			<?php if (!empty($reservation->coupon)): ?>
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

			<!-- CUSTOM POSITION MIDDLE -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-middle">
					{custom_position_middle}
				</td>
			</tr>

			<!-- SELECTED MENUS -->

			<?php if (count($reservation->menus)): ?>
				<tr>
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 0; border-top: 2px solid #ddd;" class="order-summary-table">
							<tr>
								<td style="padding: 0; line-height: 1.4em; font-size: 14px; text-align: left;" class="order-menus-block">
									<h3 style="padding: 12px 25px 0; margin: 0;">
										<?php echo JText::translate('VRORDERTITLE3'); ?>
									</h3>
									<div style="padding: 10px 25px;" class="menus-list">
										<?php foreach ($reservation->menus as $menu): ?>
											<div style="padding: 2px 0;display: inline-block;width: 100%;" class="menu-item">
												<div style="float: left;" class="menu-item-left-side">
													<?php echo $menu->name; ?>
												</div>
												<div style="float: right;" class="menu-item-right-side">
													x<?php echo $menu->quantity; ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?php endif; ?>

			<!-- PURCHASED ITEMS -->

			<?php if ($reservation->items): ?>
				<tr>
					<td style="padding: 0; font-size: 14px; line-height: 1.4em; text-align: center; border-top: 2px solid #ddd;" class="order-item-line">
						<?php foreach ($reservation->items as $item): ?>
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
			<?php endif; ?>

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
									<?php foreach ($reservation->displayFields as $label => $value): ?>
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

			<!-- CONFIRMATION LINK -->

			<?php if ($reservation->statusRole == 'PENDING'): ?>
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

			<!-- REJECTION LINK -->

			<?php if ($reservation->statusRole == 'PENDING'): ?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 5px auto 0; padding: 0;" class="link-table">
							<tr>
								<td style="padding: 0; line-height: 1.4em; font-size: 14px; text-align: left;" class="link-block">
									<h3 style="padding: 12px 25px 0; margin: 0;">
										<?php echo JText::translate('VRREJECTIONLINK'); ?>
									</h3>
									<div style="padding: 10px 25px;" class="link-container">
										<a href="{rejection_link}" target="_blank" style="word-break: break-word;">{rejection_link}</a>
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
 * @var string|null  {logo}                 The logo image of your company. Null if not specified.
 * @var integer      {order_number}         The unique ID of the reservation.
 * @var string       {order_key}            The serial key of the reservation.
 * @var string       {order_date_time}      The checkin date and time of the reservation.
 * @var string       {order_people}         The party size of the reservaion.
 * @var string       {order_status}         The status of the order [CONFIRMED, PENDING, REMOVED or CANCELLED].
 * @var string|null  {order_payment}        The name of the payment processor selected (*), otherwise NULL.
 * @var string|null  {order_payment_notes}  The notes of the payment processor selected, otherwise NULL.
 * @var string       {order_deposit}        The formatted deposit to leave for the reservation.
 * @var string       {order_coupon_code}    The coupon code used for the order.
 * @var string       {order_room}           The room in which the reservation was placed.
 * @var string       {order_room_table}     The table of the room in which the reservation was placed.
 * @var string       {order_link}           The direct url to the page of the order.
 * @var string       {confirmation_link}    The direct url to confirm the order. Available only if the status of the order is PENDING.
 * @var string       {rejection_link}       The direct url to reject the order. Available only if the status of the order is PENDING.
 * @var string|null  {company_name}         The name of the company.
 * @var string|null  {user_name}            The name of the user account.
 * @var string|null  {user_username}        The username of the user account.
 * @var string|null  {user_email}           The e-mail address of the user account.
 */
