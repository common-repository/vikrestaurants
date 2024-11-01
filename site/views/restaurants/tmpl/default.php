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

// refresh working shifts every time the date changes
JHtml::fetch('vrehtml.sitescripts.updateshifts', $restaurant = 1);
JHtml::fetch('vrehtml.sitescripts.datepicker', '#vrcalendar:input');
JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.fontawesome');

// display step bar using the view sub-template
echo $this->loadTemplate('stepbar');
?>

<!-- reservation form -->

<div class="vrreservationform" id="vrsearchform" >

	<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=search' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
		
		<fieldset class="vrformfieldset">
			<legend><?php echo JText::translate('VRMAKEARESERVATION'); ?></legend>

			<div class="vrsearchinputdiv">
				<label class="vrsearchinputlabel" for="vrcalendar">
					<?php echo JText::translate('VRDATE'); ?>
				</label>
				
				<div class="vrsearchentryinput vre-calendar-wrapper">
					<input class="vrsearchdate vre-calendar" type="text" value="<?php echo $this->escape($this->args['date']); ?>" id="vrcalendar" name="date" size="20" />
				</div>
			</div>

			<div class="vrsearchinputdiv">
				<label class="vrsearchinputlabel" for="vrhour">
					<?php echo JText::translate('VRTIME'); ?>
				</label>

				<div class="vrsearchentryselect vre-select-wrapper">
					<?php
					// get available times
					$times = JHtml::fetch('vikrestaurants.times', $restaurant = 1, $this->args['date']);

					$attrs = [
						'id'    => 'vrhour',
						'class' => 'vre-select',
					];

					// display times dropdown
					echo JHtml::fetch('vrehtml.site.timeselect', 'hourmin', $this->args['hourmin'], $times, $attrs);
					?>
				</div>
			</div>

			<div class="vrsearchinputdiv">
				<label class="vrsearchinputlabel" for="vrpeople">
					<?php echo JText::translate('VRPEOPLE'); ?>
				</label>

				<div class="vrsearchentryselectsmall vre-select-wrapper">
					<?php
					// get people options
					$options = JHtml::fetch('vikrestaurants.people');

					$attrs = [
						'id'    => 'vrpeople',
						'class' => 'vre-select',
					];

					// display times dropdown
					echo JHtml::fetch('vrehtml.site.peopleselect', 'people', $this->args['people'], $attrs);
					?>
				</div>
			</div>

			<div class="vrsearchinputdiv">
				<?php
				// ask to the customer whether all the members of the
				// group belong to the same family due to COVID-19
				// prevention measures
				if (VREFactory::getConfig()->getBool('safedistance')): ?>
					<?php JHtml::fetch('bootstrap.tooltip', '.vrfamily-help'); ?>
					<div class="vre-family-check">
						<input type="checkbox" name="family" id="vrfamily" value="1" <?php echo $this->family ? 'checked="checked"' : ''; ?> />

						<label for="vrfamily">
							<?php echo JText::translate('VRSAFEDISTLABEL'); ?>
							<a href="javascript:void(0);" class="vrfamily-help" title="<?php echo $this->escape(JText::translate('VRSAFEDISTLABEL_TIP')); ?>">
								<i class="fas fa-exclamation-triangle"></i>
							</a>
						</label>
					</div>
				<?php endif; ?>

				<button type="submit" class="vre-btn primary big" id="vre-find-table-btn">
					<?php echo JText::translate('VRFINDATABLE'); ?>
				</button>
			</div>
			
			<input type="hidden" name="option" value="com_vikrestaurants" />
			<input type="hidden" name="view" value="search" />
		</fieldset>

	</form>

</div>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vrcalendar:input').on('change', () => {
				// auto-refresh times on date change
				vrUpdateWorkingShifts('#vrcalendar', '#vrhour');
			});
		});
	})(jQuery);
</script>