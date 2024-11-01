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
 * VikRestaurants - Restaurant Cancellation E-Mail Template
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
			
			<!-- TOP BOX [logo and cancellation content] -->

			<tr>
				<td style="padding: 0 25px; text-align: center;">
					<div style="display: inline-block; width: 200px; margin-bottom: 20px;" class="heading-logo">{logo}</div>
					<div style="margin: 10px auto; line-height: 1.4em; font-size: 14px;" class="heading-description">{cancellation_content}</div>
				</td>
			</tr>

			<!-- CUSTOM POSITION TOP -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-top">
					{custom_position_top}
				</td>
			</tr>

			<!-- CANCELLATION REASON -->

			<?php if ($reservation->cancellation_reason): ?>
				<tr>
					<td style="text-align: center; line-height: 1.3em; font-size: 13px; padding: 10px;" class="cancellation_reason">
						{cancellation_reason}
					</td>
				</tr>
			<?php endif; ?>

			<!-- ORDER LINK -->

			<tr class="no-printable">
				<td style="padding: 0;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 0;" class="link-table">
						<tr>
							<td style="padding: 0; line-height: 1.4em; font-size: 14px; padding: 10px 0; text-align: center;" class="link-block">
								<a href="{order_link}" target="_blank" style="word-break: break-word;">{order_link}</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<!-- CUSTOM POSITION MIDDLE -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-middle">
					{custom_position_middle}
				</td>
			</tr>

			<!-- RESERVATION DETAILS -->

			<tr>
				<td style="padding: 0; text-align: center;">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 10px auto 0;">
						<tr>
							<td style="line-height: 1.4em; font-size: 14px; border-top: 2px solid #ddd; text-align: left;" class="order-details">
								<div style="display: inline-block; width: 100%; padding: 10px 25px; box-sizing: border-box;" class="order-details-line">
									<div style="float: left; display: inline-block;" class="order-details-left">
										{order_number} - {order_key}
									</div>
									<div style="float: right; display: inline-block;" class="order-details-right">
										{order_status}
									</div>
								</div>
								<div style="padding: 10px 25px;display: inline-block; width: 100%; box-sizing: border-box;" class="order-details-line">
									<div style="float: left; display: inline-block;" class="order-details-left">
										{order_date_time}
									</div>
									<div style="float: right; display: inline-block;" class="order-details-right">
										{order_people}
									</div>
								</div>
								<div style="padding: 10px 25px;" class="order-details-line">
									{order_room} - {order_room_table}
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
 * @var string|null  {logo}                  The logo image of your company.
 * @var string|null  {company_name}          The name of the company.
 * @var integer      {order_number}          The unique ID of the reservation.
 * @var string       {order_key}             The serial key of the reservation.
 * @var string       {order_status}          The status of the order.
 * @var string       {order_date_time}       The checkin date and time of the reservation.
 * @var string       {order_people}          The party size of the reservaion.
 * @var string       {order_room}            The room in which the reservation was placed.
 * @var string       {order_room_table}      The table of the room in which the reservation was placed.
 * @var string|null  {cancellation_content}  The content specified in the language file at VRORDERCANCELLEDCONTENT.
 * @var string       {cancellation_reason}   The cancellation reason specified by the customer (according to the configuration).
 * @var string       {order_link}            The direct url to the details page of the reservation.
 */
