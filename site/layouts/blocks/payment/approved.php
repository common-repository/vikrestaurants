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
 * Layout variables
 * -----------------
 * @var  array   $data   An associative array containing the transaction details.
 * @var  mixed   $order  An object containing the order/reservation details.
 * @var  string  $scope  The caller of this layout (e.g. restaurant, takeaway).
 */
extract($displayData);

// get payment details
$payment = $data['payment_info'];

?>

<a name="payment" style="display: none;"></a>

<div id="vr-pay-box" class="<?php echo $this->escape($payment->position); ?>">

	<?php
	// display notes after purchase
	if (!empty($order->payment->notes->afterPurchase)): ?>
		<div class="vrpaymentouternotes">
			<div class="vrpaymentnotes">
				<?php
				// assign notes to temporary variable
				$content = $order->payment->notes->afterPurchase;

				/**
				 * Render HTML description to interpret attached plugins.
				 * 
				 * @since 1.8
				 */
				VREApplication::getInstance()->onContentPrepare($content, $full = true);

				echo $content->text;
				?>
			</div>
		</div>
	<?php endif; ?>

</div>
