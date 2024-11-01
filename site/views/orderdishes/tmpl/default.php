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

JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.fontawesome');
JHtml::fetch('vrehtml.assets.toast', 'bottom-center');

?>

<div class="vre-order-dishes-wrapper">

	<?php
	// display a list of filters to show only one menu per time
	if (count($this->menus) > 1): ?>

		<div class="vre-order-dishes-menu-selection">

			<?php foreach ($this->menus as $menu): ?>

				<div class="vre-order-dishes-menu-picker" data-id="<?php echo (int) $menu->id; ?>">

					<?php if ($menu->image): ?>
						<div class="order-menu-image">
							<?php
							echo JHtml::fetch('vrehtml.media.display', $menu->image, [
								'alt'   => $menu->name,
								'title' => $menu->name,
							]);
							?>
						</div>
					<?php endif; ?>

					<div class="order-menu-title">
						<?php echo $menu->name; ?>
					</div>

				</div>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

	<div class="vre-order-dishes-menus">
		<?php
		foreach ($this->menus as $menu)
		{
			// leave menu hidden in case there is more than one menu available
			?>
			<div class="vre-order-dishes-menu-wrapper" data-id="<?php echo (int) $menu->id; ?>" style="<?php echo count($this->menus) > 1 ? 'display: none;' : ''; ?>">
				<?php
				// assign menu for being used in a sub-template
				$this->foreachMenu = $menu;
				
				// display menu block
				echo $this->loadTemplate('menu');
				?>
			</div>
			<?php
		}
		?>
	</div>

</div>

<?php
// load floating cart 
echo $this->loadTemplate('cart');

// load HTML and scripts to handle the dishes overlay
echo $this->loadTemplate('overlay');

// load payment overlay
echo $this->loadTemplate('payment');
?>

<script>
	(function($) {
		'use strict';

		let SELECTED_MENU_ID     = 0;
		let STOP_SCROLLING_EVENT = false;

		// callback used to find the closest section according
		// to the current scroll position
		const sectionScrollHandle = (elem) => {
			// check if the current scroll top (minus a fixed threshold) is higher
			// than the top offset of the specified sections
			return $(window).scrollTop() + 100 > $(elem).offset().top;
		};

		const vrHighlightSection = (id_section) => {
			const section = $('#vrmenuseclink' + id_section);

			// proceed only in case the section is not highlighted yet
			if (!section.hasClass('vrmenu-sectionlight')) {
				// remove highlight from all sections
				$('.vrmenu-sectionlink').removeClass('vrmenu-sectionlight');
				// highlight selected section only
				section.addClass('vrmenu-sectionlight');

				const bar = $('.vre-order-dishes-menu-wrapper[data-id="' + SELECTED_MENU_ID + '"]')
					.find('.vrmenu-sectionsbar');

				// calculate real offset by ignoring the offset of the wrapper
				let offset = section.offset().left - section.offsetParent().offset().left;

				if (offset < -10 || offset > 10) {
					// animate only if the section is not already displayed within the first 10px
					bar.stop(true, true).animate({
						scrollLeft: bar.scrollLeft() + offset,
					});
				}
			}
		}

		window.vrFadeSection = (id_section) => {
			// highlight section
			vrHighlightSection(id_section);

			let sectionsHeight = 0;

			$('.vrmenu-sectionsbar').each(function() {
				if (sectionsHeight < $(this).outerHeight()) {
					sectionsHeight = $(this).outerHeight();
				}
			});

			// temporarily stop scrolling event
			STOP_SCROLLING_EVENT = true;

			$('html, body').stop(true, true).animate({
				scrollTop: $('#vrmenusection' + id_section).offset().top - (20 + sectionsHeight),
			}).promise().done(() => {
				// restart scrolling event once the animation is completed,
				// so that the sections bar won't face any flashes
				STOP_SCROLLING_EVENT = false;
			});
		}

		$(function() {
			$('.vre-order-dishes-menu-picker').on('click', function() {
				// remove active class
				$('.vre-order-dishes-menu-picker').removeClass('active');
				// hide all menus
				$('.vre-order-dishes-menu-wrapper').hide();
				// get ID of the selected menu
				let id = $(this).addClass('active').data('id');
				// show selected menu block
				const box = $('.vre-order-dishes-menu-wrapper[data-id="' + id + '"]').show();

				SELECTED_MENU_ID = id;

				// auto-scroll to menu
				$('html, body').animate({
					scrollTop: box.offset().top - 30,
				});

				// store select menu in a cookie for the whole session
				document.cookie = 'vre.orderdishes.menu=' + id + '; path=/'; 
			});

			<?php
			$cookie = JFactory::getApplication()->input->cookie;

			// get last selected menu from cookie
			$id_menu = $cookie->getUint('vre_orderdishes_menu');

			if ($id_menu)
			{
				?>
				// auto-click menu
				$('.vre-order-dishes-menu-picker[data-id="<?php echo $id_menu; ?>"]').trigger('click');

				SELECTED_MENU_ID = <?php echo $id_menu; ?>;
				<?php
			}

			if ($this->canOrder)
			{
				?>
				$('.vre-order-dishes-product.clickable').on('click', function() {
					// get ID of the clicked product
					let id = $(this).data('id');

					// show popup to insert the product
					vrOpenDishOverlay(id);
				});
				<?php
			}
			?>

			// register scroll event only once the document is ready
			onDocumentReady().then((data) => {
				// auto-select the closest section while scrolling the page
				$(window).on('scroll', function() {
					if (!SELECTED_MENU_ID || STOP_SCROLLING_EVENT) {
						return false;
					}

					// get sections in reverse ordering to always start from the last one
					const sections = $('.vre-order-dishes-menu-wrapper[data-id="' + SELECTED_MENU_ID + '"]')
						.find('.vre-order-dishes-section.can-highlight')
							.get().reverse();

					if (sections.length == 0) {
						return false;
					}

					let found = false;

					$(sections).each(function() {
						// reverse ordering to always start from the last one
						if (sectionScrollHandle(this)) {
							// highlight new section
							vrHighlightSection($(this).data('id'));

							found = true;

							// abort after finding the closest section
							return false;
						}
					});

					if (!found) {
						// highlight the first section available, because the page
						// scroll is probably lower than the offset of the first section
						vrHighlightSection($(sections.pop()).data('id'));
					}
				});
			});
		});
	})(jQuery);
</script>