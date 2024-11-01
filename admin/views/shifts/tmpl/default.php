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

$rows = $this->rows;

$filters = $this->filters;

$ordering = $this->ordering;

$vik = VREApplication::getInstance();

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewShiftsList". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayListView($is_searching);

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="search" id="vrkeysearch" size="32" 
				value="<?php echo $this->escape($filters['search']); ?>" placeholder="<?php echo $this->escape(JText::translate('JSEARCH_FILTER_SUBMIT')); ?>" />

			<button type="submit" class="btn">
				<i class="fas fa-search"></i>
			</button>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewShiftsList","type":"search","key":"search"} -->

		<?php
		// plugins can use the "search" key to introduce custom
		// filters within the search bar
		if (isset($forms['search']))
		{
			echo $forms['search'];
		}
		?>

		<div class="btn-group pull-left hidden-phone">
			<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vrToggleSearchToolsButton(this);">
				<?php echo JText::translate('JSEARCH_TOOLS'); ?>&nbsp;<i class="fas fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vr-tools-caret"></i>
			</button>
		</div>
		
		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::translate('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>
	</div>

	<div class="btn-toolbar hidden-phone" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<div class="btn-group pull-left">
			<select name="group" id="vr-group-sel" class="<?php echo ($filters['group'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php
				$options = JHtml::fetch('vrehtml.admin.groups', array(1, 2), true);

				echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['group'], true);
				?>
			</select>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewShiftsList","type":"search","key":"filters"} -->

		<?php
		// plugins can use the "filters" key to introduce custom
		// filters within the search bar
		if (isset($forms['filters']))
		{
			echo $forms['filters'];
		}
		?>

	</div>

<?php
if (VikRestaurants::isContinuosOpeningTime())
{
	echo $vik->alert(JText::sprintf('VRSHIFTSIGNOREDWARNING', 'index.php?option=com_vikrestaurants&amp;task=shift.switchmode'), 'warning', true);
}

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
	<!-- {"rule":"customizer","event":"onDisplayShiftsTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayShiftsTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>

				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 's.id', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- NAME -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGESHIFT1', 's.name', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- FROM -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGESHIFT2', 's.from', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- TO -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGESHIFT3'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- SHOW LABEL -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGESHIFT5'); ?>
				</th>

				<!-- GROUP -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGESHIFT4', 's.group', $this->orderDir, $this->ordering); ?>
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">

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
							<a href="index.php?option=com_vikrestaurants&amp;task=shift.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
								<?php echo $row['name']; ?>
							</a>
							<?php
						}
						else
						{
							echo $row['name'];
						}
						?>
					</div>

					<?php if (strlen($row['label']) && $row['name'] != $row['label']): ?>
						<div class="hidden-phone"><small><?php echo $row['label']; ?></small></div>
					<?php endif; ?>

					<div class="badge mobile-only">
						<?php echo JHtml::fetch('vikrestaurants.min2time', $row['from']) . ' - ' . JHtml::fetch('vikrestaurants.min2time', $row['to']); ?>
					</div>
				</td>

				<!-- FROM -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('vikrestaurants.min2time', $row['from']); ?>
				</td>

				<!-- TO -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('vikrestaurants.min2time', $row['to']); ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- SHOW LABEL -->

				<td style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['showlabel'], $row['id'], 'shift.showlabel', $canEditState); ?>
				</td>

				<!-- GROUP -->

				<td style="text-align: center;">
					<?php echo JText::translate($row['group'] == 1 ? 'VRSHIFTGROUPOPT1' : 'VRSHIFTGROUPOPT2'); ?>
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
	<input type="hidden" name="view" value="shifts" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<script>
	(function($, w) {
		'use strict';

		w.clearFilters = () => {
			jQuery('#vrkeysearch').val('');
		jQuery('#vr-group-sel').updateChosen('');
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery, window);
</script>