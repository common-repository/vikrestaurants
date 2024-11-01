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

$currency = VREFactory::getCurrency();

?>

<div class="vrorderboxcontent">

	<h3 class="vrorderheader"><?php echo JText::translate('VRTKORDERTITLE3'); ?></h3>

	<div class="vrordercontentinfo restaurant-menus">

		<?php foreach ($this->reservation->items as $item): ?>
			<div class="vrtk-order-food">

				<div class="vrtk-order-food-details">

					<div class="vrtk-order-food-details-left">
						<span class="vrtk-order-food-details-name"><?php echo $item->name; ?></span>
					</div>

					<div class="vrtk-order-food-details-right">
						<span class="vrtk-order-food-details-quantity">x<?php echo $item->quantity; ?></span>

						<span class="vrtk-order-food-details-price">
							<?php echo $currency->format($item->price * $item->quantity); ?>
						</span>
					</div>

				</div>

				<?php if (!empty($item->notes)): ?>
					<div class="vrtk-order-food-notes">
						<?php echo $item->notes; ?>
					</div>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>

	</div>

</div>
