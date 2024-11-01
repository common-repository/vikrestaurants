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

JHtml::fetch('formbehavior.chosen');
JHtml::fetch('bootstrap.tooltip', '.hasTooltip');

$rows = $this->rows;

$filters = $this->filters;

$vik = VREApplication::getInstance();

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');

$config = VREFactory::getConfig();

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewRescodesorderList". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayListView($is_searching);

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<?php if (!empty($forms['search']) || !empty($forms['filters'])): ?>
		
		<div class="btn-toolbar" style="height: 32px;">

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewRescodesorderList","type":"search","key":"search"} -->

			<?php
			// plugins can use the "search" key to introduce custom
			// filters within the search bar
			if (isset($forms['search']))
			{
				echo $forms['search'];
			}
			?>
			
			<?php
			// in case a plugin needs to use the filter bar, display the button
			if (isset($forms['filters']))
			{
				?>
				<div class="btn-group pull-left">
					<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vrToggleSearchToolsButton(this);">
						<?php echo JText::translate('JSEARCH_TOOLS'); ?>&nbsp;<i class="fas fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vr-tools-caret"></i>
					</button>
				</div>
				<?php
			}
			?>
			
			<div class="btn-group pull-left">
				<button type="button" class="btn" onclick="clearFilters();">
					<?php echo JText::translate('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>

		</div>

		<div class="btn-toolbar" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewRescodesorderList","type":"search","key":"filters"} -->

			<?php
			// plugins can use the "filters" key to introduce custom
			// filters within the search bar
			if (isset($forms['filters']))
			{
				echo $forms['filters'];
			}
			?>

		</div>

	<?php endif; ?>
	
<?php
if (count($rows) == 0)
{
	echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
}
else
{
	/**
	 * Trigger event to display custom columns.
	 *
	 * @since 1.9
	 */
	$columns = $this->onDisplayTableColumns();
	?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayRescodesorderTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayRescodesorderTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- CODE -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGERESCODE2'); ?>
				</th>
				
				<!-- ICON -->

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					&nbsp;
				</th>
				
				<!-- NOTES -->

				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="30%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGERESCODE5'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- CREATION DATE -->

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="15%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRCREATEDON', 'os.createdon', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- AUTHOR -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="15%" style="text-align: center;">
					<?php echo JText::translate('VRCREATEDBY'); ?>
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		$kk = 0;
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];	 
			?>
			<tr class="row<?php echo ($i % 2); ?>">

				<td><input type="checkbox" id="cb<?php echo (int) $i;?>" name="cid[]" value="<?php echo (int) $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>"></td>
				
				<td>
					<div class="td-primary">
						<?php
						if ($canEdit)
						{
							?>
							<a href="index.php?option=com_vikrestaurants&amp;task=rescodeorder.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
								<?php echo $row['code']; ?>
							</a>
							<?php
						}
						else
						{
							echo $row['code'];
						}
						?>
					</div>
				</td>

				<!-- ICON -->

				<td style="text-align: center;" class="vrrescodelink">
					<?php if (!empty($row['icon'])): ?>
						<img src="<?php echo VREMEDIA_SMALL_URI . $row['icon']; ?>" style="max-width: 20px;" />
					<?php endif; ?>
				</td>

				<!-- NOTES -->

				<td class="hidden-phone">
					<?php
					if (strlen($row['notes']))
					{
						echo $row['notes']; 
					}
					else
					{
						?><small><i><?php echo $row['code_notes']; ?></i></small><?php
					}
					?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- CREATION DATE -->

				<td style="text-align: center;">
					<?php
					if (VikRestaurants::now() - $row['createdon'] < 86400)
					{ 
						echo VikRestaurants::formatTimestamp($config->get('dateformat') . ' ' . $config->get('timeformat'), $row['createdon']); 
					}
					else
					{
						echo date($config->get('dateformat') . ' ' . $config->get('timeformat'), $row['createdon']);
					}
					?>
				</td>

				<!-- AUTHOR -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo $row['user_name']; ?>
				</td>

			</tr>
			<?php
		}		
		?>
	</table>
	<?php
}
?>

	<input type="hidden" name="id_order" value="<?php echo $this->escape($filters['id_order']); ?>" />
	<input type="hidden" name="group" value="<?php echo $this->escape($filters['group']); ?>" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="rescodesorder" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>

</form>
