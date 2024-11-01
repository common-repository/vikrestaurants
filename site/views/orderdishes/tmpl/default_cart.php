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

$config   = VREFactory::getConfig();
$currency = VREFactory::getCurrency();

?>

<div class="vre-order-dishes-cart">

	<div class="dishes-cart-collapsed">

		<div class="dishes-transmit-wrapper">

			<?php if (!$this->reservation->bill_closed): ?>
				<button type="button" class="vre-btn success" id="vre-transmit-btn">
					<?php echo JText::translate('VRTAKEAWAYORDERBUTTON'); ?>
				</button>

				<button type="button" class="vre-btn danger" id="vre-closebill-btn">
					<?php echo JText::translate('VREORDERFOOD_CLOSE_BILL'); ?>
				</button>
			<?php endif; ?>

			<button type="button" class="vre-btn primary" id="vre-paynow-btn" style="<?php echo $this->reservation->bill_closed ? '' : 'display:none;'; ?>">
				<?php echo JText::translate('VREORDERFOOD_PAY_NOW'); ?>
			</button>
		</div>

		<div class="dishes-cart-items" id="vre-cart-items">
			<?php
			// display cart by using a layout
			echo JLayoutHelper::render('orderdish.cart', [
				'cart'        => $this->cart,
				'reservation' => $this->reservation,
			]);
			?>
		</div>
		
	</div>

	<button type="button" class="vre-btn primary dishes-cart-minified">
		<i class="fas fa-shopping-cart"></i>

		<?php
		$cart_total = $this->cart->getTotalCost();
		?>

		<span id="vre-cart-total" data-total="<?php echo (float) $cart_total; ?>">
			<?php echo JText::sprintf('VRCARTTOTALBUTTON', $currency->format($cart_total)); ?>
		</span>
	</button>

</div>

<?php
JText::script('VRTKCARTDISHESHOWWORK');
JText::script('VRTKCARTDISHTRANSMITTED');
JText::script('VRTKCARTDISHTRANSMITTED_SHORT');
JText::script('VREORDERFOOD_CLOSE_BILL_PENDING');
JText::script('VREORDERFOOD_CLOSE_BILL_PROCEED');
JText::script('VREORDERFOOD_CLOSE_BILL_DISCLAIMER');
?>

