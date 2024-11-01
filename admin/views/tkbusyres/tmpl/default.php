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
JHtml::fetch('vrehtml.assets.fontawesome');

$rows = $this->rows;

$currency = VREFactory::getCurrency();
$config   = VREFactory::getConfig();

$vik = VREApplication::getInstance();

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<div class="modal-content-padding" style="padding: 10px;">
	
		<div class="btn-toolbar vr-btn-toolbar" style="height:32px;" id="busy-filter-bar">
			
			<?php
			$options = [
				JHtml::fetch('select.option',  15, VikRestaurants::minutesToStr(15)),
				JHtml::fetch('select.option',  30, VikRestaurants::minutesToStr(30)),
				JHtml::fetch('select.option',  60, VikRestaurants::minutesToStr(60)),
				JHtml::fetch('select.option',  90, VikRestaurants::minutesToStr(90)),
				JHtml::fetch('select.option', 120, VikRestaurants::minutesToStr(120)),
			];
			?>
			<div class="btn-group pull-right">
				<select name="interval" id="vr-interval-select" class="active" onchange="document.adminForm.submit();">
					<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $this->filters['interval']); ?>
				</select>   
			</div>
			
		</div>
		
	<?php
	if (count($rows) == 0)
	{
		echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
	}
	else
	{
		?>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
			
			<?php echo $vik->openTableHead(); ?>
				<tr>

					<!-- ORDER NUMBER -->
					
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone nowrap'); ?>" width="1%" style="text-align: left;"><?php echo JText::translate('JGRID_HEADING_ID'); ?></th>
					
					<!-- CHECK-IN -->

					<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="10%" style="text-align: left;"><?php echo JText::translate('VRMANAGETKRES3'); ?></th>
					
					<!-- SERVICE -->

					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="15%" style="text-align: left;"><?php echo JText::translate('VRMANAGETKRES13'); ?></th>
					
					<!-- CUSTOMER -->

					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="15%" style="text-align: left;"><?php echo JText::translate('VRMANAGETKRES24'); ?></th>
					
					<!-- ITEMS -->

					<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;"><?php echo JText::translate('VRMANAGETKRES22'); ?></th>
					
					<!-- TOTAL -->

					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="5%" style="text-align: left;"><?php echo JText::translate('VRMANAGETKRES8'); ?></th>
					
					<!-- RESERVATION CODE -->

					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">&nbsp;</th>
					
					<!-- STATUS -->

					<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="10%" style="text-align: left;"><?php echo JText::translate('VRMANAGETKRES9'); ?></th>
				
				</tr>
			<?php echo $vik->closeTableHead(); ?>

			<?php
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];

				$route_obj = $row['route'] ? json_decode($row['route']) : null;
				?>
				<tr class="row<?php echo ($i % 2); ?>">

					<!-- ORDER NUMBER -->

					<td class="hidden-phone">
						<div class="td-primary"><?php echo $row['id']; ?></div>
					</td>

					<!-- CHECK-IN -->

					<td>
						<div class="td-primary">
							<?php
							echo JHtml::fetch('date', date('Y-m-d H:i:s', $row['checkin_ts']), JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'), date_default_timezone_get());

							if ($row['preparation_ts'])
							{
								// subtract a time slot from the preparation time
								$row['preparation_ts'] = strtotime('-' . $config->get('tkminint') . ' minutes', $row['preparation_ts']);
								// fetch preparation time hint
								$prepTip = JText::sprintf('VRE_TKRES_PREP_TIME_HINT', date($config->get('timeformat'), $row['preparation_ts']));

								?><i class="fas fa-info-circle hasTooltip" title="<?php echo $this->escape($prepTip); ?>" style="margin-left:4px;"></i><?php
							}
							?>
						</div>

						<?php if ($row['checkin_ts'] > VikRestaurants::now() && $row['service'] === 'delivery' && !empty($route_obj->duration) && $route_obj->duration > 0): ?>
							<div class="td-secondary">
								<?php echo JText::sprintf('VRMANAGETKRES34', date($config->get('timeformat'), $row['checkin_ts'] - $route_obj->duration)); ?>
							</div>
						<?php endif; ?>

						<div class="mobile-only badge badge-info">
							<?php echo $this->services[$row['service']] ?? $row['service']; ?>
						</div>
					</td>

					<!-- SERVICE -->

					<td class="hidden-phone">
						<div class="td-primary">
							<?php echo $this->services[$row['service']] ?? $row['service']; ?>
						</div>

						<?php if (!empty($route_obj->distancetext) || !empty($route_obj->durationtext)): ?>
							<div class="td-secondary">
								<?php if (!empty($route_obj->distancetext)): ?>
									<span style="display: inline-block; margin-right: 6px;">
										<i class="fas fa-road"></i>&nbsp;<?php echo $route_obj->distancetext; ?>
									</span>
								<?php endif; ?>

								<?php if (!empty($route_obj->durationtext)): ?>
									<span style="display: inline-block; margin-right: 6px;">
										<i class="fas fa-stopwatch"></i>&nbsp;<?php echo $route_obj->durationtext; ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if (!empty($route_obj->origin)): ?>
							<div class="td-secondary"><?php echo $route_obj->origin; ?></div>
						<?php endif; ?>
					</td>

					<!-- CUSTOMER -->

					<td class="hidden-phone">
						<div class="td-primary">
							<?php echo $row['purchaser_nominative']; ?>
						</div>
						
						<div class="td-secondary">
							<?php if ($row['purchaser_phone']): ?>
								<a href="tel:<?php echo $row['purchaser_phone']; ?>">
									<i class="fas fa-phone"></i>&nbsp;
									<?php echo $row['purchaser_phone']; ?>
								</a>
							<?php elseif ($row['purchaser_mail']): ?>
								<a href="mail:<?php echo $row['purchaser_mail']; ?>">
									<i class="fas fa-envelope"></i>&nbsp;
									<?php echo $row['purchaser_mail']; ?>
								</a>
							<?php endif; ?>
						</div>

						<?php if ($row['service'] === 'delivery' && $row['purchaser_address']): ?>
							<div class="td-secondary">
								<?php echo $row['purchaser_address']; ?>
							</div>
						<?php endif; ?>
					</td>

					<!-- ITEMS -->

					<td style="text-align: center;">
						<div class="td-primary hasTooltip" title="<?php echo $this->escape(JText::sprintf('VRTKRESITEMSINCART', $row['items_preparation_count'], $row['items_count'])); ?>">
							<?php if ($row['items_preparation_count'] > 0): ?>
								<div>
									<i class="fas fa-burn"></i>
									<?php echo $row['items_preparation_count']; ?>
								</div>
							<?php endif; ?>
							
							<?php if ($row['items_count'] - $row['items_preparation_count'] > 0): ?>
								<div>
									<i class="fas fa-snowflake"></i>
									<?php echo ($row['items_count'] - $row['items_preparation_count']); ?>
								</div>
							<?php endif; ?>
						</div>
					</td>

					<!-- TOTAL -->

					<td class="hidden-phone">
						<div class="td-primary">
							<?php echo $currency->format($row['total_to_pay']); ?>
						</div>
					</td>

					<!-- RESERVATION CODE -->

					<td style="text-align: center;" class="hidden-phone">
						<?php
						if (empty($row['code_icon']))
						{
							echo !empty($row['code']) ? $row['code'] : '';
						}
						else
						{
							?>
							<div class="vrrescodelink">
								<img src="<?php echo VREMEDIA_SMALL_URI . $row['code_icon']; ?>" class="hasTooltip" title="<?php echo $this->escape($row['code']); ?>" />
							</div>
							<?php
						}
						?>
					</td>

					<!-- STATUS -->

					<td>
						<?php
						echo JHtml::fetch('vrehtml.status.display', $row['status']);

						if (JHtml::fetch('vrehtml.status.ispending', 'takeaway', $row['status']))
						{
							$expires_in = VikRestaurants::formatTimestamp($config->get('dateformat') . ' ' . $config->get('timeformat'), $row['locked_until'], $local = false);
							?>
							<div class="td-secondary">
								<?php echo JText::sprintf('VRTKRESEXPIRESIN', $expires_in); ?>
							</div>
							<?php
						}
						?>
					</td>

				</tr>
				<?php
			}		
			?>
		</table>
		<?php
	}
	?>

	</div>
		
	<input type="hidden" name="view" value="tkbusyres" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="date" value="<?php echo $this->escape($this->filters['date']); ?>" />
	<input type="hidden" name="time" value="<?php echo $this->escape($this->filters['time']); ?>" />
	
</form>

<script>
	(function($) {
		'use strict';

		$(function() {
			onInstanceReady(() => {
				if ($.fn.chosen === undefined) {
					return false;
				}

				return true;
			}).then(() => {
				VikRenderer.chosen('#busy-filter-bar');
			});
		});
	})(jQuery);
</script>