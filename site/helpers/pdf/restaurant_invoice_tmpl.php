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

$total_rows = 5;

if ($order_details['discount_val'] <= 0)
{
	$total_rows--;
}

if ($order_details['tip_amount'] <= 0)
{
	$total_rows--;
}

?>

<table width="100%"  border="0" cellspacing="5" cellpadding="5">
	<tr>
		<td width="65%">{company_logo}</td>
		<td width="35%"align="right" valign="bottom">
			<table width="100%" border="0" cellpadding="1" cellspacing="1">
				<tr>
					<td align="right" bgcolor="#FFFFFF"><strong><?php echo JText::translate('VRINVNUM'); ?> {invoice_number}{invoice_suffix}</strong></td>
				</tr>
				<tr>
					<td align="right" bgcolor="#FFFFFF"><strong><?php echo JText::translate('VRINVDATE'); ?> {invoice_date}</strong></td>
				</tr>
				<tr>
					<td align="right" bgcolor="#FFFFFF"><strong>{invoice_order_number}</strong></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="2">
	<tr bgcolor="#E1E1E1" style="background-color: #E1E1E1;">
		<td width="65%"><strong><?php echo JText::translate('VRINVITEMDESC'); ?></strong></td>
		<td width="15%"><strong><?php echo JText::translate('VRINVITEMQUANTITY'); ?></strong></td>
		<td width="35%"><strong><?php echo JText::translate('VRINVITEMPRICE'); ?></strong></td>
	</tr>
	{invoice_order_details}
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="2">
	<tr bgcolor="#E1E1E1">
		<td width="70%" colspan="2" rowspan="<?php echo $total_rows; ?>" valign="top">
			<strong><?php echo JText::translate('VRINVCUSTINFO'); ?></strong><br/>{customer_info}<br/>{billing_info}
		</td>
		<td width="30%" align="left">
			<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td align="left"><strong><?php echo JText::translate('VRINVTOTAL'); ?></strong></td>
				<td align="right">{invoice_totalnet}</td>
			</tr></table>
		</td>
	</tr>
	<tr bgcolor="#E1E1E1">
		<td width="30%" align="left">
			<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td align="left"><strong><?php echo JText::translate('VRINVTAXES'); ?></strong></td>
				<td align="right">{invoice_totaltax}</td>
			</tr></table>
		</td>
	</tr>
	<?php
	if ($order_details['tip_amount'] > 0)
	{
		?>
		<tr bgcolor="#E1E1E1">
			<td width="30%" align="left">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
					<td align="left"><strong><?php echo JText::translate('VRINVTIP'); ?></strong></td>
					<td align="right">{invoice_totaltip}</td>
				</tr></table>
			</td>
		</tr>
		<?php
	}
	?>
	<?php
	if ($order_details['discount_val'] > 0)
	{
		?>
		<tr bgcolor="#E1E1E1" color="#900">
			<td width="30%" align="left">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
					<td align="left"><strong><?php echo JText::translate('VRINVDISCOUNTVAL'); ?></strong></td>
					<td align="right">{invoice_discountval}</td>
				</tr></table>
			</td>
		</tr>
		<?php
	}
	?>
	<tr bgcolor="#E1E1E1">
		<td width="30%" align="left" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td align="left"><strong><?php echo JText::translate('VRINVGRANDTOTAL'); ?></strong></td>
				<td align="right">{invoice_grandtotal}</td>
			</tr></table>
		</td>
	</tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="2">
	<tr><td>&nbsp;</td></tr>
	<tr><td align="center">{company_info}</td></tr>
</table>