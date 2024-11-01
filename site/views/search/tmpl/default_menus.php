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
 * Template file used to display the menus selection.
 *
 * @since 1.8
 */

$currency = VREFactory::getCurrency();

?>

<div class="vrsearchmenucont">

	<div class="vrsearchmenutitle"><?php echo JText::translate('VRSEARCHCHOOSEMENU'); ?></div>

	<div class="vrsearchmenulist">
		<?php
		foreach ($this->menus as $menu)
		{ 	
			if (empty($menu->image) || !is_file(VREMEDIA . DIRECTORY_SEPARATOR . $menu->image))
			{
				// use default image in case it is missing
				$menu->image = 'menu_default_icon.jpg';   
			}
			
			// generate URL to visit menu details
			$url = JRoute::rewrite('index.php?option=com_vikrestaurants&view=menudetails&id=' . $menu->id . ($this->itemid ? '&Itemid=' . $this->itemid : ''));
			?>
			<div class="vrsearchmenudetails">

				<div class="vrsearchmenuinnerdetails">

					<div class="vrsearchmenuimage">
						<a href="<?php echo $url; ?>" target="_blank">
							<?php
							echo JHtml::fetch('vrehtml.media.display', $menu->image, [
								'alt'   => $menu->name,
								'small' => false,
							]);
							?>
						</a>
					</div>

					<?php if ($menu->freechoose): ?>

						<div class="vrsearchmenuname">
							<span class="menu-title"><?php echo $menu->name; ?></span>

							<?php if ($menu->cost > 0): ?>
								<span class="menu-cost"><?php echo $currency->format($menu->cost); ?></span>
							<?php endif; ?>
						</div>

						<div class="vrsearchmenufoot" data-id="<?php echo (int) $menu->id; ?>">
							<span class="vrsearchmenufootleft selected-quantity">0</span>

							<span class="vrsearchmenufootright">
								<a href="javascript: void(0);" class="vrsearchmenuaddlink">
									<i class="fas fa-plus-square"></i>
								</a>

								<a href="javascript: void(0);" class="vrsearchmenudellink vrsearchlinkdisabled">
									<i class="fas fa-minus-square"></i>
								</a>
							</span>
						</div>

					<?php else: ?>

						<div class="vrsearchmenuname">
							<span class="menu-title">
								<label for="menu_radio_sel_<?php echo (int) $menu->id; ?>"><?php echo $menu->name; ?></label>
							</span>

							<span class="menu-radio-sel">
								<input type="radio" name="menus_radio_selection" id="menu_radio_sel_<?php echo (int) $menu->id; ?>" value="<?php echo (int) $menu->id; ?>" />
							</span>
						</div>

						<?php if ($menu->cost > 0): ?>
							<div class="menu-cost-sub"><?php echo $currency->format($menu->cost); ?></div>
						<?php endif; ?>
						
					<?php endif; ?>

				</div>

			</div>
			<?php 
		}
		?>
	</div>

</div>

<div class="vryourmenusdiv">
	<span id="vrbookmenuselsp">
		<?php echo JText::sprintf('VRSEARCHCHOOSEMENUSTATUS', '0/' . $this->args['people']); ?>
	</span>
</div>

<?php
JText::script('VRSEARCHCHOOSEMENUSTATUS');
?>

<script>
	(function($, w) {
		'use strict';

		const getTotalSelectedMenus = () => {
			let total = 0;

			$('#vrconfirmform input[name^="menus["]').each(function() {
				total += parseInt($(this).val());
			});

			return total;
		}
		
		const updateMenuStatus = (id_menu, quantity, selected) => {
			// update menu quantity
			$('.vrsearchmenufoot[data-id="' + id_menu + '"]')
				.find('.selected-quantity')
					.html(quantity);

			// update total quantity
			let text = Joomla.JText._('VRSEARCHCHOOSEMENUSTATUS');
			text = text.replace(/%s/, selected + '/<?php echo $this->args['people']; ?>');

			$('#vrbookmenuselsp').html(text);
		}

		w.validateMenus = () => {
			let selected = getTotalSelectedMenus();
			let total    = <?php echo $this->args['people']; ?>;

			return selected == total;
		}

		$(function() {
			// add menu event
			$('.vrsearchmenuaddlink').on('click', function() {
				let selected = getTotalSelectedMenus();
				let total    = <?php echo $this->args['people']; ?>;

				let id_menu = $(this).closest('.vrsearchmenufoot[data-id]').data('id');

				// add menu only if not reached the total
				if (selected < total) {
					let quantity = 1;

					// check if the menu was already added
					let menu = $('#vrconfirmform input[name="menus[' + id_menu + ']"]');

					if (menu.length) {
						// update existing record
						quantity += parseInt(menu.val());
						menu.val(quantity);
					} else {
						// insert new record
						$('#vrconfirmform').append(
							'<input type="hidden" name="menus[' + id_menu + ']" value="1" />'
						);
					}

					selected++;

					// update texts
					updateMenuStatus(id_menu, quantity, selected);

					// check if we reached the total
					if (selected == total) {
						// mark total count as OK
						$('#vrbookmenuselsp').addClass('vrbookmenuokpeople');
						// disable any ADD button
						$('.vrsearchmenuaddlink').addClass('vrsearchlinkdisabled');
						// scroll to continue button
						$('html,body').animate({
							scrollTop: $('#vre-search-continue-btn').offset().top - 5,
						}, {
							duration: 'slow',
						});
					}

					// enable delete button
					$('.vrsearchmenufoot[data-id="' + id_menu + '"]')
						.find('.vrsearchmenudellink')
							.removeClass('vrsearchlinkdisabled');
				}
			});

			// remove menu event
			$('.vrsearchmenudellink').on('click', function() {
				let selected = getTotalSelectedMenus();
				let id_menu  = $(this).closest('.vrsearchmenufoot[data-id]').data('id');

				// get menu box
				let menu = $('#vrconfirmform input[name="menus[' + id_menu + ']"]');

				// get menu selected quantity
				let quantity = menu.length ? parseInt(menu.val()) : 0;

				if (selected > 0 && quantity > 0) {
					quantity--;

					if (quantity > 0) {
						// update existing record
						menu.val(quantity);
					} else {
						// delete record
						menu.remove();

						// disable remove link
						$(this).addClass('vrsearchlinkdisabled');
					}

					selected--;

					// update texts
					updateMenuStatus(id_menu, quantity, selected);

					// mark total count as NOT OK
					$('#vrbookmenuselsp').removeClass('vrbookmenuokpeople');

					// enable add buttons
					$('.vrsearchmenuaddlink')
						.removeClass('vrsearchlinkdisabled');
				}
			});

			$('input[name="menus_radio_selection"]').on('change', function() {
				let id_menu = parseInt($(this).val());
				let total   = <?php echo $this->args['people']; ?>;

				// remove all the selected menus
				$('#vrconfirmform input[name^="menus["]').remove();
				// add new menu for all guests
				$('#vrconfirmform').append(
					'<input type="hidden" name="menus[' + id_menu + ']" value="' + total + '" />'
				);

				updateMenuStatus(id_menu, total, total);
			});
		});
	})(jQuery, window);
</script>