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

<h3><?php echo JText::translate('VRMANAGETKRES22'); ?></h3>

<div class="order-items-cart">

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"items.start","type":"field"} -->

	<?php
	// plugins can use the "items.start" key to introduce custom
	// HTML before the items list
	if (isset($this->addons['items.start']))
	{
		echo $this->addons['items.start'];

		// unset items start form to avoid displaying it twice
		unset($this->addons['items.start']);
	}
	?>

	<?php foreach ($this->order->items as $item): ?>

		<div class="cart-item-record">

			<div class="cart-item-details">
				
				<div class="cart-item-name">
					<span class="item-prod-name"><?php echo $item->productName; ?></span>

					<?php if ($item->id_option): ?>
						<span class="item-option-name badge badge-info"><?php echo $item->optionName; ?></span>
					<?php endif; ?>
				</div>

				<div class="cart-item-quantity">
					x<?php echo $item->quantity; ?>
				</div>

				<div class="cart-item-price">
					<?php echo $currency->format($item->price); ?>
				</div>

			</div>

			<?php
			if ($item->toppings)
			{
				foreach ($item->toppings as $group)
				{
					?>
					<div class="cart-item-toppings">
						<span class="cart-item-toppings-group">
							<?php echo $group->title; ?>:
						</span>
						<span class="cart-item-toppings-list">
							<?php echo $group->str; ?>
						</span>
					</div>
					<?php
				}
			}
			?>

			<?php if ($item->notes): ?>
				<div class="cart-item-notes"><?php echo $item->notes; ?></div>
			<?php endif; ?>

		</div>

	<?php endforeach; ?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"items.end","type":"field"} -->

	<?php
	// plugins can use the "items.end" key to introduce custom
	// HTML next to the purchased items
	if (isset($this->addons['items.end']))
	{
		echo $this->addons['items.end'];

		// unset items end form to avoid displaying it twice
		unset($this->addons['items.end']);
	}
	?>

</div>
