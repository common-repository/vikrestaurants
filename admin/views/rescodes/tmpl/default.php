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

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');
$canOrder     = $this->ordering == 'r.ordering';

if ($canOrder && $canEditState)
{
	$saveOrderingUrl = 'index.php?option=com_vikrestaurants&task=rescode.saveOrderAjax&tmpl=component';
	JHtml::fetch('vrehtml.scripts.sortablelist', 'rescodesList', 'adminForm', $this->orderDir, $saveOrderingUrl, ['type' => $filters['type']]);
}

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewRescodesList". The event method receives the
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
		<!-- {"rule":"customizer","event":"onDisplayViewRescodesList","type":"search","key":"search"} -->

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

		<div class="btn-group pull-right">
			<select name="type" id="vr-type-sel" onchange="document.adminForm.submit();">
				<?php
				$options = JHtml::fetch('vrehtml.admin.groups', [1, 2]);

				/**
				 * Added support for "Food" group.
				 *
				 * @since 1.8
				 */
				$options[] = JHtml::fetch('select.option', 3, 'VRCONFIGFIELDSETFOOD');

				echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['type'], true);
				?>
			</select>
		</div>

	</div>

	<div class="btn-toolbar" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewRescodesList","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplayRescodesTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayRescodesTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>" id="rescodesList">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 'r.id', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- CODE -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGERESCODE2', 'r.code', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- NOTES -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="40%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGERESCODE5'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- RULE -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="10%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGECUSTOMF11'); ?>
				</th>

				<!-- ICON -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGERESCODE3'); ?>
				</th>

				<!-- ORDERING -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone nowrap'); ?>" width="1%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', '<i class="fas fa-sort"></i>', 'r.ordering', $this->orderDir, $this->ordering); ?>
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

				<!-- CODE -->
				
				<td>
					<div class="td-primary">
						<?php
						if ($canEdit)
						{
							?>
							<a href="index.php?option=com_vikrestaurants&amp;task=rescode.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
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

				<!-- NOTES -->

				<td class="hidden-phone">
					<?php echo $row['notes']; ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- RULE -->

				<td class="hidden-phone">
					<?php
					if ($row['rule'])
					{
						try
						{
							/** @var E4J\VikRestaurants\ReservationCodes\CodeRule */
							$rule = E4J\VikRestaurants\ReservationCodes\CodesHandler::getRule($row['rule']);
							?>
							<span class="hasTooltip" title="<?php echo $this->escape($rule->getDescription()); ?>">
								<?php echo $rule->getName(); ?>
							</span>
							<?php
						}
						catch (Exception $e)
						{
							// rule not found
							?>
							<span class="hasTooltip" title="<?php echo $this->escape($e->getMessage()); ?>">
								<em><?php echo $row['rule']; ?></em>
								<i class="fas fa-exclamation-circle no"></i>
							</span>
							<?php
						}
					}
					?>
				</td>

				<!-- ICON -->

				<td style="text-align: center;" class="vrrescodelink">
					<?php
					if (!empty($row['icon']))
					{
						?>
						<img src="<?php echo VREMEDIA_URI . $row['icon']; ?>" />
						<?php
					}
					?>
				</td>

				<!-- ORDERING -->
				
				<td class="order nowrap center hidden-phone">
					<?php echo JHtml::fetch('vrehtml.admin.sorthandle', $row['ordering'], $canEditState, $canOrder); ?>
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
	<input type="hidden" name="view" value="rescodes" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />
	
	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<script>
	(function($) {
		'use strict';

		window['clearFilters'] = () => {
			$('#vrkeysearch').val('');
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery);
</script>