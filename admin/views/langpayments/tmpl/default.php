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

$rows = $this->rows;

$filters = $this->filters;

$vik = VREApplication::getInstance();

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

<?php
if (count($rows) == 0)
{
	echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
}
else
{
	?>
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGELANG1');?>						
				</th>

				<!-- NAME -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGELANG2');?>						
				</th>

				<!-- NOTES BEFORE PURCHASE -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="30%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGEPAYMENT11');?>						
				</th>

				<!-- NOTES AFTER PURCHASE -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="30%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGEPAYMENT7');?>						
				</th>

				<!-- LANGUAGE -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGELANG4');?>						
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

			$name = $row['name'] ? $row['name'] : JText::translate('JOPTION_USE_DEFAULT');
			
			if ($row['note'])
			{
				$note = strip_tags($row['note']);

				if (strlen($note) > 300)
				{
					$note = mb_substr($note, 0, 256, 'UTF-8') . '...';
				}
			}
			else
			{
				$note = JText::translate('JOPTION_USE_DEFAULT');
			}

			if ($row['prenote'])
			{
				$prenote = strip_tags($row['prenote']);

				if (strlen($prenote) > 300)
				{
					$prenote = mb_substr($prenote, 0, 256, 'UTF-8') . '...';
				}
			}
			else
			{
				$prenote = JText::translate('JOPTION_USE_DEFAULT');
			}
			?>
			<tr class="row<?php echo $i % 2; ?>">

				<td>
					<input type="checkbox" id="cb<?php echo (int) $i;?>" name="cid[]" value="<?php echo (int) $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>">
				</td>

				<!-- ID -->

				<td class="hidden-phone">
					<?php echo $row['id']; ?>
				</td>

				<!-- NAME -->

				<td>
					<div class="td-primary">
						<?php
						if ($canEdit)
						{
							?>
							<a href="index.php?option=com_vikrestaurants&amp;task=langpayment.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
								<?php echo $name; ?>
							</a>
							<?php
						}
						else
						{
							echo $name;
						}
						?>
					</div>
				</td>

				<!-- NOTES BEFORE PURCHASE -->

				<td class="hidden-phone">
					<?php echo $prenote; ?>
				</td>

				<!-- NOTES AFTER PURCHASE -->

				<td class="hidden-phone">
					<?php echo $note; ?>
				</td>

				<!-- LANGUAGE -->

				<td style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.site.flag', $row['tag']); ?>
				</td>

			</tr>
			<?php
		}		
		?>
	</table>
	<?php
}
?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="langpayments" />
	<input type="hidden" name="id_payment" value="<?php echo (int) $filters['id_payment']; ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>
