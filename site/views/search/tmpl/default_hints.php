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

/**
 * Template file used to display the suggested times
 * in case of no available tables.
 *
 * @since 1.8
 */

$config = VREFactory::getConfig();

?>

<div class="vrresultbookdiv vrfault">
	<span><?php echo JText::translate('VRRESNOSINGTABLEFOUND'); ?></span>
</div>

<div class="vrhintsouterdiv">
	<?php
	// make sure we have at least a valid hint
	if (array_filter($this->hints))
	{
		// insert selected time within the middle of the hints
		array_splice($this->hints, floor(count($this->hints) / 2), 0, array($this->checkinTime));
		?>
		<div class="vrresultbooktrydiv">
			<?php echo JText::translate('VRRESTRYHINTS'); ?>
		</div>
	
		<div class="vrresulttruehintsdiv">
			<?php
			// fetch base URL to try a different time
			$base_url = 'index.php?option=com_vikrestaurants&view=search&date=' . $this->args['date'] . '&people=' . $this->args['people'];

			/**
			 * Preserve safe distance preferences.
			 * 
			 * @since 1.9
			 */
			$family = JFactory::getApplication()->getUserState('vre.search.family');

			if ($family)
			{
				$base_url .= '&family=1';
			}

			if ($this->itemid)
			{
				$base_url .= '&Itemid=' . $this->itemid;
			}

			// iterate hints
			foreach ($this->hints as $hint)
			{
				// make sure we have a valid hint
				if ($hint)
				{
					// compare hint timestamp with checkin
					if ($hint->ts != $this->checkinTime->ts)
					{
						// display clickable hint
						?>
						<div class="vrresulthintsdiv">
							<a href="<?php echo JRoute::rewrite($base_url . '&hourmin=' . $hint->hour . ':' . $hint->min); ?>" class="vre-btn success">
								<?php echo $hint->format; ?>
							</a>
						</div>
						<?php
					}
					else
					{
						// display selected time slot
						?>
						<div class="vrresultdisabledhintsdiv">
							<button type="button" disabled class="vre-btn danger">
								<?php echo $hint->format; ?>
							</button>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
		<?php 
	}
	else
	{
		// no available hints
		?>
		<div class="vrresultfalsehintdiv">
			<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=restaurants' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vre-btn primary">
				<?php echo JText::translate('VRRESNOTABLESSELECTNEWDATES'); ?>
			</a>
		</div>
		<?php
	}
?>
</div>
