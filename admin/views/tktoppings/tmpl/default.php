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

$multi_lang = VikRestaurants::isMultilanguage();

$currency = VREFactory::getCurrency();

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');
$canOrder     = $this->ordering == 't.ordering';

if ($canOrder && $canEditState)
{
	$saveOrderingUrl = 'index.php?option=com_vikrestaurants&task=tktopping.saveOrderAjax&tmpl=component';
	JHtml::fetch('vrehtml.scripts.sortablelist', 'toppingsList', 'adminForm', $this->orderDir, $saveOrderingUrl);
}

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTktoppings". The event method receives the
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
		<!-- {"rule":"customizer","event":"onDisplayViewTktoppings","type":"search","key":"search"} -->

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

		<?php
		$separators = JHtml::fetch('vrehtml.admin.tktopseparators');

		if ($separators)
		{
			array_unshift($separators, JHtml::fetch('select.option', 0, JText::translate('VRE_FILTER_SELECT_SEPARATOR')));
			?>
			<div class="btn-group pull-left">
				<select name="id_separator" id="vr-separator-sel" class="<?php echo ($filters['id_separator'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
					<?php echo JHtml::fetch('select.options', $separators, 'value', 'text', $filters['id_separator']); ?>
				</select>
			</div>
			<?php
		}
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTktoppings","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplayTktoppingsTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayTktoppingsTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>" id="toppingsList">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 't.id', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- NAME -->
				
				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGETKTOPPING1', 't.name', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- PRICE -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGETKTOPPING2', 't.price', $this->orderDir, $this->ordering); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- STATUS -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGETKTOPPING3'); ?>
				</th>

				<!-- SEPARATOR -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGETKTOPPING5'); ?>
				</th>

				<!-- LANGUAGE -->
				
				<?php if ($multi_lang && $canEdit): ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGEMENU33');?>
					</th>
				<?php endif; ?>

				<!-- ORDERING -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="1%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', '<i class="fas fa-sort"></i>', 't.ordering', $this->orderDir, $this->ordering); ?>
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
							<a href="index.php?option=com_vikrestaurants&amp;task=tktopping.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
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

				<!-- PRICE -->
				
				<td style="text-align: center;" class="hidden-phone">
					<?php echo $currency->format($row['price']); ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- STATUS -->
				
				<td style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['published'], $row['id'], 'tktopping.publish', $canEditState); ?>
				</td>

				<!-- SEPARATOR -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo $row['separator'] ?: '/'; ?>
				</td>

				<!-- LANGUAGE -->

				<?php if ($multi_lang): ?>
					<td style="text-align: center;">
						<a href="index.php?option=com_vikrestaurants&amp;view=langtktoppings&amp;id_topping=<?php echo (int) $row['id']; ?>">
							<?php
							foreach ($row['languages'] as $lang)
							{
								echo ' ' . JHtml::fetch('vrehtml.site.flag', $lang) . ' ';
							}
							?>
						</a>
					</td>
				<?php endif; ?>

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
	<input type="hidden" name="view" value="tktoppings" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<script>
	(function($, w) {
		'use strict';

		w.clearFilters = () => {
			$('#vrkeysearch').val('');
			$('#vr-status-sel').updateChosen('');
			$('#vr-separator-sel').updateChosen(0);
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery, window);
</script>