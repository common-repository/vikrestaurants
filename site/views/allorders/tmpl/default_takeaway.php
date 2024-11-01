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

if (count($this->orders) == 0)
{
	?>
	<div class="vr-allorders-void"><?php echo JText::translate('VRALLTKORDERSVOID'); ?></div>
	<?php
}
else
{
	?>
	<div class="vr-allorders-box">
		
		<div class="vr-allorders-tinylist">
			
			<?php foreach ($this->orders as $ord): ?>

				<div class="list-order-bar">

					<div class="order-oid">
						<?php echo substr($ord->sid, 0, 2) . '#' . substr($ord->sid, -2, 2); ?>
					</div>

					<div class="order-summary">
						<div class="summary-status">
							<?php echo JHtml::fetch('vrehtml.status.display', $ord->status); ?>
						</div>

						<div class="summary-service">
							<?php
							if (date('Y-m-d', $ord->created_on) != date('Y-m-d', $ord->checkin_ts))
							{
								// check-in different from creation date, display date too
								echo $ord->checkin_lc3;
							}
							else
							{
								// check-in equals to creation date, display only the time
								echo date(VREFactory::getConfig()->get('timeformat'), $ord->checkin_ts);
							}

							echo ', ' . JHtml::fetch('vikrestaurants.tkservice', $ord->service);
							?>
						</div>
					</div>

					<div class="order-purchase">
						<div class="purchase-date">
							<?php echo VikRestaurants::formatTimestamp(JText::translate('DATE_FORMAT_LC1'), $ord->created_on); ?>
						</div>

						<div class="purchase-price">
							<?php
							if ($ord->total_to_pay > 0)
							{
								echo VREFactory::getCurrency()->format($ord->total_to_pay);
							}
							?>
						</div>
					</div>

					<div class="order-view-button">
						<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=order&ordnum=' . $ord->id . '&ordkey=' . $ord->sid . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>">
							<?php echo JText::translate('VRVIEWORDER'); ?>					
						</a>
					</div>

				</div>

			<?php endforeach; ?>

		</div>
		
		<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=allorders' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
			<?php echo JHtml::fetch('form.token'); ?>
			<div class="vr-list-pagination"><?php echo $this->takeawayNavbut; ?></div>
			<input type="hidden" name="option" value="com_vikrestaurants" />
			<input type="hidden" name="view" value="allorders" />
		</form>
	
	</div>
	<?php
}
