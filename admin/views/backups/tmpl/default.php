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
JHtml::fetch('vrehtml.assets.fontawesome');

$rows = $this->rows;

$filters = $this->filters;

$vik = VREApplication::getInstance();

$is_searching = $this->hasFilters();

$date_time_format = JText::translate('DATE_FORMAT_LC3') . ' ' .	preg_replace("/:i/", ':i:s', VREFactory::getConfig()->get('timeformat'));

$canEdit = JFactory::getUser()->authorise('core.admin', 'com_vikrestaurants');

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewBackupsList". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayListView($is_searching);
?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left">
			<?php echo $vik->calendar($filters['date'], 'date', 'vre-date-filter', null, ['onChange' => 'document.adminForm.submit()']); ?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewBackupsList","type":"search","key":"search"} -->

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

		<?php
		$options = [];
		$options[] = JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_TYPE'));

		foreach ($this->exportTypes as $id => $handler)
		{
			$options[] = JHtml::fetch('select.option', $id, $handler->getName());
		}
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vr-type-sel" class="<?php echo (!empty($filters['type']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewBackupsList","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplayBackupsTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayBackupsTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		<?php echo $vik->openTableHead(); ?>
			<tr>

				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- DATE -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGEMEDIA3', 'createdon', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- TYPE -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;">
					<?php echo JText::translate('VRE_BACKUP_CONFIG_TYPE_LABEL'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- SIZE -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone nowrap'); ?>" width="8%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGEMEDIA13', 'filesize', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- RESTORE -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone nowrap'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMAPGPRESTOREBUTTON'); ?>
				</th>

				<!-- DOWNLOAD -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone nowrap'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRDOWNLOAD'); ?>
				</th>
			
			</tr>
		<?php echo $vik->closeTableHead(); ?>
		
		<?php
		for ($i = 0, $n = count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			?>
			<tr class="row<?php echo ($i % 2); ?>">

				<td>
					<input type="checkbox" id="cb<?php echo (int) $i;?>" name="cid[]" value="<?php echo $this->escape($row->name); ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>">
				</td>

				<!-- NAME -->

				<td>
					<?php echo JHtml::fetch('date', $row->date, $date_time_format); ?>
				</td>

				<!-- TYPE -->

				<td>
					<?php echo $row->type->name; ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- SIZE -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('number.bytes', $row->size); ?>
				</td>

				<!-- RESTORE -->
				
				<td style="text-align: center;" class="hidden-phone">
					<?php if ($canEdit): ?>
						<a href="<?php echo VREFactory::getPlatform()->getUri()->addCSRF('index.php?option=com_vikrestaurants&task=backup.restore&cid[]=' . $this->escape($row->name), $xhtml = true); ?>" class="backup-restore-link">
							<i class="fas fa-history big"></i>
						</a>
					<?php else: ?>
						<a href="javascript:void(0)" class="disabled">
							<i class="fas fa-history big"></i>
						</a>
					<?php endif; ?>
				</td>

				<!-- DOWNLOAD -->
				
				<td style="text-align: center;" class="hidden-phone">
					<?php if ($canEdit): ?>
						<a href="<?php echo $row->url; ?>">
							<i class="fas fa-download big"></i>
						</a>
					<?php else: ?>
						<a href="javascript:void(0)" class="disabled">
							<i class="fas fa-download big"></i>
						</a>
					<?php endif; ?>
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
	<input type="hidden" name="view" value="backups" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<?php
// load create modal content
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-newbackup',
	array(
		'title'       => JText::translate('VREMAINTITLENEWBACKUP'),
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'width'       => 60,
		'footer'      => '<button type="button" class="btn btn-success" data-role="backup.save">' . JText::translate('VRSAVE') . '</button>',
	),
	$this->loadTemplate('modal')
);

JText::script('VRE_BACKUP_RESTORECONF1');
JText::script('VRE_BACKUP_RESTORECONF2');
?>

<script>
	(function($, w) {
		'use strict';

		const openModal = (id, url, jqmodal) => {
			<?php echo $vik->bootOpenModalJS(); ?>
		}

		w.vrCloseJModal = (id) => {
			<?php echo $vik->bootDismissModalJS(); ?>
		}

		w.clearFilters = () => {
			$('#vr-date-filter').val('');
			$('#vr-type-sel').updateChosen('');
			
			document.adminForm.submit();
		}

		Joomla.submitbutton = (task) => {
			if (task === 'backup.add') {
				openModal('newbackup', null, true);
			} else {
				Joomla.submitform(task, document.adminForm);
			}
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');

			$('a.backup-restore-link').on('click', (event) => {
				let r = confirm(Joomla.JText._('VRE_BACKUP_RESTORECONF1'));

				if (!r) {
					return false;
				}

				r = confirm(Joomla.JText._('VRE_BACKUP_RESTORECONF2'));

				if (!r) {
					return false
				}

				return true;
			});
		});
	})(jQuery, window);
</script>