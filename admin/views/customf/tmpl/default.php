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

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');
$canOrder     = $this->ordering == 'f.ordering';

if ($canOrder && $canEditState)
{
	$saveOrderingUrl = 'index.php?option=com_vikrestaurants&task=customf.saveOrderAjax&tmpl=component';
	JHtml::fetch('vrehtml.scripts.sortablelist', 'customfieldsList', 'adminForm', $this->orderDir, $saveOrderingUrl, ['group' => $filters['group']]);
}

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewCustomfList". The event method receives the
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
		<!-- {"rule":"customizer","event":"onDisplayViewCustomfList","type":"search","key":"search"} -->

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
			<select name="group" id="vr-group-sel" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', JHtml::fetch('vrehtml.admin.groups'), 'value', 'text', $filters['group'], true); ?>
			</select>
		</div>
	</div>

	<div class="btn-toolbar" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		$options = [
			JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_TYPE')),
		];

		foreach ($this->types as $key => $type)
		{
			$options[] = JHtml::fetch('select.option', $key, $type);
		}
		?>
		<div class="btn-group pull-left">
			<select name="type" id="vr-type-sel" class="<?php echo (!empty($filters['type']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['type']); ?>
			</select>
		</div>

		<?php
		$options = [
			JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_RULE')),
		];

		foreach ($this->rules as $key => $rule)
		{
			$options[] = JHtml::fetch('select.option', $key, $rule);
		}
		?>
		<div class="btn-group pull-left">
			<select name="rule" id="vr-rule-sel" class="<?php echo ($filters['rule'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['rule']); ?>
			</select>
		</div>

		<?php
		if ($filters['group'] == 1)
		{
			$options = [
				JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_SERVICE')),
			];

			foreach ($this->services as $key => $service)
			{
				$options[] = JHtml::fetch('select.option', $key, $service);
			}
			?>
			<div class="btn-group pull-left">
				<select name="service" id="vr-service-sel" class="<?php echo ($filters['service'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
					<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['service']); ?>
				</select>
			</div>
			<?php
		}
		
		$options = array(
			JHtml::fetch('select.option', '', 'JOPTION_SELECT_PUBLISHED'),
			JHtml::fetch('select.option', 1, 'VRMANAGECUSTOMF3'),
			JHtml::fetch('select.option', 0, 'VRCONFIGCANCREASONOPT1'),
		);
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vr-status-sel" class="<?php echo (strlen($filters['status']) ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['status'], true); ?>
			</select>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewCustomfList","type":"search","key":"filters"} -->

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
if (count($this->rows) == 0)
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
	<!-- {"rule":"customizer","event":"onDisplayCustomfTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayCustomfTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>" id="customfieldsList">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>
				
				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ID -->
				
				<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 'f.id', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- NAME -->

				<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="20%" style="text-align: left;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGECUSTOMF1', 'f.name', $this->orderDir, $this->ordering); ?>
				</th>
				
				<!-- RULE -->

				<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="10%" style="text-align: left;">
					<?php echo JText::translate('VRMANAGECUSTOMF11'); ?>
				</th>

				<!-- SERVICE -->

				<?php if ($filters['group'] == 1): ?>
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="7%" style="text-align: left;">
						<?php echo JText::translate('VRTKORDERDELIVERYSERVICE'); ?>
					</th>
				<?php endif; ?>
				
				<!-- REQUIRED -->

				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
					<?php echo JText::translate( 'VRMANAGECUSTOMF3' ); ?>
				</th>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>
				
				<!-- LANGUAGE -->

				<?php
				if ($multi_lang && $canEdit)
				{
					?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGEMENU33');?>
					</th>
					<?php
				}
				?>

				<!-- ORDERING -->
				
				<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="1%" style="text-align: center;">
					<?php echo JHtml::fetch('vrehtml.admin.sort', '<i class="fas fa-sort"></i>', 'f.ordering', $this->orderDir, $this->ordering); ?>
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
							<a href="index.php?option=com_vikrestaurants&amp;task=customf.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
								<?php echo JText::translate($row['name']); ?>
							</a>
							<?php
						}
						else
						{
							echo JText::translate($row['name']);
						}
						?>
					</div>

					<div class="td-secondary">
						<?php
						if (isset($this->types[$row['type']]))
						{
							echo $this->types[$row['type']];
						}
						else
						{
							?>
							<span class="badge badge-warning"><?php echo $row['type']; ?></span>
							<?php
						}
						?>	
					</div>
				</td>

				<!-- RULE -->

				<td class="hidden-phone">
					<?php
					if ($row['rule'])
					{
						switch ($row['rule']) 
						{
							case 'nominative':    $clazz = 'male';           break;
							case 'email':         $clazz = 'envelope';       break;
							case 'phone':         $clazz = 'phone';          break;
							case 'address':       $clazz = 'road';           break;
							case 'zip':           $clazz = 'map-marker-alt'; break;
							case 'city':          $clazz = 'map-marker-alt'; break;
							case 'state':         $clazz = 'map-marker-alt'; break;
							case 'notes':         $clazz = 'sticky-note';    break;
							case 'deliverynotes': $clazz = 'map-signs';      break;
							default:
								$clazz = 'plug';
						}
						?>

						<span class="td-pull-left">
							<?php
							if (isset($this->rules[$row['rule']]))
							{
								echo $this->rules[$row['rule']];
							}
							else
							{
								?>
								<span class="badge badge-warning"><?php echo $row['rule']; ?></span>
								<?php
							}
							?>
						</span>

						<span class="td-pull-right">
							<i class="fas fa-<?php echo $clazz; ?> big" style="width:32px; text-align: center;"></i>
						</span>
						<?php
					}
					?>
				</td>

				<!-- SERVICE -->

				<?php if ($filters['group'] == 1): ?>
					<td class="hidden-phone">
						<?php
						if (!$row['service'])
						{
							echo JText::translate('JOPTION_ANY');
						}
						else if (isset($this->services[$row['service']]))
						{
							echo $this->services[$row['service']];
						}
						else
						{
							?>
							<span class="badge badge-warning"><?php echo $row['service']; ?></span>
							<?php
						}
						?>
					</td>
				<?php endif; ?>

				<!-- REQUIRED -->

				<td style="text-align: center;" class="hidden-phone">
					<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['required'], $row['id'], 'customf.required', $canEditState); ?>
				</td>

				<!-- CUSTOM -->

				<?php foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- LANGUAGE -->

				<?php if ($multi_lang && $canEdit): ?>
					<td style="text-align: center;">
						<a href="index.php?option=com_vikrestaurants&amp;view=langcustomf&amp;id_customf=<?php echo (int) $row['id']; ?>">
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
	<input type="hidden" name="view" value="customf" />

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
			$('#vr-type-sel').updateChosen('');
			$('#vr-rule-sel').updateChosen('');
			$('#vr-service-sel').updateChosen('');
			$('#vr-status-sel').updateChosen('');
			
			document.adminForm.submit();
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');
		});

	})(jQuery, window);
</script>