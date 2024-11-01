<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_search
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Use VikRestaurants scripts to handle default search events.
 *
 * @since 1.5
 */
JHtml::fetch('vrehtml.sitescripts.updateshifts', $restaurant = 1);
JHtml::fetch('vrehtml.sitescripts.datepicker', '#vrcalendarmod' . $module_id . ':input');
JHtml::fetch('bootstrap.tooltip');

$sel = $last_values;

/**
 * Get Itemid by checking the new property name.
 *
 * @since 1.4.1
 */
$itemid = (int) $params->get('itemid', 0);

if (!$itemid)
{
	$itemid = JFactory::getApplication()->input->getUint('Itemid', 0);
}

$itemid = $itemid ? '&Itemid=' . $itemid : '';

?>

<div class="moduletablevikre">
	
	<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=search' . $itemid); ?>" method="post" id="vr-search-form-<?php echo (int) $module_id; ?>">
		
		<div class="vrformfieldsetmod">
			
			<div class="vrsearchinputdivmod">
				<label class="vrsearchinputlabelmod" for="vrcalendarmod<?php echo (int) $module_id; ?>">
					<?php echo JText::translate('VRDATE'); ?>
				</label>
				
				<div class="vrsearchentryinputmod vrmod-search-wrappercal">
					<span class="vrmod-search-iconcal"></span>
					<input class="vrsearchdatemod" type="text" value="<?php echo htmlspecialchars($sel['date']); ?>" id="vrcalendarmod<?php echo (int) $module_id; ?>" name="date" size="20"/>
				</div>
			</div>

			<div class="vrsearchinputdivmod">
				<label class="vrsearchinputlabelmod" for="vrhourmod<?php echo (int) $module_id; ?>">
					<?php echo JText::translate('VRTIME'); ?>
				</label>
				
				<div class="vrsearchentryselectmod vre-select-wrapper">
					<?php
					// get available times
					$times = JHtml::fetch('vikrestaurants.times', $restaurant = 1, $sel['date']);

					$attrs = [
						'id'    => 'vrhourmod' . (int) $module_id,
						'class' => 'vrsearchhourmod vre-select',
					];

					// display times dropdown
					echo JHtml::fetch('vrehtml.site.timeselect', 'hourmin', $sel['hourmin'], $times, $attrs);
					?>
				</div>
			</div>

			<div class="vrsearchinputdivmod">
				<label class="vrsearchinputlabelmod" for="vrpeoplemod<?php echo (int) $module_id; ?>">
					<?php echo JText::translate('VRPEOPLE'); ?>
				</label>
				
				<div class="vrsearchentryselectmod vre-select-wrapper">
					<?php
					// get people options
					$options = JHtml::fetch('vikrestaurants.people');

					$attrs = [
						'id'    => 'vrpeoplemod' . (int) $module_id,
						'class' => 'vrsearchpeoplemod vre-select',
					];

					// display times dropdown
					echo JHtml::fetch('vrehtml.site.peopleselect', 'people', $sel['people'], $attrs);
					?>
				</div>
			</div>

			<?php
			/**
			 * Added support for safe distance disclaimer.
			 *
			 * @since 1.5
			 */
			if (VREFactory::getConfig()->getBool('safedistance'))
			{
				// ask to the customer whether all the members of the
				// group belong to the same family due to COVID-19
				// prevention measures
				?>
				<div class="vrsearchinputdivmod checkbox-wrapper">
					<input type="checkbox" name="family" id="vrfamilymod<?php echo (int) $module_id; ?>" value="1" <?php echo $sel['family'] ? 'checked="checked"' : ''; ?> />

					<label for="vrfamilymod<?php echo (int) $module_id; ?>">
						<?php echo JText::translate('VRSAFEDISTLABEL'); ?>
						<a href="javascript:void(0)" class="vrfamilymod-help" title="<?php echo htmlspecialchars(JText::translate('VRSAFEDISTLABEL_TIP')); ?>">
							<i class="fas fa-exclamation-triangle"></i>
						</a>
					</label>
				</div>
				<?php
			}
			?>

			<div class="vrsearchinputdivmod">
				<button type="submit" class="vre-btn primary">
					<?php echo JText::translate('VRFINDATABLE'); ?>
				</button>
			</div>
			
			<input type="hidden" name="option" value="com_vikrestaurants" />
			<input type="hidden" name="view" value="search" />
		</div>

	</form>

</div>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vrcalendarmod<?php echo (int) $module_id; ?>:input').on('change', () => {
				// refresh times
				vrUpdateWorkingShifts('#vrcalendarmod<?php echo (int) $module_id; ?>', '#vrhourmod<?php echo (int) $module_id; ?>');
			});

			$('#vr-search-form-<?php echo (int) $module_id; ?>').on('submit', () => {
				if ($('#vrhourmod<?php echo (int) $module_id; ?>').prop('disabled') === true) {
					<?php
					/**
					 * Prevent form submit while hourmin dropdown is empty.
					 * The system is still retriving the available times.
					 *
					 * @since 1.4.2
					 */
					?>
					return false;
				}
			});

			if ($.fn.tooltip) {
				// init tooltip only if available with the current jQuery instance
				$('.vrfamilymod-help').tooltip();
			} else {
				console.warn('jQuery.fn.tooltip is not available');
			}
		});
	})(jQuery);
</script>