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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('formbehavior.chosen');

$rows = $this->rows;

$filters = $this->filters;

$vik = VREApplication::getInstance();

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');

$currency = VREFactory::getCurrency();

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewCouponsList". The event method receives the
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
		<!-- {"rule":"customizer","event":"onDisplayViewCouponsList","type":"search","key":"search"} -->

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
			<select name="group" id="vr-group-sel" class="<?php echo (strlen($filters['group']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php
				$options = JHtml::fetch('vrehtml.admin.groups', null, true);

				echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['group'], true);
				?>
			</select>
		</div>

		<div class="btn-group pull-left">
			<select name="type" id="vr-type-sel" class="<?php echo ($filters['type'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php
				$options = array(
					JHtml::fetch('select.option', 0, 'VRE_FILTER_SELECT_TYPE'),
					JHtml::fetch('select.option', 1, 'VRCOUPONTYPEOPTION1'),
					JHtml::fetch('select.option', 2, 'VRCOUPONTYPEOPTION2'),
				);

				echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['type'], true);
				?>
			</select>
		</div>

		<?php
		$options = array(
			JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_CATEGORY')),
			JHtml::fetch('select.option',  0,     JText::translate('VRE_FILTER_NO_CATEGORY')),
		);

		$options = array_merge($options, JHtml::fetch('vrehtml.admin.coupongroups'));
		?>
		<div class="btn-group pull-left">
			<select name="id_category" id="vr-category-sel" class="<?php echo (strlen($filters['id_category']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['id_category']); ?>
			</select>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewCouponsList","type":"search","key":"filters"} -->

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
	<!-- {"rule":"customizer","event":"onDisplayCouponsTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayCouponsTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>

				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 'c.id', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- CODE -->

				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGECOUPON1', 'c.code', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- TYPE -->

				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="8%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGECOUPON2'); ?>
				</th>
				
				<!-- VALUE -->

				<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGECOUPON4'); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- USAGES -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRE_USAGES_FIELDSET', 'c.usages', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- PUBLISHING -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate('JGLOBAL_FIELDSET_PUBLISHING'); ?>
				</th>
				
				<!-- GROUP -->
				
				<th class="<?php echo $vik->getAdminThClass(); ?>" width="10%" style="text-align: center;">
					<?php echo JText::translate('VRMANAGECOUPON10'); ?>
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
					<div class="td-primary td-pull-left">
						<?php
						if ($canEdit)
						{
							?>
							<a href="index.php?option=com_vikrestaurants&amp;task=coupon.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
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

					<?php if ($row['notes']): ?>
						<div class="td-pull-right">
							<a href="javascript:void(0)" class="hasTooltip" title="<?php echo $this->escape($row['notes']); ?>">
								<i class="fas fa-sticky-note big"></i>
							</a>
						</div>
					<?php endif; ?>
				</td>

				<!-- TYPE -->

				<td class="hidden-phone">
					<?php echo JText::translate('VRCOUPONTYPEOPTION' . $row['type']); ?>
				</td>

				<!-- AMOUNT -->

				<td style="text-align: center;">
					<?php
					if ((float) $row['value'])
					{
						if ($row['percentot'] == 1)
						{
							echo $currency->format($row['value'], [
								'symbol'     => '%',
								'position'   => 1,
								'space'      => false,
								'no_decimal' => true,
							]);
						}
						else
						{
							echo $currency->format($row['value']);
						}
					}
					else
					{
						echo '/';
					}
					?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- USAGES -->

				<td style="text-align: center;" class="hidden-phone">
					<span class="badge badge-<?php echo $row['type'] == 1 || $row['usages'] < $row['maxusages'] ? 'success' : 'important'; ?>">
						<?php
						echo $row['usages'];

						if ($row['type'] == 2)
						{
							echo '/' . $row['maxusages'];
						}
						?>
					</span>
				</td>

				<!-- PUBLISHING -->

				<td style="text-align: center;" class="hidden-phone">
					<?php
					echo JHtml::fetch('vrehtml.admin.stateaction', [
						'state' => $row['type'] == 1 || $row['usages'] < $row['maxusages'],
						'start' => $row['start_publishing'],
						'end'   => $row['end_publishing'],
					]);
					?>
				</td>

				<!-- GROUP -->

				<td style="text-align: center;">
					<?php echo JText::translate($row['group'] == 0 ? 'VRMANAGECONFIGTITLE1' : 'VRMANAGECONFIGTITLE2'); ?>
				</td>

			</tr>
			<?php
		}		
		?>
	</table>
	<?php
}
?>

	<input type="hidden" name="datasheet_type" value="coupons" />
	<input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_vikrestaurants&view=coupons'); ?>" />
	
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="coupons" />

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
			$('#vr-group-sel').updateChosen('');
			$('#vr-type-sel').updateChosen(0);
			$('#vr-category-sel').updateChosen('');
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');

			Joomla.submitbutton = (task) => {
				if (task == 'export') {
					// populate view instead of task
					document.adminForm.view.value = task;
					task = '';
				}
				
				Joomla.submitform(task, document.adminForm);
			}
		});

	})(jQuery);
</script>