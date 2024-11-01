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
 * Template file used to display a summary of the ordered products.
 *
 * @since 1.8
 */

$cart = $this->cart;

$config   = VREFactory::getConfig();
$currency = VREFactory::getCurrency();

$show_taxes = $config->getBool('tkshowtaxes');
$use_taxes  = $config->getUint('tkusetaxes');

// calculate cart totals (net, tax and gross)
$totals = $cart->getTotals();
// get total cost (before tax)
$total_cost = $cart->getTotalCost();
// get total discount
$total_discount = $cart->getTotalDiscount();

?>

<div id="vrtkconfcartitemsdiv" class="vrtkconfcartitemsdiv">

	<!-- CART ITEMS -->

	<div id="vrtkconfitemcontainer">

		<?php
		foreach ($cart->getItems() as $k => $item)
		{
			?>
			<div id="vrtk-conf-itemrow<?php echo (int) $k; ?>" class="vrtkconfcartoneitemrow">

				<div class="vrtk-confcart-item-main">

					<div class="vrtkconfcartleftrow">
						<div class="vrtkconfcart-item-name">
							<small class="vrtkconfcartquantitysp">
								<?php echo $item->getQuantity() . JText::translate('VRTKCARTQUANTITYSUFFIX'); ?>
							</small>

							<span class="vrtkconfcartenamesp"><?php echo $item->getItemName(); ?></span>
							
							<?php if (strlen($item->getOptionName())): ?>
								<span class="vrtkconfcartonamesp">-&nbsp;<?php echo $item->getOptionName(); ?></span>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="vrtkconfcartrightrow">
						<span class="vrtkconfcartpricesp">
							<?php
							$item_total_price = $item->getTotalCost();
							
							if ($item_total_price > 0)
							{
								echo $currency->format($item_total_price);
							}
							else
							{
								echo JText::translate('VRFREE');
							}
							?>
						</span>

						<?php if ($item->getPrice() != $item->getOriginalPrice()): ?>
							<span class="vrtkconfcartpricesp-full">
								<s><?php echo $currency->format($item->getTotalCostBeforeDiscount()); ?></s>
							</span>
						<?php endif; ?>
						
						<?php if ($item->canBeRemoved()): ?>
							<span class="vrtkconfcartremovesp">
								<a href="<?php echo VREFactory::getPlatform()->getUri()->addCSRF('index.php?option=com_vikrestaurants&task=takeawayconfirm.removefromcart&index=' . $k . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vrtkconfcartremovelink">
									<i class="fas fa-minus-circle"></i>
								</a>
							</span>
						<?php endif; ?>
					</div>

				</div>

				<div class="vrtk-confcart-item-details">
					<?php if (count($item->getToppingsGroups())): ?>
						<div class="vrtk-confcart-item-toppings">
							<?php
							foreach ($item->getToppingsGroups() as $t_group)
							{
								$toppings = $t_group->getToppings();

								/**
								 * Before displaying the group title, make sure
								 * the customer selected at least a topping.
								 * Otherwise an empty label would be shown.
								 *
								 * @since 1.7.4
								 */
								if ($toppings)
								{
									?>
									<div class="vrtk-confcart-topping">
										<?php
										echo $t_group->getTitle() . ': ';
										
										foreach ($toppings as $index => $topping)
										{
											if ($index > 0)
											{
												echo ', ';
											}

											echo $topping->getName();

											/**
											 * Include picked units when higher than 1.
											 *
											 * @since 1.8.2
											 */
											if ($topping->getUnits() > 1)
											{
												echo ' x' . $topping->getUnits();
											}
										}
										?>
									</div>
									<?php
								}
							}
							?>
						</div>
					<?php endif; ?>
					
					<?php if (strlen($item->getAdditionalNotes())): ?>
						<div class="vrtk-confcart-notes"><?php echo $item->getAdditionalNotes(); ?></div>
					<?php endif; ?>
				</div>

			</div>
			<?php
		}
		?>

	</div>

	<!-- DISCOUNT VALUE -->

	<div class="vrtk-confcart-fullcost-details discount" style="<?php echo $total_discount > 0 ? '' : 'display: none;'; ?>">
			
		<span class="fullcost-label">
			<?php echo JText::translate('VRTKCARTTOTALDISCOUNT'); ?>
		</span>

		<div class="fullcost-amount" id="vrtkconfcartdiscount">
			<?php echo $currency->format($total_discount); ?>
		</div>

	</div>

	<!-- TOTAL NET -->

	<?php if ($totals->tax > 0): ?>
		<div class="vrtk-confcart-fullcost-details net">

			<span class="fullcost-label">
				<?php echo JText::translate('VRTKCARTTOTALNET'); ?>
			</span>

			<div class="fullcost-amount" id="vrtkconfcartfullcost">
				<span id="vrtkconfcartnet">
					<?php echo $currency->format($totals->net); ?>
				</span>
			</div>
		</div>
	<?php endif; ?>
	
	<!-- DELIVERY COST -->

	<div class="vrtk-confcart-fullcost-details service" style="display:none;">

		<span class="fullcost-label">
			<?php echo JText::translate('VRTKCARTTOTALSERVICE'); ?>
		</span>

		<div class="fullcost-amount" id="vrtkconfcartservice">
			<!-- filled via JS -->
		</div>

	</div>
	
	<!-- TAXES -->

	<?php if ($totals->tax > 0): ?>
		<div class="vrtk-confcart-fullcost-details taxes">
			
			<span class="fullcost-label">
				<?php echo JText::translate('VRTKCARTTOTALTAXES'); ?>
			</span>

			<div class="fullcost-amount" id="vrtkconfcarttaxes">
				<?php echo $currency->format($totals->tax); ?>
			</div>

		</div>
	<?php endif; ?>

	<!-- GRATUITY -->

	<?php
	$gratuity = 0;

	if ($config->getBool('tkenablegratuity'))
	{
		$def_gratuity = explode(':', $config->get('tkdefgratuity', ''));
		?>
		<div class="vrtk-confcart-fullcost-details gratuity">

			<span class="fullcost-label">
				<?php echo JText::translate('VRTKCARTTOTALTIP'); ?>
			</span>

			<div class="fullcost-amount" id="vrtkconfcartgratuity">
				<?php
				$gratuity = (float) $def_gratuity[0];

				if ($def_gratuity[1] == 1)
				{
					$gratuity = $total_cost * $gratuity / 100;
				}

				echo $currency->format($gratuity);
				?>
			</div>

			<div class="gratuity-inline-form">
				<input type="number" value="<?php echo (float) $def_gratuity[0]; ?>" min="0" step="any" max="9999" id="vrtk-gratuity-amount" />
				
				<div class="vre-select-wrapper">
					<select id="vrtk-gratuity-percentot" class="vre-select">
						<option value="1" <?php echo $def_gratuity[1] == 1 ? 'selected="selected"' : ''; ?>>%</option>
						<option value="2" <?php echo $def_gratuity[1] == 2 ? 'selected="selected"' : ''; ?>><?php echo $config->get('currencysymb', ''); ?></option>
					</select>
				</div>
			</div>

		</div>
		<?php
	}
	?>
	
	<!-- GRAND TOTAL -->

	<div class="vrtk-confcart-fullcost-details grand-total">
		
		<span class="fullcost-label">
			<?php echo JText::translate('VRTKCARTTOTALPRICE'); ?>
		</span>
		
		<div class="fullcost-amount" id="vrtkconfcartprice">
			<?php echo $currency->format($totals->gross); ?>
		</div>

	</div>
	
