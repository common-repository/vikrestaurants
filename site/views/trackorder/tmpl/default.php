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

JHtml::fetch('vrehtml.sitescripts.animate');

$config = VREFactory::getConfig();

$now = VikRestaurants::now();

if (!$this->order || $this->order->statusRole != 'APPROVED')
{
	// order not found or not confirmed yet
	?>
	<div class="vr-confirmpage order-error"><?php echo JText::translate('VRCONFORDNOROWS'); ?></div>
	<?php
}
else
{
	// order found and confirmed
	if (!$this->history)
	{
		// no order status
		?>
		<div class="vr-confirmpage"><?php echo JText::translate('VRTRACKORDERNOSTATUS'); ?></div>
		<?php
	}
	else
	{
		// display list of statuses
		?>
		<div class="vr-trackorder-wrapper">

			<?php foreach ($this->history as $day => $list): ?>

				<div class="vr-trackorder-day">

					<div class="vr-trackorder-day-head">
						<?php echo JHtml::fetch('date', $day, JText::translate('DATE_FORMAT_LC1'), date_default_timezone_get()); ?>
					</div>

					<div class="vr-trackorder-day-list">

						<?php foreach ($list as $status): ?>

							<div class="vr-trackorder-status">

								<span class="vr-trackorder-status-time">
									<?php echo date($config->get('timeformat'), $status->createdon); ?>
								</span>

								<span class="vr-trackorder-status-details">
									<?php
									// fetch code description
									$description = strlen((string) $status->notes) ? $status->notes : $status->codeNotes;

									if (!$description)
									{
										// code description not found, use plain code
										$description = $status->code;
									}

									echo $description;
									?>
								</span>

								<?php if ($now - $status->createdon < 7200): ?>
									<span class="vr-trackorder-status-ago">
										(<?php echo VikRestaurants::formatTimestamp('', $status->createdon); ?>)
									</span>
								<?php endif; ?> 

							</div>
							
						<?php endforeach; ?>

					</div>

				</div>

			<?php endforeach; ?>

		</div>
		<?php
	}
}
