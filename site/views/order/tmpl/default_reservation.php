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

$order = $this->order;

$config = VREFactory::getConfig();

?>
	
<div class="vrorderboxcontent">

	<h3 class="vrorderheader"><?php echo JText::translate('VRORDERTITLE2'); ?></h3>

	<div class="vrordercontentinfo">

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRORDERDATETIME'); ?></span>
			<span class="orderinfo-value">
				<?php echo $order->checkin_lc1; ?>
			</span>
		</div>

		<div class="vrorderinfo">
			<span class="orderinfo-label"><?php echo JText::translate('VRTKORDERDELIVERYSERVICE'); ?></span>
			<span class="orderinfo-value"><?php echo JHtml::fetch('vikrestaurants.tkservice', $order->service); ?></span>
		</div>
		
		<br clear="all"/>

		<?php foreach ($order->displayFields as $key => $val): ?>
			<div class="vrorderinfo">
				<span class="orderinfo-label"><?php echo $key; ?></span>
				<span class="orderinfo-value"><?php echo nl2br($val); ?></span>
			</div>
		<?php endforeach ?>

	</div>

	<?php
	// check whether the customer is eligible for the cancellation request
	if (VikRestaurants::canUserCancelOrder($order))
	{
		echo '<br clear="all"/>';

		// load cancellation form by using a sub-template
		echo $this->loadTemplate('cancellation');
	}
	?>

</div>