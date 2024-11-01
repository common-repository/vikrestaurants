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

$is_shifted  = !VikRestaurants::isContinuosOpeningTime();
$date_format = VREFactory::getConfig()->get('dateformat');

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewSpecialdaysList". The event method receives the
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
		<!-- {"rule":"customizer","event":"onDisplayViewSpecialdaysList","type":"search","key":"search"} -->

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

		<div class="btn-group pull-right">
			<button type="button" class="btn" onclick="vrOpenJModal('sdtest', null, true);">
				<?php echo JText::translate('VRTESTSPECIALDAYS'); ?>
			</button>
		</div>
	</div>

	<div class="btn-toolbar hidden-phone" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<div class="btn-group pull-left">
			<select name="group" id="vr-group-sel" class="<?php echo ($filters['group'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php
				$options = JHtml::fetch('vrehtml.admin.groups', [1, 2], true);

				echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['group'], true);
				?>
			</select>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewSpecialdaysList","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplaySpecialdaysTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplaySpecialdaysTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
			
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
			
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 's.id', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- NAME -->
			
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGESPDAY1', 's.name', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- WORKING SHIFTS -->

				<?php if ($is_shifted): ?>
					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="15%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGESPDAY4');?>
					</th>
				<?php endif; ?>

				<!-- DAYS FILTER -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="15%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGESPDAY5');?>
				</th>

				<!-- PUBLISHING -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('JGLOBAL_FIELDSET_PUBLISHING'); ?>
				</th>

				<!-- MENUS -->
			
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGESPDAY10');?>
				</th>

				<!-- MARK ON CALENDAR -->
			
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGESPDAY12');?>
				</th>

				<!-- PRIORITY -->
			
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="8%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGESPDAY20', 's.priority', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- GROUP -->
			
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="8%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGESPDAY16', 's.group', $this->orderDir, $this->ordering); ?>
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		$date = new JDate;

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
							<a href="index.php?option=com_vikrestaurants&amp;task=specialday.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
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

					<?php
					if ($row['start_ts'] != -1 && $row['end_ts'] != -1)
					{
						?>
						<div class="mobile-only">
							<span class="badge"><?php echo date($date_format, $row['start_ts']); ?></span>
							<span class="badge"><?php echo date($date_format, $row['end_ts']); ?></span>
						</div>
						<?php
					}
					?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- WORKING SHIFTS -->

				<?php
				if ($is_shifted)
				{
					$row['custom_shifts'] = $row['custom_shifts'] ? (array) json_decode($row['custom_shifts']) : []
					?>
					<td style="text-align: center;" class="hidden-phone">
						<?php
						// fetch working shifts
						if ($row['working_shifts'] || $row['custom_shifts'])
						{
							// merge global and custom working shifts
							$_arr = array_filter(
								array_merge(
									explode(',', $row['working_shifts']),
									$row['custom_shifts']
								)
							);

							$row['working_shifts'] = [];

							foreach ($_arr as $shift)
							{
								if (is_numeric($shift))
								{
									// recover working shift
									$shift = JHtml::fetch('vikrestaurants.timeofshift', $shift);
								}
								else
								{
									// normalize the custom shift
									$shift = JHtml::fetch('vikrestaurants.normalizeshift', $shift);
								}

								if ($shift)
								{
									// create tooltip
									$tooltip = '<i class="fas fa-stopwatch hasTooltip" title="' . $shift->fromtime . ' - ' . $shift->totime . '"></i>';

									$row['working_shifts'][] = $tooltip . ' ' . $shift->name;
								}
							}

							$row['working_shifts'] = implode(', ', $row['working_shifts']);
						}
						else
						{
							// all shifts available
							$row['working_shifts'] = JText::translate('VRMANAGEMENU24');
						}

						echo $row['working_shifts'];
						?>
					</td>
					<?php
				}
				?>

				<!-- DAYS FILTER -->

				<td style="text-align: center;" class="hidden-phone">
					<?php
					// fetch days filter
					if (strlen((string) $row['days_filter']))
					{
						$_df = explode(',', $row['days_filter']);

						$row['days_filter'] = [];

						foreach ($_df as $day)
						{
							// convert day core to string (abbr.)
							$row['days_filter'][] = $date->dayToString($day, true);
						}

						$row['days_filter'] = implode(', ', $row['days_filter']);
					}
					else
					{
						// all days available
						$row['days_filter'] = JText::translate('VRMANAGEMENU25');
					}

					echo $row['days_filter'];
					?>
				</td>

				<!-- PUBLISHING -->

				<td style="text-align: center;" class="hidden-phone">
					<?php
					echo JHtml::fetch('vrehtml.admin.stateaction', [
						'state' => true,
						'start' => $row['start_ts'],
						'end'   => $row['end_ts'],
					]);
					?>
				</td>

				<!-- MENUS -->

				<td style="text-align: center;">
					<a href="javascript: void(0);" onclick="return vrOpenMenusModal(<?php echo (int) $row['id']; ?>);">
						<i class="fas fa-search big"></i>
					</a>
				</td>

				<!-- MARK ON CALENDARs -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['markoncal'], $row['id'], 'specialday.markoncal', $canEditState); ?>
				</td>

				<!-- PRIORITY -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JText::translate('VRPRIORITY' . $row['priority']); ?>
				</td>

				<!-- GROUP -->

				<td style="text-align: center;" class="hidden-phone">
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
	<input type="hidden" name="view" value="specialdays" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<?php
// special days test
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-sdtest',
	array(
		'title'       => JText::translate('VRTESTSPECIALDAYS'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'         => 'index.php?option=com_vikrestaurants&view=specialdaystest&tmpl=component',
	)
);

// menus list
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-menuslist',
	array(
		'title'       => JText::translate('VRMANAGESPDAY10'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'         => '',
	)
);
?>

<script>
	(function($, w) {
		'use strict';

		w.clearFilters = () => {
			$('#vrkeysearch').val('');
			$('#vr-group-sel').updateChosen('');
			
			document.adminForm.submit();
		}

		w.vrOpenJModal = (id, url, jqmodal) => {
			<?php echo $vik->bootOpenModalJS(); ?>
		}

		w.vrOpenMenusModal = (id) => {
			let url = 'index.php?option=com_vikrestaurants&view=menuslist&tmpl=component&id=' + id;
			vrOpenJModal('menuslist', url, true);
			return false;
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery, window);
</script>