</div>

<script>
	(function($, w) {
		'use strict';

		w.TK_BASE_TOTAL = <?php echo $total_cost; ?>;

		w.vrGetGratuity = () => {
			let amount = parseFloat($('#vrtk-gratuity-amount').val());
			let type   = parseInt($('#vrtk-gratuity-percentot').val());

			if (isNaN(amount)) {
				// make sure gratuity is enabled
				return 0;
			}

			if (type == 1) {
				// calculate charge on total cost before taxes
				amount = w.TK_BASE_TOTAL * amount / 100;
			}

			return amount.roundTo(2);
		}

		w.vrRefreshGrandTotal = () => {
			// get selected service
			let service = vrGetSelectedService();

			// recalculate service charge and auto-update the totals
			calculateServiceCharge(service);
		}

		w.calculateServiceCharge = (service) => {
			// get gratuity
			let gratuity = vrGetGratuity();

			const currency = Currency.getInstance();

			return new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=takeawayconfirm.getservicecharge' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					{
						service: service,
						area: TK_DELIVERY_AREA,
					},
					(resp) => {
						resolve(resp);
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						reject(error.responseText);
					}
				);
			}).then((resp) => {
				// add gratuity to total gross
				resp.totals.gross = currency.sum(resp.totals.gross, gratuity);

				if (resp.type === 'charge') {
					// refresh service cost
					$('#vrtkconfcartservice').text(currency.format(resp.charge.gross)).parent().show();

					// add service gross to total
					resp.totals.gross = currency.sum(resp.totals.gross, resp.charge.gross);
				} else {
					// hide service cost
					$('#vrtkconfcartservice').text(currency.format(0)).parent().hide();
				}

				// net
				$('#vrtkconfcartnet').text(currency.format(resp.totals.net));
				// discount
				$('#vrtkconfcartdiscount').text(currency.format(resp.totals.discount));
				// taxes
				$('#vrtkconfcarttaxes').text(currency.format(resp.totals.tax));
				// grand total
				$('#vrtkconfcartprice').text(currency.format(resp.totals.gross));

				if (resp.totals.discount > 0) {
					$('#vrtkconfcartdiscount').parent().show();
				} else {
					$('#vrtkconfcartdiscount').parent().hide();
				}
			}).catch((error) => {
				alert(error);
			});
		}

		$(function() {
			// gratuity
			$('#vrtk-gratuity-amount, #vrtk-gratuity-percentot').on('change', () => {
				// calculate gratuity
				let gratuity = vrGetGratuity();
				// update tip label
				$('#vrtkconfcartgratuity').text(Currency.getInstance().format(gratuity));

				// refresh total cost
				vrRefreshGrandTotal();
			});
		});
	})(jQuery, window);
</script>