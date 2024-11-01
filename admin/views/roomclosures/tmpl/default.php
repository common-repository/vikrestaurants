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

$vik = VREApplication::getInstance();

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');

$now = VikRestaurants::now();

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewRoomclosuresList". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayListView($is_searching);

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">
	
	<div class="btn-toolbar" style="height:32px;">

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewRoomclosuresList","type":"search","key":"search"} -->

		<?php
		// plugins can use the "search" key to introduce custom
		// filters within the search bar
		if (isset($forms['search']))
		{
			echo $forms['search'];
		}
		?>

		<div class="btn-group pull-left">
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

	<div class="btn-toolbar" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<div class="btn-group pull-left">
			<?php
			$options = array(
				JHtml::fetch('select.option', 0, JText::translate('VRMAPSCHOOSEROOM')),
			);

			$options = array_merge($options, JHtml::fetch('vikrestaurants.rooms'));
			?>
			<select name="id_room" id="vr-rooms-sel" class="<?php echo ($filters['id_room'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['id_room']); ?>
			</select>
		</div>

		<div class="btn-group pull-left vr-toolbar-setfont">
			<?php
			$attr = array(
				'onChange' => 'document.adminForm.submit();',
			);

			echo $vik->calendar($filters['date'], 'date', 'vr-date-filter', null, $attr);
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewRoomclosuresList","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplayRoomclosuresTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayRoomclosuresTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>

				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 'c.id', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- ROOM -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGEROOMCLOSURE1', 'r.ordering', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- START DATE -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGEROOMCLOSURE2', 'c.start_ts', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- END DATE -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGEROOMCLOSURE3', 'c.end_ts', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- DURATION -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="15%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEROOMCLOSURE4'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>
				
				<!-- STATUS -->

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEROOMCLOSURE5'); ?>
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			
			if ($row['start_ts'] <= $now && $now < $row['end_ts'])
			{
				$status = 'confirmed';
			}
			else if ($row['end_ts'] <= $now)
			{
				$status = 'removed';
			}
			else
			{
				$status = 'pending';
			}
			?>
			<tr class="row<?php echo ($i % 2); ?>">

				<td>
					<input type="checkbox" id="cb<?php echo (int) $i;?>" name="cid[]" value="<?php echo (int) $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>">
				</td>

				<!-- ID -->

				<td class="hidden-phone">
					<?php echo $row['id']; ?>
				</td>

				<!-- ROOM -->

				<td>
					<div class="td-primary">
						<?php
						if ($canEdit)
						{
							?>
							<a href="index.php?option=com_vikrestaurants&amp;task=roomclosure.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
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

					<div class="td-secondary mobile-only">
						<span class="badge badge-important">
							<?php echo JHtml::fetch('date', $row['start_ts'], JText::translate('DATE_FORMAT_LC2'), date_default_timezone_get()); ?>
						</span>
						<span class="badge badge-important">
							<?php echo JHtml::fetch('date', $row['end_ts'], JText::translate('DATE_FORMAT_LC2'), date_default_timezone_get()); ?>
						</span>
					</div>
				</td>

				<!-- START -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('date', $row['start_ts'], JText::translate('DATE_FORMAT_LC2'), date_default_timezone_get()); ?>
				</td>

				<!-- END -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('date', $row['end_ts'], JText::translate('DATE_FORMAT_LC2'), date_default_timezone_get()); ?>
				</td>

				<!-- DURATION -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo VikRestaurants::minutesToStr(($row['end_ts'] - $row['start_ts']) / 60); ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- STATUS -->

				<td style="text-align: center;">
					<span class="vrreservationstatus<?php echo $status; ?>">
						<?php echo JText::translate('VRROOMCLOSURESTATUS' . strtoupper($status)); ?>
					</span>
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
	<input type="hidden" name="view" value="roomclosures" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<script>

	(function($) {
		'use strict';

		window['clearFilters'] = () => {
			$('#vr-rooms-sel').updateChosen(0);
			$('#vr-date-filter').val('');
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery);

</script>
