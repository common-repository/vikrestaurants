(function($, w) {
	'use strict';

	/**
	 * This global variable indicates the TOP PADDING taken by the module.
	 * It is possible to increase this value in case the template uses a sticky menu.
	 *
	 * @var int
	 */
	w.TK_CART_TOP_PADDING = 15;

	/**
	 * This function handles the auto-scroll feature of the cart.
	 * Every time the page is scrolled, the module recalculates its position for being
	 * displayed always on the top of the page.
	 */
	$(function() {

		const cart = $('.vrtkcartfixed');

		if (cart.length === 0) {
			// no available cart elements
			return;
		}
			
		const offset = cart.offset();

		const divstart = cart.siblings('.vrtkcartstart');
		const divlimit = $('.vrtkgotopaydiv');
		
		$(w).scroll(() => {
		
			if ($(w).scrollTop() > offset.top) {
				let toTop = $(w).scrollTop() - offset.top + w.TK_CART_TOP_PADDING;

				if ((divlimit.offset().top - divstart.offset().top) >= (cart.height() + toTop)) {
					cart.stop().animate({
						marginTop: toTop,
					});
				}
			} else {
				cart.stop().animate({
					marginTop: 0,
				});
			}

		});
		
	});

})(jQuery, window);