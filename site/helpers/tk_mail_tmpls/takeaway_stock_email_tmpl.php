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
 * VikRestaurants - Take-Away Stock E-Mail Template
 *
 * @var object  $items  It is possible to use this variable to 
 * 						iterate all the items with low stocks.
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
			
			<!-- TOP BOX [logo and stocks content] -->

			<tr>
				<td style="padding: 0 25px; text-align: center;">
					<div style="display: inline-block; width: 200px; margin-bottom: 20px;" class="heading-logo">{logo}</div>
					<div style="margin: 10px auto; line-height: 1.4em; font-size: 14px;" class="heading-description">{stocks_content}</div>
				</td>
			</tr>

			<!-- CUSTOM POSITION TOP -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-top">
					{custom_position_top}
				</td>
			</tr>

			<!-- CUSTOM POSITION MIDDLE -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-middle">
					{custom_position_middle}
				</td>
			</tr>

			<!-- PRODUCTS LIST -->

			<tr>
				<td style="padding: 0; text-align: left;" class="menus-list">
					<?php foreach ($items as $menu): ?>
						<table width="100%" style="border-collapse: separate; border-spacing: 0; padding: 10px; font-size: 14px; border-top: 2px solid #ddd;" class="menu-items">
							<tr>
								<td style="font-size: 16px; font-weight: bold; padding-bottom: 10px;" class="menu-item">
									<?php echo $menu->title; ?>
								</td>
							</tr>

							<?php foreach ($menu->list as $item): ?>
								<tr>
									<td style="padding: 5px 20px;" class="item-details">
										<span style="float: left;" class="item-name">
											<?php echo $item->name; ?>
										</span>

										<span style="float: right; font-weight: bold; font-size: smaller; text-transform: uppercase; color: #900;" class="item-units">
											<?php echo JText::sprintf('VRTKADMINLOWSTOCKREMAINING', $item->remaining); ?>
										</span>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endforeach; ?>
				</td>
			</tr>

			<!-- CUSTOM POSITION BOTTOM -->

			<tr>
				<td style="padding: 0 25px;" class="custom-position-bottom">
					{custom_position_bottom}
				</td>
			</tr>

			<!-- STOCKS HELP -->

			<tr>
				<td style="padding: 30px 25px 5px; text-align: center; border-top: 2px solid #ddd; line-height: 20px; font-size: 13px;" class="stocks-help">
					{stocks_help}
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
 * @var string|null  {logo}            The logo image of your company.
 * @var string|null  {company_name}    The name of the company.
 * @var string       {stocks_content}  The content specified in the language file at VRTKADMINLOWSTOCKCONTENT.
 * @var string       {stocks_help}     The help text specified in the language file at VRTKADMINLOWSTOCKHELP.
 */
