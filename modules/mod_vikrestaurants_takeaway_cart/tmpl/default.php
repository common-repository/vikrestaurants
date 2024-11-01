<?php
/** 
 * @package     VikRestaurants
 * @subpackage  mod_vikrestaurants_takeaway_cart
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

$title = $params->get('carttitle');

$currency = VREFactory::getCurrency();

$cartTotalCost     = $cart->getTotalCost();
$cartTotalDiscount = $cart->getTotalDiscount();

/**
 * The module can be published more the once per page,
 * as all the IDs have been replaced with classes.
 *
 * @since 1.4.3
 */

$scroll = (int) $params->get('usefixed');
$sticky = (int) $params->get('mobilesticky') && $_TAKEAWAY_;

/**
 * Include starting block only in case the module should
 * follow the page scroll.
 *
 * @since 1.5.1
 */
if ($scroll)
{
	/**
	 * Use an empty space within the start tag because sometimes the
	 * div may follow the page scroll when the content is empty.
	 *
	 * @since 1.5.2
	 */
	?>
	<div class="vrtkcartstart" style="height:0;">&nbsp;</div>
	<?php
}
?>

<div class="vrtkcartitemsmodule<?php echo ($scroll ? ' vrtkcartfixed cart-fixed' : '') . ($sticky ? ' cart-mobile-sticky' : ''); ?>" id="vrtkcartitemsmodule<?php echo (int) $module_id; ?>">
	
	<div class="cart-inner-wrapper">

		<?php if (!empty($title)): ?>
			<div class="vrtkmodcarttitlecont">
				<span class="vrtkmodcarttitle"><?php echo $title; ?></span>
			</div>    
		<?php endif; ?>
		
		<div class="vrtkitemcontainer">

			<?php foreach ($cart->getItems() as $k => $item): ?>

				<div class="vrtkcartoneitemrow">

					<?php if ($_TAKEAWAY_): ?>

						<a href="javascript:void(0)" onClick="vrOpenOverlay('vrnewitemoverlay', '<?php echo htmlspecialchars($item->getName()); ?>', -1, -1, <?php echo (int) $k; ?>);">
							<div class="vrtkcartleftrow">
								<span class="vrtkcartenamesp"><?php echo $item->getItemName(); ?></span>
								<?php if ($item->getOptionID() > 0): ?>
									<span class="vrtkcartonamesp"><?php echo $item->getOptionName(); ?></span>
								<?php endif; ?>
							</div>
						</a>

					<?php else: ?>

						<div class="vrtkcartleftrow">
							<span class="vrtkcartenamesp"><?php echo $item->getItemName(); ?></span>
							<?php if ($item->getOptionID() > 0): ?>
								<span class="vrtkcartonamesp"><?php echo $item->getOptionName(); ?></span>
							<?php endif; ?>
						</div>

					<?php endif; ?>
					
					<div class="vrtkcartrightrow">
						<span class="vrtkcartquantitysp" data-quantity="<?php echo (int) $item->getQuantity(); ?>"><?php echo JText::translate('VRTKMODQUANTITYSUFFIX') . $item->getQuantity(); ?></span>

						<span class="vrtkcartpricesp">
							<?php
							$itemTotalPrice = $item->getTotalCost();
							
							if ($itemTotalPrice > 0)
							{
								echo $currency->format($itemTotalPrice);
							}
							else
							{
								echo JText::translate('VRFREE');
							}
							?>
						</span>

						<?php if ($item->getPrice() != $item->getOriginalPrice()): ?>
							<span class="vrtkcartpricesp-full"><s><?php echo $currency->format($item->getTotalCostBeforeDiscount()); ?></s></span>
						<?php endif; ?>

						<?php if (!$_TAKEAWAY_CONFIRM_ && $item->canBeRemoved()): ?>
							<span class="vrtkcartremovesp">
								<a href="javascript:void(0)" onClick="vrRemoveFromCart(<?php echo (int) $k; ?>)" class="vrtkcartremovelink">
									<i class="fas fa-minus-circle"></i>
								</a>
							</span>
						<?php endif; ?>
					</div>

				</div>

			<?php endforeach; ?>

		</div>

		<div class="vrtkcartfullcostoutmodule" style="<?php echo ($cartTotalDiscount == 0 ? "display: none;" : ""); ?>">
			<div class="vrtkcartfullcostmodule">
				<s><?php echo $currency->format($cartTotalCost); ?></s>
			</div>
		</div>
		
		<div class="vrtkcartdiscountoutmodule">
			<span class="vrtkcartdiscountlabelmodule">
				<?php echo JText::translate('VRTKMODCARTTOTALDISCOUNT'); ?>
			</span>

			<div class="vrtkcartdiscountmodule">
				<?php echo $currency->format($cartTotalDiscount); ?>
			</div>
		</div>
		
		<div class="vrtkcartpriceoutmodule">
			<span class="vrtkcartpricelabelmodule">
				<?php echo JText::translate('VRTKMODCARTTOTALPRICE'); ?>
			</span>
			<div class="vrtkcartpricemodule">
				<?php echo $currency->format($cartTotalCost - $cartTotalDiscount); ?>
			</div>
		</div>
		
		<div class="vrtkcartminorderdiv" style="display: none;">
			<?php
			echo JText::translate('VRTKMODCARTMINORDER') . ' ' . 
			$currency->format($minCostPerOrder);
			?>
		</div>
		
		<?php if(!$_TAKEAWAY_CONFIRM_): ?>
			<div class="vrtkcartbuttonsmodule">
				<div class="vrtkcartemptydivmodule">
					<span class="vrtkcartemptyspmodule">
						<button type="button" onClick="vrFlushCart();" class="vrtkcartemptybutton">
							<i class="fas fa-trash"></i>
						</button>
					</span>
				</div>

				<div class="vrtkcartorderdivmodule">
					<span class="vrtkcartorderspmodule">
						<button type="button" onClick="modVrGoToPay();" class="vrtkcartorderbutton"><?php echo JText::translate('VRTKMODORDERBUTTON'); ?></button>
					</span>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php
	/**
	 * In case the mobile mode is supported, display
	 * the sticky button.
	 *
	 * @since 1.5.1
	 */
	if ($sticky): ?>
		<button type="button" class="vre-btn primary cart-sticky-button" onclick="vrCartToggleMenu(this);">
			<i class="fas fa-shopping-basket"></i>

			<span class="vrtkcartpricemodule">
				<?php echo $currency->format($cartTotalCost - $cartTotalDiscount); ?>
			</span>
		</button>
	<?php endif; ?>
