<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

JHtml::fetch('vrehtml.assets.fancybox');

RestaurantsHelper::printMenu();

?>

<div class="viwppro-cnt">

	<div class="vikwp-alreadypro">
		<?php _e('Already purchased Vik Restaurants PRO? Upgrade to the PRO version <a href="#upgrade">here</a>.', 'vikrestaurants'); ?>
	</div>

	<div class="vikwppro-header">
		<div class="vikwppro-header-inner">
			<div class="vikwppro-header-text">
				<h2>
					<?php _e('Would you like to renew and expand your business?', 'vikrestaurants'); ?>
				</h2>
				<h3>
					<?php _e('Start using your own Take-Away and Restaurant Reservations system', 'vikrestaurants'); ?>
				</h3>
				<h4>
					<?php _e('VikRestaurants PRO: the most advanced Take-Away and Restaurant Reservations Manager for WordPress', 'vikrestaurants'); ?>
				</h4>
				<ul>
					<li><i class="fas fa-check"></i> <?php _e('Suitable for any kind of food-service business', 'vikrestaurants'); ?></li>
					<li><i class="fas fa-check"></i> <?php _e('Powerful Take-Away e-commerce system', 'vikrestaurants'); ?></li>
					<li><i class="fas fa-check"></i> <?php _e('Everything you need to manage Restaurant Reservations', 'vikrestaurants'); ?></li>
				</ul>
				<a href="https://vikwp.com/plugin/vikrestaurants?utm_source=free_version&utm_medium=vre&utm_campaign=gotopro" target="_blank" id="vikwpgotoget" class="vikwp-btn-link">
					<i class="fas fa-rocket"></i> <?php _e('Upgrade to PRO', 'vikrestaurants'); ?>
				</a>
			</div>
			<div class="vikwppro-header-img">
				<a href="javascript:void(0)" class="vremodal" onClick="vreOpenGallery(this);">
					<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI; ?>images/order-food-online.png" alt="VikRestaurants Pro" />
				</a>
			</div>
		</div>
	</div>

	<div class="viwppro-feats-cnt">
		<div class="viwppro-feats-row vikwppro-even viwppro-row-heightsmall">
			<div class="viwppro-feats-img">
				<a href="javascript:void(0)" class="vremodal" onClick="vreOpenGallery(this);">
					<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI; ?>images/toppings-management.png" alt="Product Toppings Management" />
				</a>
				<a href="javascript:void(0)" class="vremodal" onClick="vreOpenGallery(this);" style="display: none;">
					<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI; ?>images/create-poke-site.png" alt="Create a Poke Bowl" />
				</a>
			</div>
			<div class="viwppro-feats-text">
				<h4>
					<?php _e('Unlimited Take Away Products with Pickup or Delivery', 'vikrestaurants'); ?>
				</h4>
				<p>
					<?php _e('Set up unlimited Menus with unlimited Products for your take-away orders. Allow your clients to choose the Delivery Service or Self Pickup by adding an optional fee. Let your clients customize their food with a thin or large crust for their pizza, the toppings they want, or by composing a Poke with their preferred ingredients.', 'vikrestaurants'); ?>
				</p>
			</div>
		</div>

		<div class="viwppro-feats-row vikwppro-odd viwppro-row-heightsmall">
			<div class="viwppro-feats-text">
				<h4>
					<?php _e('Smart Restaurant Reservations', 'vikrestaurants'); ?>
				</h4>
				<p>
					<?php _e('Draw the exact tables of your restaurant with optional rooms for letting your guests book their preferred table. Show your clients that post-pandemic safety matters at your place, by letting VikRestaurants apply a safe distance between the table seats. Thanks to the Smart Dishes Ordering feature, guests will be able to order their food directly from their table by using any smartphone device.', 'vikrestaurants'); ?>
				</p>
			</div>
			<div class="viwppro-feats-img">
				<a href="javascript:void(0)" class="vremodal" onClick="vreOpenGallery(this);">
					<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI; ?>images/map-design-tool.gif" alt="Map Design Tool" />
				</a>
				<a href="javascript:void(0)" class="vremodal" onClick="vreOpenGallery(this);" style="display: none;">
					<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI; ?>images/map-overview.png" alt="Room Overview" />
				</a>
			</div>
		</div>

		<div class="viwppro-feats-row vikwppro-even">
			<div class="viwppro-feats-img">
				<a href="javascript:void(0)" class="vremodal" onClick="vreOpenGallery(this);">
					<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI; ?>images/dashboard.png" alt="Dashboard" />
				</a>
			</div>
			<div class="viwppro-feats-text">
				<h4>
					<?php _e('All you need to digitalize your food service business', 'vikrestaurants'); ?>
				</h4>
				<p>
					<?php _e('An all-in-one solution suitable for any restaurant activity. Turn your website into a powerful e-commerce system to allow the ordering of take-away food, whether through self-pick up or delivery in specific and pre-defined areas of your city.', 'vikrestaurants'); ?>
				</p>
				<p>
					<?php _e('The other powerful section of VikRestaurants will let you manage all of your Table Reservations, from the positioning of the tables to the food ordering at the table. Two complete solutions in just one plugin. Turn on either one or both features depending on your type of business.', 'vikrestaurants'); ?>
				</p>
			</div>
		</div>
	</div>

	<div class="viwppro-extra">
		<h3>
			<?php _e('Unlock over 50 must-have features', 'vikrestaurants'); ?>
		</h3>

		<div class="viwppro-extra-inner">

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-utensils"></i>
						<h4>
							<?php _e('Menus & Products', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Create unlimited menus with unlimited products. No limits applied!', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-shopping-cart"></i>
						<h4>
							<?php _e('Orders & Cart Management', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Manage your orders and apply modifications on the food requested.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-bacon"></i>
						<h4>
							<?php _e('Toppings', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Set up unlimited Toppings for your food. Let your clients build their custom pizza or dish.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-pepper-hot"></i>
						<h4>
							<?php _e('Food Attributes', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Describe your food with all characteristics for allergens and ingredients.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-archive"></i>
						<h4>
							<?php _e('Stocks Management System', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Manage the stocks of your products and food to update the remaining quantity or to add refills.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-ticket-alt"></i>
						<h4>
							<?php _e('Deals', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Set up custom offers to highlight particular dates and to target different guests.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-map-marker-alt"></i>
						<h4>
							<?php _e('Delivery Areas', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Define the exact delivery areas you can cover for delivering the food. No limits applied!', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-star"></i>
						<h4>
							<?php _e('Reviews', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Start collecting your own customers reviews for your food and service.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-users"></i>
						<h4>
							<?php _e('Customers Management', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Create your customers database on your website.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-calendar-check"></i>
						<h4>
							<?php _e('Reservations &amp; Bills', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Keep track of all reservations and bills. All the management functions you need.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-paint-brush"></i>
						<h4>
							<?php _e('Rooms Design Tool', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Draw the exact map of your rooms and tables by using a smart and intuitive tool.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-user-tie"></i>
						<h4>
							<?php _e('Operators', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Let your operators manage the reservations and orders from a smart private area in the front-end.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-font"></i>
						<h4>
							<?php _e('Custom Fields', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Set up the information you would like to collect from each client and for each reservation.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-file-pdf"></i>
						<h4>
							<?php _e('Invoices', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Generate and update the invoices for any bill or order.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-bookmark"></i>
						<h4>
							<?php _e('Reservation Codes', 'vikrestaurants'); ?>
						</h4>
						<p>
								<?php _e('Keep the status of all reservations and orders up to date: <em>preparing</em>, <em>delivered</em>, <em>bill paid</em> and so on.', 'vikrestaurants'); ?>
							</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-window-maximize"></i>
						<h4>
							<?php _e('Additional Widgets', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('The PRO version comes with several and useful new widgets for your website.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-low-vision"></i>
						<h4>
							<?php _e('Access Permissions', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Define the permissions for each administrator to allow or deny the access to certain pages.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-plug"></i>
						<h4>
							<?php _e('API Framework', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Enable a complete API endpoint for your website to allow the integration with third-party apps.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-sms"></i>
						<h4>
							<?php _e('SMS Gateways', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Set up custom SMS gateways to send messages to your guests.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="viwppro-extra-item">
				<div class="viwppro-extra-item-inner">
					<div class="viwppro-extra-item-text">
						<i class="fas fa-credit-card"></i>
						<h4>
							<?php _e('Payment Gateways', 'vikrestaurants'); ?>
						</h4>
						<p>
							<?php _e('Allow payments for reservations and orders directly through your website.', 'vikrestaurants'); ?>
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="vikwp-extra-more"><?php _e('and much more...', 'vikrestaurants'); ?></div>
		<a name="upgrade"></a>

	</div>

	<div class="vikwppro-licensecnt">
		<div class="col col-md-6 col-sm-12 vikwppro-licensetext">
			<div>
				<h3>
					<?php _e('Ready to renew your business?', 'vikrestaurants'); ?>
				</h3>

				<?php if ($this->licenseDate): ?>
					<h4 class="vikwppro-lickey-expired">
						<?php
						echo sprintf(
							__('Your license key expired on <span class="word-keep-all">%s</span>', 'vikrestaurants'),
							JHtml::fetch('date', $this->licenseDate, VREFactory::getConfig()->get('dateformat'))
						);
						?>
					</h4>
				<?php endif; ?>

				<h4 class="vikwppro-licensecnt-get">
					<?php _e('Get VikRestaurants PRO and start now.', 'vikrestaurants'); ?>
				</h4>
				<a href="https://vikwp.com/" class="vikwp-btn-link" target="_blank">
					<i class="fas fa-rocket"></i> <?php _e('Upgrade to PRO', 'vikrestaurants'); ?>
				</a>
			</div>
			<span class="icon-background"><i class="fas fa-rocket"></i></span>
		</div>
		
		<div class="col col-md-6 col-sm-12 vikwppro-licenseform">
			<form>
				<div class="vikwppro-licenseform-inner">
					<h4>
						<?php _e('Already have VikRestaurants PRO?', 'vikrestaurants'); ?>
						<br/ >
						<small><?php _e('Enter your licence key here', 'vikrestaurants'); ?></small>
					</h4>
					<span class="vikwppro-inputspan"><i class="fas fa-rocket"></i><input type="text" name="key" id="lickey" value="" class="license-input" autocomplete="off" /></span>
					<button type="button" class="btn btn-primary" id="vikwpvalidate" onclick="vikWpValidateLicenseKey();">
						<?php _e('Validate and Install', 'vikrestaurants'); ?>
					</button>
				</div>
			</form>
		</div>
	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		let GALLERY_DATA = [];

		w.vreOpenGallery = (link) => {
			// get clicked image
			const img = $(link).find('img');

			// open fancybox to show only the items that belong to this menu
			const instance = $.fancybox.open(GALLERY_DATA);

			// get clicked image index
			let index = $('a.vremodal img').index(img);

			if (index > 0) {
				// jump to selected image ('0' turns off the animation)
				instance.jumpTo(index, 0);
			}
		}

		$(function() {
			$('.vikwp-alreadypro a').click((e) => {
				e.preventDefault();
				$('html,body').animate({
					scrollTop: $('.vikwppro-licensecnt').offset().top - 50,
				}, {
					duration: 'fast',
				});
			});

			$('a.vremodal img').each(function() {
				const url = $(this).attr('src');

				GALLERY_DATA.push({
					src: url,
					type: 'image',
					opts: {
						// thumb: url.replace(/\/media\//, '/media@small/'),
						thumb: url,
						caption: $(this).attr('alt'),
					},
				});
			});
		});
	})(jQuery, window);
</script>

<?php
// load common scripts
echo $this->loadTemplate('js');
