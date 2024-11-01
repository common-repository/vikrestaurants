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
 * @var  Cart    $cart         The current cart instance.
 * @var  object  $reservation  The object holding the reservation details.
 */
extract($displayData);

$currency = VREFactory::getCurrency();

foreach ($cart->getItemsList() as $index => $item)
{
	// check if the item can be updated or deleted
	$canEdit = $item->isWritable() && !$reservation->bill_closed;
	?>
	<div class="dishes-cart-item-row" data-id="<?php echo (int) $item->getRecordID(); ?>">

		<div class="dish-item-quantity">
			<?php echo $item->getQuantity(); ?>x
		</div>

		<div class="dish-item-name">
			<span class="basename">
				<?php
				if ($canEdit)
				{
					?>
					<a href="javascript:void(0)" onclick="vrOpenDishOverlay(0, <?php echo (int) $index; ?>);">
						<?php echo $item->getName(); ?>
					</a>
					<?php
				}
				else
				{
					echo $item->getName();
				}
				?>
			</span>

			<?php if ($var = $item->getVariation()): ?>
				<span class="varname"><?php echo $var->name; ?></span>
			<?php endif; ?>
		</div>

		<div class="dish-item-price">
			<?php echo $currency->format($item->getTotalCost()); ?>	
		</div>

		<?php if ($canEdit): ?>
			<a href="javascript:void(0)" class="dish-item-delete" onclick="vrRemoveDishFromCart(<?php echo $index; ?>);">
				<i class="fas fa-minus-circle"></i>
			</a>
		<?php else: ?>
			<span class="dish-item-delete">&nbsp;</span>
		<?php endif; ?>
		
	</div>
	<?php
}
