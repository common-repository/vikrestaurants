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

JHtml::fetch('behavior.modal');
JHtml::fetch('formbehavior.chosen');
JHtml::fetch('bootstrap.tooltip', '.hasTooltip');

$rows = $this->rows;

$filters = $this->filters;

$vik = VREApplication::getInstance();

$multi_lang = VikRestaurants::isMultilanguage();

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');
$canOrder     = $this->ordering == 'm.ordering';

if ($canOrder && $canEditState)
{
	$saveOrderingUrl = 'index.php?option=com_vikrestaurants&task=menu.saveOrderAjax&tmpl=component';
	JHtml::fetch('vrehtml.scripts.sortablelist', 'restaurantmenusList', 'adminForm', $this->orderDir, $saveOrderingUrl);
}

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewMenusList". The event method receives the
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
		<!-- {"rule":"customizer","event":"onDisplayViewMenusList","type":"search","key":"search"} -->

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

		<?php
		$options = [
			JHtml::fetch('select.option', '', 'JOPTION_SELECT_PUBLISHED'),
			JHtml::fetch('select.option', 1, 'JPUBLISHED'),
			JHtml::fetch('select.option', 0, 'JUNPUBLISHED'),
		];
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vr-status-sel" class="<?php echo (strlen($filters['status']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['status'], true); ?>
			</select>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewMenusList","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplayMenusTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayMenusTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>" id="restaurantmenusList">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 'm.id', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- NAME -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGEMENU1', 'm.name', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- WORKING SHIFTS -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="20%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGEMENU3'); ?>
				</th>

				<!-- DAYS FILTER -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="10%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGEMENU4'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- PUBLISHED -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEMENU26'); ?>
				</th>

				<!-- CHOOSABLE -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEMENU31'); ?>
				</th>

				<!-- SPECIAL DAY -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEMENU2'); ?>
				</th>

				<!-- LANGUAGES -->

				<?php if ($multi_lang && $canEdit): ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGEMENU33'); ?>
					</th>
				<?php endif; ?>

				<!-- PREVIEW -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEMENU14'); ?>
				</th>

				<!-- IMAGE -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGEMENU18'); ?>
				</th>

				<!-- ORDERING -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone nowrap'); ?>" width="1%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', '<i class="fas fa-sort"></i>', 'm.ordering', $this->orderDir, $this->ordering); ?>
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

			// fetch working shifts
			if ($row['special_day'])
			{
				// working shifts not available in case of special day
				$row['working_shifts'] = '/';
			}
			else if (!empty($row['working_shifts']))
			{
				$_arr = explode(',', $row['working_shifts']);

				$row['working_shifts'] = array();

				foreach ($_arr as $shift_id)
				{
					// recover working shift
					$shift = JHtml::fetch('vikrestaurants.timeofshift', $shift_id);

					// create tooltip
					$tooltip = '<i class="fas fa-stopwatch hasTooltip" title="' . $shift->fromtime . ' - ' . $shift->totime . '"></i>';

					$row['working_shifts'][] = $tooltip . ' ' . $shift->name;
				}

				$row['working_shifts'] = implode(', ', $row['working_shifts']);
			}
			else
			{
				// all shifts available
				$row['working_shifts'] = JText::translate('VRMANAGEMENU24');
			}

			$date = new JDate;
			
			// fetch days filter
			if ($row['special_day'])
			{
				// days not available in case of special day
				$row['days_filter'] = '/';
			}
			else if (strlen((string) $row['days_filter']))
			{
				$_df = explode(',', $row['days_filter']);

				$row['days_filter'] = array();

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
			?>
			<tr class="row<?php echo ($i % 2); ?>">

				<td>
					<input type="checkbox" id="cb<?php echo (int) $i; ?>" name="cid[]" value="<?php echo (int) $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>">
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
							<a href="index.php?option=com_vikrestaurants&amp;task=menu.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
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
				</td>

				<!-- WORKING SHIFTS -->

				<td class="hidden-phone">
					<?php echo $row['working_shifts']; ?>
				</td>

				<!-- DAYS FILTER -->

				<td class="hidden-phone">
					<?php echo $row['days_filter']; ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- PUBLISHED -->

				<td style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['published'], $row['id'], 'menu.publish', $canEditState); ?>
				</td>

				<!-- CHOOSABLE -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['choosable'], $row['id'], null, false); ?>
				</td>

				<!-- SPECIAL DAY -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['special_day'], $row['id'], null, false); ?>
				</td>

				<!-- LANGUAGES -->

				<?php if ($multi_lang && $canEdit): ?>
					<td style="text-align: center;">
						<a href="index.php?option=com_vikrestaurants&amp;view=langmenus&amp;id_menu=<?php echo (int) $row['id']; ?>">
							<?php
							foreach ($row['languages'] as $lang)
							{
								echo ' ' . JHtml::fetch('vrehtml.site.flag', $lang) . ' ';
							}
							?>
						</a>
					</td>
				<?php endif; ?>

				<!-- PREVIEW -->

				<td style="text-align: center;" class="hidden-phone">
					<a href="javascript: void(0);" onclick="return openMenuPreviewModal(<?php echo (int) $row['id']; ?>);">
						<i class="fas fa-search big"></i>
					</a>
				</td>

				<!-- IMAGE -->

				<td style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.imagestatus', $row['image']); ?>
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
	<input type="hidden" name="view" value="menus" />
	<input type="hidden" name="task" value="" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<?php
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-sneakmenu',
	array(
		'title'       => JText::translate('VRMANAGEMENU14'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);
?>

<script>
	(function($, w) {
		'use strict';

		const vrOpenJModal = (id, url, jqmodal) => {
			<?php echo $vik->bootOpenModalJS(); ?>
		}

		w.openMenuPreviewModal = (id) => {
			let url = 'index.php?option=com_vikrestaurants&view=sneakmenu&tmpl=component&id=' + id;
			vrOpenJModal('sneakmenu', url, true);
			return false;
		}

		w.clearFilters = () => {
			$('#vrkeysearch').val('');
			$('#vr-status-sel').updateChosen('');
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery, window);
</script>