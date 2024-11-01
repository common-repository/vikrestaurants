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
 * VikRestaurants - Take-Away Review E-Mail Template
 *
 * @var object  $review  It is possible to use this variable to 
 * 						 access the details of the review.
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
			
			<!-- TOP BOX [logo and review content] -->

			<tr>
				<td style="padding: 0 25px; text-align: center;">
					<div style="display: inline-block; width: 200px; margin-bottom: 20px;" class="heading-logo">{logo}</div>
					<div style="margin: 10px auto; line-height: 1.4em; font-size: 14px;" class="heading-description">{review_content}</div>
				</td>
			</tr>

			<!-- CUSTOM POSITION TOP -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-top">
					{custom_position_top}
				</td>
			</tr>

			<!-- PRODUCT DETAILS -->

			<tr>
				<td style="padding: 10px; font-size: 14px; line-height: 1.4em; border-top: 2px solid #ddd; text-align: center;" class="product-details">
					<table width="100%" style="border-collapse: separate; border-spacing: 0;">
						<tr>
							<?php if ($review->productImage): ?>
								<td width="30%" style="vertical-align: top; padding-right: 10px;" class="product-image">
									<img src="{review_product_image}" alt="{review_product_menu} - {review_product_name}" style="max-width:100%;" />
								</td>
							<?php endif; ?>

							<td style="vertical-align: top; text-align: left;" width="<?php echo $review->productImage ? '70%' : 'auto'; ?>" class="product-text">
								<div class="product-text-line">
									<h3 style="margin: 0 0 8px;">{review_product_menu} - {review_product_name}</h3>
								</div>
								<div class="product-text-line">
									{review_product_desc}
								</div>
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

			<!-- REVIEW DETAILS -->

			<tr>
				<td style="padding: 15px 10px 0;" class="review-outer-details">
					<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 10px; font-size: 14px; border: 1px solid #ddd;" class="review-details">
						<tr>
							<td class="review-top">
								<span style="float: left;" class="review-rating">{review_rating}</span>

								<span style="float: right; line-height: 26px; font-size: smaller;" class="review-verified">{review_verified}</span>
							</td>
						</tr>
						<tr>
							<td style="text-align: left; padding-top: 10px;" class="review-text">
								<div style="font-weight: bold; font-size: 16px; margin-bottom: 10px;" class="review-title">
									{review_title}
								</div>
								<div class="review-comment">
									<?php if ($review->comment): ?>
										{review_comment}
									<?php else: ?>
										<small><em><?php echo JText::translate('VRREVIEWNOCOMMENT'); ?></em></small>
									<?php endif; ?>
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

			<!-- CONFIRMATION LINK -->

			<?php if (!$review->published): ?>
				<tr class="no-printable">
					<td style="padding: 0; text-align: center;">
						<table width="100%" style="border-collapse: separate; border-spacing: 0; margin: 5px auto 0; padding: 0; font-size: 14px;">
							<tr>
								<td style="padding: 0; line-height: 1.4em; text-align: left;">
									<div style="padding: 0px 10px 0;"><strong><?php echo JText::translate('VRCONFIRMATIONLINK'); ?></strong></div>
									<div style="padding: 10px;">
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
 * @var string|null  {logo}                  The logo image of your company.
 * @var string|null  {company_name}          The name of the company.
 * @var string       {review_content}        The content specified in the language file at VRREVIEWCONTENT.
 * @var string       {review_product_menu}   The menu name of the reviewed product.
 * @var string       {review_product_name}   The name of the reviewed product.
 * @var string|null  {review_product_desc}   The description of the reviewed product.
 * @var string|null  {review_product_image}  The image URI of the reviewed product.
 * @var string       {review_title}          The title of the review left.
 * @var string|null  {review_comment}        The comment of the review left.
 * @var string       {review_rating}         The stars (images) related to the rating left.
 * @var string|null  {review_verified}       The "VERIFIED" text in case the review was left by a trusted customer.
 * @var string       {confirmation_link}	 The direct url to the details page of the order.
 * @var string|null  {user_name}             The name of the user account.
 * @var string|null  {user_username}         The username of the user account.
 * @var string|null  {user_email}            The e-mail address of the user account.
 */