<script>
	(function($) {
		'use strict';

		const vrCheckTransmitBtnStatus = () => {
			// get transmit button
			const btn = $('#vre-transmit-btn');

			// check if we have any pending items
			if ($('#vre-cart-items').children().filter('[data-id="0"]').length) {
				// enable button to allow dishes transmission
				btn.prop('disabled', false);
			} else {
				// disable button
				btn.prop('disabled', true);
			}
		}

		const vrCheckCloseBillBtnStatus = () => {
			// get close bill button
			const btn = $('#vre-closebill-btn');

			// check if we have any items already transmitted
			if ($('#vre-cart-items').children().filter('[data-id!="0"]').length) {
				// display button
				btn.show();
			} else {
				// hide button
				btn.hide();
			}
		}

		const vrUpdateCartTotal = (total) => {
			// format total as currency
			let ftotal = Currency.getInstance().format(total);
			// fetch total text
			ftotal = Joomla.JText._('VRTKADDTOTALBUTTON').replace(/%s/, ftotal);
			// update text
			$('#vre-cart-total').text(ftotal).attr('data-total', total);
		}

		window.vrGetCartTotal = () => {
			return parseFloat($('#vre-cart-total').attr('data-total'));
		}

		window.vrAddDishToCart = (data) => {
			// create promise
			return new Promise((resolve, reject) => {
				// make request to add dish within the cart
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=orderdish.addcart' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					data,
					(resp) => {
						// update total
						vrUpdateCartTotal(resp.total);

						// refresh cart
						$('#vre-cart-items').html(resp.cartHTML);

						// check status of trasmit button
						vrCheckTransmitBtnStatus();
						// check status of close bill button
						vrCheckCloseBillBtnStatus();

						let cached = false;

						if (typeof Storage !== 'undefined') {
							// check if we already alerted the message
							cached = sessionStorage.getItem('vreOrderDishesGuide');
						}

						if (!cached) {
							// check if the cart is expanded
							if (!$('.dishes-cart-collapsed').is(':visible')) {
								// expand cart
								$('button.dishes-cart-minified').trigger('click');
							}

							// display how the system works only once
							VREToast.dispatch({
								text:   Joomla.JText._('VRTKCARTDISHESHOWWORK'),
								status: VREToast.NOTICE_STATUS,
								delay:  25000,
								action: () => {
									// dispose message when clicked
									VREToast.dispose(true);

									// hide cart if visible
									if ($('.dishes-cart-collapsed').is(':visible')) {
										$('button.dishes-cart-minified').trigger('click');
									}
								},
							});

							if (typeof Storage !== 'undefined') {
								sessionStorage.setItem('vreOrderDishesGuide', 1);
							}
						}

						// resolve with received response
						resolve(resp);
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						// display reason of the error
						alert(error.responseText);

						// reject with error
						reject(error);
					}
				);
			});
		}

		window.vrRemoveDishFromCart = (index) => {
			// create promise
			return new Promise((resolve, reject) => {
				// make request to add dish within the cart
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=orderdish.removecart' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					{
						ordnum: <?php echo $this->reservation->id; ?>,
						ordkey: '<?php echo $this->reservation->sid; ?>',
						index:  index,
					},
					(resp) => {
						// update total
						vrUpdateCartTotal(resp.total);

						// refresh cart
						$('#vre-cart-items').html(resp.cartHTML);

						if ($('#vre-cart-items').children().length == 0) {
							// toggle cart in case of no children
							$('button.dishes-cart-minified').trigger('click');
						}

						// check status of trasmit button
						vrCheckTransmitBtnStatus();
						// check status of close bill button
						vrCheckCloseBillBtnStatus();

						// resolve with received response
						resolve(resp);
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						// display reason of the error
						alert(error.responseText);

						// reject with error
						reject(error);
					}
				);
			});
		}

		window.vrTransmitPendingOrder = (btn) => {
			// create promise
			return new Promise((resolve, reject) => {
				// make request to add dish within the cart
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=orderdish.transmit' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					{
						ordnum: <?php echo $this->reservation->id; ?>,
						ordkey: '<?php echo $this->reservation->sid; ?>',
					},
					(resp) => {
						// refresh cart
						$('#vre-cart-items').html(resp.cartHTML);

						// check status of trasmit button
						vrCheckTransmitBtnStatus();
						// check status of close bill button
						vrCheckCloseBillBtnStatus();

						// resolve with received response
						resolve(resp);
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						// display reason of the error
						alert(error.responseText);

						// reject with error
						reject(error);
					}
				);
			});
		}

		window.vrCloseBill = (btn) => {
			// create promise
			return new Promise((resolve, reject) => {
				// make request to add dish within the cart
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=orderdish.closebill' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					{
						ordnum: <?php echo $this->reservation->id; ?>,
						ordkey: '<?php echo $this->reservation->sid; ?>',
					},
					(resp) => {
						// update total
						vrUpdateCartTotal(resp.total);

						// refresh cart
						$('#vre-cart-items').html(resp.cartHTML);

						// resolve with received response
						resolve(resp);
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						// display reason of the error
						alert(error.responseText);

						// reject with error
						reject(error);
					}
				);
			});
		}

		$(function() {
			$('button.dishes-cart-minified').on('click', function() {
				// get collapsed cart
				const cart = $('.dishes-cart-collapsed');

				// check if the current device is (probably) a mobile
				const isMobile = window.matchMedia && window.matchMedia("only screen and (max-width: 640px)").matches;

				if (cart.is(':visible')) {
					cart.slideUp();

					// make body scrollable again
					$('body').css('overflow', 'auto');
				} else {
					// open cart only in case one ore more items have been
					// added or transimtted to the kitchen
					if ($('#vre-cart-items').children().length) {
						cart.slideDown();

						if (isMobile) {
							// prevent body from scrolling when the cart
							// is expanded and the device is pretty small
							$('body').css('overflow', 'hidden');
						}
					}
				}
			});

			$('#vre-transmit-btn').on('click', function() {
				// transmit pending orders
				vrTransmitPendingOrder(this).then((data) => {
					let cached = false;

					if (typeof Storage !== 'undefined') {
						// check if we already alerted the message
						cached = sessionStorage.getItem('vreTransmitAlert');
					}

					if (!cached) {
						<?php if ($config->getBool('editfood')): ?>
							// inform the customers that they are still allowed to
							// edit dishes as long as they are not under preparation
							let transmitMsgKey = 'VRTKCARTDISHTRANSMITTED';
						<?php else: ?>
							// inform the customers that the dishes have been 
							// transmitted to the kitchen
							let transmitMsgKey = 'VRTKCARTDISHTRANSMITTED_SHORT';
						<?php endif; ?>

						// alert message only once
						VREToast.dispatch({
							text:   Joomla.JText._(transmitMsgKey),
							status: VREToast.SUCCESS_STATUS,
							delay:  15000,
							action: () => {
								// dispose message when clicked
								VREToast.dispose(true);
							},
						});

						if (typeof Storage !== 'undefined') {
							sessionStorage.setItem('vreTransmitAlert', 1);
						}
					}
				});
			});

			$('#vre-closebill-btn').on('click', function() {
				let msg;

				if ($('#vre-cart-items').children().filter('[data-id="0"]').length) {
					// pending orders
					msg = Joomla.JText._('VREORDERFOOD_CLOSE_BILL_PENDING');
				} else {
					// no pending order
					msg = Joomla.JText._('VREORDERFOOD_CLOSE_BILL_PROCEED');
				}

				// add disclaimer
				msg += "\n" + Joomla.JText._('VREORDERFOOD_CLOSE_BILL_DISCLAIMER');

				// ask for a confirmation
				let r = confirm(msg);

				if (!r) {
					// action refused
					return false;
				}

				// request bill closure
				vrCloseBill(this).then((data) => {
					// remove buttons used to transmit and close the bill
					$('#vre-transmit-btn, #vre-closebill-btn').remove();

					// show button to proceed with the payment
					$('#vre-paynow-btn').show();
				});
			});

			$('#vre-paynow-btn').on('click', function() {
				<?php if (count($this->payments)): ?>
					// open payment overlay
					vrOpenPaymentOverlay();
				<?php else: ?>
					// redirect to reservation summary page
					document.location.href = '<?php echo $this->paymentURL; ?>';
				<?php endif; ?>
			});

			// check status of trasmit button
			vrCheckTransmitBtnStatus();
			// check status of close bill button
			vrCheckCloseBillBtnStatus();
		});
	})(jQuery);
</script>