</div>

<?php
JText::script('VRFREE');
JText::script('VRTKMODQUANTITYSUFFIX');
JText::script('VRTKADDITEMERR2');
?>

<script>
	(function($, w) {
		'use strict';

		let vrcart_curr_price = <?php echo $cartTotalCost; ?>;
	
		w.VIKRESTAURANTS_CART_INSTANCE = 1;
		
		w.vrCartRefreshItems = (items, tcost, tdisc, grand_total) => {
			const currency = Currency.getInstance();
			
			let html = '';

			items.forEach((item) => {
				let stroke = '';

				if (item.price != item.original_price) {
					stroke = '<span class="vrtkcartpricesp-full"><s>' + currency.format(item.original_price) + '</s></span>\n';
				}
				
				html += '<div class="vrtkcartoneitemrow">\n' +
					'<a href="javascript:void(0)" onClick="vrOpenOverlay(\'vrnewitemoverlay\', \'' + item.item_name + (item.var_name.length > 0 ? " - " + item.var_name : "") + '\', -1, -1, ' + item.index + ');">\n' +
						'<div class="vrtkcartleftrow">\n' +
							'<span class="vrtkcartenamesp">' + item.item_name + '</span>\n'+
							'<span class="vrtkcartonamesp">' + item.var_name + '</span>\n'+
						'</div>\n' +
					'</a>\n' +
					'<div class="vrtkcartrightrow">\n' +
						'<span class="vrtkcartquantitysp" data-quantity="' + item.quantity + '">' + Joomla.JText._('VRTKMODQUANTITYSUFFIX') + item.quantity + '</span>\n';

						if (item.price > 0) {
							html += '<span class="vrtkcartpricesp">' + currency.format(item.price) + '</span>\n';
						} else {
							html += '<span class="vrtkcartpricesp">' + Joomla.JText._('VRFREE') + '</span>\n';
						}

				html += stroke;

				<?php if (!$_TAKEAWAY_CONFIRM_): ?>
					if (item.removable) { 
						html += '<span class="vrtkcartremovesp">\n'+
							'<a href="javascript:void(0)" onClick="vrRemoveFromCart(' + item.index + ')" class="vrtkcartremovelink">\n'+
								'<i class="fas fa-minus-circle"></i>\n'+
							'</a>\n'+
						'</span>\n';
					}
				<?php endif; ?>

				html += '</div>\n' +
					'</div>\n';
			});

			$('.vrtkitemcontainer').html(html);
			
			vrCartUpdateTotalCost(tcost, tdisc, grand_total, items);
		}
		
		w.vrCartUpdateTotalCost = (tcost, tdisc, grand_total, items) => {
			const currency = Currency.getInstance();

			$('.vrtkcartpricemodule').html(currency.format(grand_total));
			$('.vrtkcartfullcostmodule').html(currency.format(tcost));
			$('.vrtkcartdiscountmodule').html(currency.format(tdisc));
			
			if (tdisc > 0) {
				$('.vrtkcartfullcostoutmodule').show();
			} else {
				$('.vrtkcartfullcostoutmodule').hide();
			}
			
			vrcart_curr_price = grand_total;

			// set up an event to inform any subscriber that the cart has changed
			let event = $.Event('vikrestaurants.takeaway.cart.updated');
			
			// set up event data
			event.cart = {
				items: items,
				totals: {
					cost: tcost,
					discount: tdisc,
					final: grand_total,
				},
			};

			/**
			 * Notify the subscribers that the details of the cart have changed.
			 * 
			 * @since 1.6
			 */
			$(window).trigger(event);
		}
		
		w.vrRemoveFromCart = (id) => {
			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=takeaway.removefromcartajax' . ($itemid ? '&Itemid=' . $itemid : '')); ?>',
				{
					index: id,
				},
				(obj) => {
					vrCartRefreshItems(obj.items, obj.total, obj.discount, obj.finalTotal);
				},
				(error) => {
					if (!error.responseText || error.responseText.length > 1024) {
						error.responseText = Joomla.JText._('VRTKADDITEMERR2');
					}

					alert(error.responseText);
				}
			);
		}
		
		w.vrFlushCart = () => {
			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=takeaway.emptycartajax' . ($itemid ? '&Itemid=' . $itemid : '')); ?>',
				{},
				(resp) => {
					// remove all records
					$('.vrtkcartoneitemrow').remove();
					// reset totals
					vrCartUpdateTotalCost(0, 0, 0, []);
				},
				(error) => {
					if (!error.responseText || error.responseText.length > 1024) {
						error.responseText = Joomla.JText._('VRTKADDITEMERR2');
					}

					alert(error.responseText);
				}
			);		
		}
		
		w.modVrGoToPay = () => {
			let min_cost_per_res = <?php echo $minCostPerOrder; ?>;

			if (min_cost_per_res > vrcart_curr_price) {
				$('.vrtkcartminorderdiv').fadeIn().delay(2000).fadeOut();
			} else {
				document.location.href = '<?php echo $TAKEAWAY_CONFIRM_URL; ?>';
			}
		}

		w.vrIsCartVisibleOnScreen = () => {
			// get screen position and height
			let screen_y = $(w).scrollTop();
			let screen_h = $(w).height();

			// do not consider buttons, discount and total cost on cart bottom (around 100px)
			let some_cart_bottom_padding = 100;

			let visible = false;

			/**
			 * Since there may be several modules published
			 * within the same page, we should iterate all the
			 * modules to check if at least one of them is visible.
			 *
			 * @since 1.5
			 */
			$('.vrtkcartitemsmodule').each(function() {
				let cart_y = parseInt($(this).offset().top);
				let cart_h = parseInt($(this).height());

				if (screen_y <= cart_y && cart_y + cart_h - some_cart_bottom_padding < screen_y + screen_h) {
					// current module is visible
					visible = true;
					// break EACH
					return false;
				}
			});

			return visible;
		}

		w.vrCartToggleMenu = (button) => {
			// get collapsed cart
			const cart = $(button).siblings('.cart-inner-wrapper');

			if (cart.is(':visible')) {
				cart.slideUp();

				// make body scrollable again
				$('body').css('overflow', 'auto');
			} else {
				// open cart only in case one ore more items have been
				// added or transimtted to the kitchen
				if ($(cart).find('.vrtkcartoneitemrow').length) {
					cart.slideDown();

					// prevent body from scrolling when the cart
					// is expanded and the device is pretty small
					$('body').css('overflow', 'hidden');
				}
			}
		}

		$(function() {
			/**
			 * Define the cart top padding equals to the value specified by the
			 * configuration of the module.
			 * 
			 * @since 1.6.1
			 */
			w.TK_CART_TOP_PADDING = <?php echo (int) $params->get('paddingtop', 15); ?>;
		});
	})(jQuery, window);	
</script>