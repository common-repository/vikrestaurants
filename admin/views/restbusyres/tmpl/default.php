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

$config = VREFactory::getConfig();

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

			<?php
			$rooms = array_merge(
				[JHtml::fetch('select.option', 0, JText::translate('VRMAPSCHOOSEROOM'))],
				JHtml::fetch('vikrestaurants.rooms')
			);
			?>
			<div class="btn-group pull-right" style="margin-right: 6px;">
				<select name="id_room" id="vr-room-select" class="<?php echo ($this->filters['id_room'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
					<?php echo JHtml::fetch('select.options', $rooms, 'value', 'text', $this->filters['id_room']); ?>
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

					<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="15%" style="text-align: left;"><?php echo JText::translate('VRMANAGERESERVATION3'); ?></th>
					
					<!-- CUSTOMER -->

					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="15%" style="text-align: left;"><?php echo JText::translate('VRMANAGERESERVATION17'); ?></th>
					
					<!-- TABLE -->

					<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;"><?php echo JText::translate('VRMANAGERESERVATION5'); ?></th>
					
					<!-- RESERVATION CODE -->

					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">&nbsp;</th>
					
					<!-- STATUS -->

					<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="10%" style="text-align: left;"><?php echo JText::translate('VRMANAGERESERVATION12'); ?></th>
				
				</tr>
			<?php echo $vik->closeTableHead(); ?>

			<?php
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];
				?>
				<tr class="row<?php echo ($i % 2); ?>">

					<!-- ORDER NUMBER -->
					
					<td class="hidden-phone">
						<div class="td-primary"><?php echo $row['id']; ?></div>
					</td>

					<!-- CHECK-IN -->

					<td>
						<div class="td-primary">
							<?php echo JHtml::fetch('date', $row['checkin_ts'], JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'), date_default_timezone_get()); ?>
						</div>

						<div class="td-secondary">
							<?php echo JText::plural('VRE_N_PEOPLE', $row['people']); ?>
						</div>
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
					</td>

					<!-- TABLE -->

					<td style="text-align: center;">
						<div class="hasTooltip" title="<?php echo $this->escape($row['room_name']); ?>">
							<?php foreach ($row['tables'] as $tableName): ?>
								<span class="badge badge-info"><?php echo $tableName; ?></span>
							<?php endforeach; ?>
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

						if (JHtml::fetch('vrehtml.status.ispending', 'restaurant', $row['status']))
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
	
	<input type="hidden" name="view" value="restbusyres" />
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