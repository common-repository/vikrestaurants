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

$order     = isset($displayData['order'])     ? $displayData['order']     : [];
$breakdown = isset($displayData['breakdown']) ? $displayData['breakdown'] : [];
$usetaxbd  = isset($displayData['usetaxbd'])  ? $displayData['usetaxbd']  : false;

$rowSpan = 2;

if ($order->payment_charge > 0)
{
	$rowSpan++;
}

if ($order->discount_val > 0)
{
	$rowSpan++;
}

if ($order->tip_amount > 0)
{
	$rowSpan++;
}

if ($usetaxbd)
{
	// increase row size by the number of fetched tax breakdowns
	$rowSpan += count($breakdown);
}
else if ($order->total_tax > 0)
{
	// display the total taxes instead
	$rowSpan++;
}

$currency = VREFactory::getCurrency();

?>

<table width="100%"  border="0">
	
	<tr>
		<td>
			<table width="100%"  border="0" cellspacing="5" cellpadding="5">
				<tr>
					<td width="70%">{company_logo}<br/><br />{company_info}</td>

					<td width="30%"align="right" valign="bottom">
						<table width="100%" border="0" cellpadding="1" cellspacing="1">
							<tr>
								<td align="right" bgcolor="#FFFFFF"><strong><?php echo JText::translate('VRINVNUM'); ?> {invoice_number}{invoice_suffix}</strong></td>
							</tr>

							<tr>
								<td align="right" bgcolor="#FFFFFF"><strong><?php echo JText::translate('VRINVDATE'); ?> {invoice_date}</strong></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table width="100%"  border="0" cellspacing="1" cellpadding="2">
				<tr bgcolor="#E1E1E1" style="background-color: #E1E1E1;">
					<td width="64%"><strong><?php echo JText::translate('VRINVITEMDESC'); ?></strong></td>
					<td width="12%" align="right"><strong><?php echo JText::translate('VRINVTOTAL'); ?></strong></td>
					<td width="12%" align="right"><strong><?php echo JText::translate('VRINVTAXES'); ?></strong></td>
					<td width="12%" align="right"><strong><?php echo JText::translate('VRINVITEMPRICE'); ?></strong></td>
				</tr>
				
				<?php foreach ($order->items as $item): ?>	
					<tr>
						<td width="64%"><?php echo $item->quantity . 'x ' . $item->name; ?></td>
						<td width="12%" align="right"><?php echo $currency->format($item->net); ?></td>
						<td width="12%" align="right"><?php echo $currency->format($item->tax); ?></td>
						<td width="12%" align="right"><?php echo $currency->format($item->gross); ?></td>
					</tr>
				<?php endforeach; ?>

				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table width="100%" border="0" cellspacing="1" cellpadding="2">
				<tr bgcolor="#E1E1E1">
					<td width="70%" colspan="2" rowspan="<?php echo (int) $rowSpan; ?>" valign="top">
						<strong><?php echo JText::translate('VRINVCUSTINFO'); ?></strong><br/>{customer_info}<br/>{billing_info}
					</td>

					<td width="30%" align="left">
						<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
							<td align="left"><strong><?php echo JText::translate('VRINVTOTAL'); ?></strong></td>
							<td align="right">{invoice_totalnet}</td>
						</tr></table>
					</td>
				</tr>

				<?php if ($order->discount_val > 0): ?>
					<tr bgcolor="#E1E1E1" color="#900">
						<td width="30%" align="left">
							<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
								<td align="left"><strong><?php echo JText::translate('VRINVDISCOUNTVAL'); ?></strong></td>
								<td align="right">{invoice_discountval}</td>
							</tr></table>
						</td>
					</tr>
				<?php endif; ?>

				<?php if ($order->tip_amount > 0): ?>
					<tr bgcolor="#E1E1E1">
						<td width="30%" align="left">
							<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
								<td align="left"><strong><?php echo JText::translate('VRINVTIP'); ?></strong></td>
								<td align="right">{invoice_totaltip}</td>
							</tr></table>
						</td>
					</tr>
				<?php endif; ?>

				<?php if ($order->payment_charge > 0): ?>
					<tr bgcolor="#E1E1E1">
						<td width="30%" align="left">
							<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
								<td align="left"><strong><?php echo JText::sprintf('VRTKCARTTOTALPAYCHARGE', $order->payment->name); ?></strong></td>
								<td align="right">{invoice_paycharge}</td>
							</tr></table>
						</td>
					</tr>
				<?php endif; ?>

				<?php if ($usetaxbd): ?>
					<?php foreach ($breakdown as $name => $tax): ?>
						<tr bgcolor="#E1E1E1">
							<td width="30%" align="left">
								<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
									<td align="left"><strong><?php echo $name ?></strong></td>
									<td align="right"><?php echo $currency->format($tax); ?></td>
								</tr></table>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php elseif ($order->total_tax > 0): ?>
					<tr bgcolor="#E1E1E1">
						<td width="30%" align="left">
							<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
								<td align="left"><strong><?php echo JText::translate('VRINVTAXES'); ?></strong></td>
								<td align="right">{invoice_totaltax}</td>
							</tr></table>
						</td>
					</tr>
				<?php endif; ?>

				<tr bgcolor="#E1E1E1">
					<td width="30%" align="left" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
							<td align="left"><strong><?php echo JText::translate('VRINVGRANDTOTAL'); ?></strong></td>
							<td align="right">{invoice_grandtotal}</td>
						</tr></table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>