/*
 * SEARCH BAR - editconfig
 */

function SearchBar(matches) {
	this.setMatches(matches);
}

SearchBar.prototype.setMatches = function(matches) {
	this.matches = matches;
	this.currIndex = 0;
};

SearchBar.prototype.clear = function() {
	this.setMatches(false);
};

SearchBar.prototype.isNull = function() {
	return this.matches === false;
};

SearchBar.prototype.isEmpty = function() {
	return !this.isNull() && this.matches.length == 0;
};

SearchBar.prototype.getElement = function() {
	if (this.matches === false) {
		return null;
	}

	return this.matches[this.currIndex];
};

SearchBar.prototype.getCurrentIndex = function() {
	return this.currIndex;
};

SearchBar.prototype.size = function() {
	if (this.matches === false) {
		return 0;
	}

	return this.matches.length;
};

SearchBar.prototype.next = function() {
	if (this.matches === false) {
		return null;
	}

	this.currIndex++;
	if (this.currIndex >= this.matches.length) {
		this.currIndex = 0;
	}

	return this.matches[this.currIndex];
};

SearchBar.prototype.previous = function() {
	if (this.matches === false) {
		return null;
	}

	this.currIndex--;
	if (this.currIndex < 0) {
		this.currIndex = this.matches.length - 1;
	}

	return this.matches[this.currIndex];
};

/*
 * LEFTBOARD MENU
 */

jQuery(document).ready(function() {

	if (typeof VIKRESTAURANTS_MENU_INIT === 'undefined') {
		// avoid to re-init menu again
		VIKRESTAURANTS_MENU_INIT = true;

		if (isLeftBoardMenuCompressed()) {
			jQuery('.vre-leftboard-menu.compressed .parent .title.selected').removeClass('collapsed');
			jQuery('.vre-leftboard-menu.compressed .parent .wrapper.collapsed').removeClass('collapsed');
		}

		jQuery('#vre-main-menu .parent .title').disableSelection();

		jQuery('#vre-main-menu .parent .title').on('click', function(){
			leftBoardMenuItemClicked(this, 'click');
		});

		jQuery('#vre-main-menu .parent .title').hover(function(){
			if (isLeftBoardMenuCompressed() && !jQuery(this).hasClass('collapsed')) {
				leftBoardMenuItemClicked(this, 'hover');

				jQuery('#vre-main-menu.compressed .parent .title').removeClass('collapsed');
				jQuery(this).addClass('collapsed');
			}

			if (jQuery(this).hasClass('has-href') && jQuery(this).find('.wrapper').length) {
				leftBoardMenuItemClicked(this, 'hover');

				jQuery('#vre-main-menu .parent .title').removeClass('collapsed');
				jQuery(this).addClass('collapsed');
			}
		}, function() {
			if (jQuery(this).hasClass('has-href') && jQuery(this).find('.wrapper').length) {
				leftBoardMenuItemClicked(this, 'out');

				jQuery('#vre-main-menu .parent .title').removeClass('collapsed');
			}
		});
		
		jQuery('.vre-leftboard-menu').hover(function(){
			
		}, function(){
			if (jQuery(window).width() >= 768) {
				jQuery('.vre-leftboard-menu.compressed .parent .title').removeClass('collapsed');
				jQuery('.vre-leftboard-menu.compressed .parent .wrapper').removeClass('collapsed');
			}
		});

		jQuery('.vre-leftboard-menu .custom').hover(function(){
			if (jQuery(window).width() >= 768) {
				jQuery('.vre-leftboard-menu.compressed .parent .title').removeClass('collapsed');
				jQuery('.vre-leftboard-menu.compressed .parent .wrapper').removeClass('collapsed');
			}
		}, function(){

		});

		jQuery('#vre-menu-toggle-phone').on('click', function() {
			jQuery('.vre-leftboard-menu').slideToggle();
		});
	}

});

function leftBoardMenuItemClicked(elem, callee) {
	var wrapper = jQuery(elem).next('.wrapper');

	if (!wrapper.length) {
		// find wrapper within the container
		wrapper = jQuery(elem).find('.wrapper');
	}

	var has = !wrapper.hasClass('collapsed');

	if (has && callee == 'out')
	{
		// do not proceed as we are facing a loading delay,
		// because the 'hover' event wasn't yet ready
		return;
	}

	jQuery('#vre-main-menu .parent .wrapper').removeClass('collapsed');

	jQuery('.vre-angle-dir').removeClass('fa-angle-up');
	jQuery('.vre-angle-dir').addClass('fa-angle-down');
	jQuery('#vre-main-menu .parent .title').removeClass('focus');
	
	if (has) {
		wrapper.addClass('collapsed');
		var angle = jQuery(elem).find('.vre-angle-dir');
		angle.addClass('fa-angle-up');
		angle.removeClass('fa-angle-down');

		wrapper.closest('.title').addClass('focus');
	}
}

function leftBoardMenuToggle() {

	// restore arrows
	jQuery('.vre-angle-dir').removeClass('fa-angle-up');
	jQuery('.vre-angle-dir').addClass('fa-angle-down');
	jQuery('#vre-main-menu .parent .title').removeClass('focus');

	var status;

	if (isLeftBoardMenuCompressed()) {
		jQuery('.vre-leftboard-menu').removeClass('compressed');
		jQuery('.vre-task-wrapper').removeClass('extended');
		status = 1;
	} else {
		jQuery('.vre-leftboard-menu').addClass('compressed');
		jQuery('.vre-task-wrapper').addClass('extended');

		jQuery('.vre-leftboard-menu.compressed .parent .title.selected').removeClass('collapsed');
		jQuery('.vre-leftboard-menu.compressed .parent .wrapper.collapsed').removeClass('collapsed');

		status = 2;
	}

	leftBoardMenuRegisterStatus(status);
	jQuery(window).trigger('resize');

}

function leftBoardMenuRegisterStatus(status) {
	/**
	 * Store the main menu status with the browser cookie,
	 * so that each administrator will be able to use its
	 * preferred layout.
	 *
	 * Keep the main menu status for 1 year.
	 *
	 * @since 1.8
	 */
	var date = new Date();
	date.setYear(date.getFullYear() + 1);

	document.cookie = 'vikrestaurants.mainmenu.status=' + status + '; expires=' + date.toUTCString() + '; path=/';
}

function isLeftBoardMenuCompressed() {
	return jQuery('.vre-leftboard-menu').hasClass('compressed') && jQuery(window).width() >= 768;
}

/*
 * DOCUMENT CONTENT RESIZE
 */

jQuery(document).ready(function() {
	// Statement to quickly disable doc resizing.
	// Do not proceed in case of small devices (< 768).
	if (true && jQuery(window).width() >= 768) {
		var task     = jQuery('.vre-task-wrapper');
		var lfb_menu = jQuery('.vre-leftboard-menu');
		var _margin  = 20;

		jQuery(window).resize(function() {
			var p = (lfb_menu.width() + _margin) * 100 / jQuery(document).width();
			task.css('width', (100 - Math.ceil(p + 1)) + '%');
		});
	}

	jQuery(window).trigger('resize');
});

String.prototype.hashCode = function(){
	var hash = 0;
	if (this.length == 0) return hash;
	for (i = 0; i < this.length; i++) {
		char = this.charCodeAt(i);
		hash = ((hash<<5)-hash)+char;
		hash = hash & hash; // Convert to 32bit integer
	}
	return hash;
}

class VikTimer {
	static debounce(key, func, wait, immediate) {
		if (!VikTimer.debounceLookup) {
			VikTimer.debounceLookup = {};
		}

		return function() {
			var context = this, args = arguments;
			var later = function() {
				VikTimer.debounceLookup[key] = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !VikTimer.debounceLookup[key];
			clearTimeout(VikTimer.debounceLookup[key]);
			VikTimer.debounceLookup[key] = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	}

	static isRunning(key) {
		return VikTimer.debounceLookup && VikTimer.debounceLookup[key];
	}
}

/**
 * Search tools
 */
function vrToggleSearchToolsButton(btn, suffix) {
	if (suffix === undefined) {
		suffix = '';
	} else {
		suffix = '-' + suffix;
	}

	if (jQuery(btn).hasClass('btn-primary')) {
		jQuery('#vr-search-tools' + suffix).slideUp();

		jQuery(btn)
			.removeClass('btn-primary')
			.find('i')
				.removeClass('fa-caret-up')
				.addClass('fa-caret-down');
	} else {
		jQuery('#vr-search-tools' + suffix).slideDown();

		jQuery(btn)
			.addClass('btn-primary')
			.find('i')
				.removeClass('fa-caret-down')
				.addClass('fa-caret-up');
	}
}

jQuery.fn.updateChosen = function(value, active) {
	let el = jQuery(this);

	if (el.prev('.select2-container').length == 0) {
		// chosen
		el.val(value).trigger('chosen:updated').trigger('liszt:updated');

		if (active) {
			el.next().addClass('active');
		} else {
			el.next().removeClass('active');
		}
	} else {
		// chosen not supported, rendered with select2
		el.select2('val', value);
	}

	return el;
};

jQuery.fn.disableChosen = function(disabled) {
	return jQuery(this).prop('disabled', disabled ? true : false)
		.trigger('chosen:updated')
		.trigger('liszt:updated');
};

/**
 * VikRestaurants Card
 */

jQuery.fn.vrecard = function(key, value) {
	if (!key) {
		throw 'Missing key';
	}

	if (value !== undefined) {
		// setter
		switch (key) {
			case 'image':
				const hasImgTag = jQuery(this).find('.vre-card-image img');

				if (value && value.match(/^<img/)) {
					if (hasImgTag.length) {
						hasImgTag.replaceWith(value);
					} else {
						jQuery(this).find('.vre-card-image').html(value);
					}
				} else {
					if (hasImgTag.length) {
						hasImgTag.attr('src', value);
					} else {
						jQuery(this).find('.vre-card-image').html(
							jQuery('<img />').attr('src', value)
						);
					}
				}

				if (value) {
					jQuery(this).find('.vre-card-image').show();
				} else {
					jQuery(this).find('.vre-card-image').hide();
				}
				break;

			case 'badge':
				jQuery(this).find('.card-badge-icon').html(value);
				break;

			case 'primary':
				jQuery(this).find('.card-text-primary').html(value);
				break;

			case 'secondary':
				jQuery(this).find('.card-text-secondary').html(value);
				break;

			case 'edit':
				jQuery(this).find('button.card-edit').attr('onclick', value);
				break;

			default:
				throw 'Unsupported parameter ' + key;
		}
	} else {
		// getter
		switch (key) {
			case 'image':
				return jQuery(this).find('.vre-card-image img').attr('src');

			case 'badge':
				return jQuery(this).find('.card-badge-icon').html();

			case 'primary':
				return jQuery(this).find('.card-text-primary').html();

			case 'secondary':
				return jQuery(this).find('.card-text-secondary').html();

			case 'edit':
				return jQuery(this).find('button.card-edit');

			default:
				throw 'Unsupported parameter ' + key;
		}
	}
}

/**
 * Percentage circle.
 */

jQuery.fn.percentageCircle = function(method, data) {
	if (typeof method !== 'string') {
		// we probably have data as first argument 
		data = typeof method === 'object' ? method : {};
		// auto-create percentage circle
		method = 'create';
	}

	// define internal function to validate progress
	var getProgress = function(progress) {
		// validate progress
		progress = parseInt(progress);
		return isNaN(progress) ? 0 : Math.min(100, Math.abs(progress));
	};

	var animationTimeout = null;
	var _this = this;

	// define function to set and animate progress
	var animateProgress = function() {
		// get current progress
		var progress = getProgress(jQuery(_this).data('tmp'));
		var ceil     = getProgress(jQuery(_this).data('progress'));
		var factor   = progress <= ceil ? 1 : -1;
		var updated  = progress + 1 * factor;

		// fetch animation steps timer
		var timer = parseInt(jQuery(_this).data('timer'));
		timer = isNaN(timer) ? 16 : Math.abs(timer);

		if (timer == 0) {
			// bypass progress animation
			updated = ceil;
		}

		// make sure the progress didn't exceed the maximum/minimum amount
		if (factor == 1) {
			updated = Math.min(updated, ceil);
		} else {
			updated = Math.max(updated, ceil);
		}

		// go to next animation step
		jQuery(_this).removeClass('p' + progress).addClass('p' + updated);
		// update percentage text
		jQuery(_this).find('.amount').text(updated + '%');

		// update progress
		jQuery(_this).data('tmp', updated);

		// re-launch animation recursively, in case it didn't end
		if ((factor == 1 && updated < ceil) || (factor == -1 && updated > ceil)) {
			
			// register timeout
			animationTimeout = setTimeout(animateProgress, timer);
		} else {
			// trigger done event
			jQuery(_this).trigger('done');

			if (ceil == 100) {
				// trigger complete event
				jQuery(_this).trigger('complete');
			}
		}
	};

	if (method.match(/^create$/i)) {
		// create default properties
		data = jQuery.extend({
			progress: 0,
			size:     null,
			color:    null,
			darkMode: false,
			timer:    16,
		}, data);

		// validate progress
		data.progress = getProgress(data.progress);

		// get original classes
		var classes = jQuery(this).attr('class');

		// instantiate only once
		if (!jQuery(this).hasClass('c100')) {
			jQuery(this).addClass('c100 p' + data.progress)
				.data('progress', data.progress)
				.data('tmp', data.progress)
				.data('class', classes)
				.data('size', data.size)
				.data('color', data.color)
				.data('darkMode', data.darkMode)
				.data('timer', data.timer)
				.html('<span class="amount">' + data.progress + '%</span><div class="slice">\n<div class="bar"></div>\n<div class="fill"></div>\n</div>');

			if (data.size) {
				jQuery(this).addClass(data.size);
			}

			if (data.color) {
				jQuery(this).addClass(data.color);
			}

			if (data.darkMode) {
				jQuery(this).addClass('dark');
			}
		}
	} else if (method.match(/^destroy$/i)) {
		// make sure the progress exists
		if (jQuery(this).hasClass('c100')) {
			jQuery(this).html('').attr('class', jQuery(this).data('class'));
		}
	} else if (method.match(/^progress$/i)) {
		// look for getter or setter
		if (typeof data !== 'undefined') {
			// get previous
			var prev = jQuery(this).data('progress');
			// setter
			jQuery(this).data('progress', getProgress(data));

			// make sure something has changed
			if (jQuery(this).data('progress') != prev) {
				// trigger change event
				jQuery(this).trigger('change');
				// animate progress
				animateProgress();
			}
		} else {
			// getter
			return getProgress(jQuery(this).data('progress'));
		}
	} else if (method.match(/^color$/i)) {
		// look for getter or setter
		if (typeof data !== 'undefined') {
			// get previous
			var prev = jQuery(this).data('color');
			// setter
			jQuery(this).data('color', data);
			// refresh color
			jQuery(this).removeClass(prev).addClass(data);
		} else {
			// getter
			var color = jQuery(this).data('color');
			return color ? color : 'blue';
		}
	} else if (method.match(/^size$/i)) {
		// look for getter or setter
		if (typeof data !== 'undefined') {
			// get previous
			var prev = jQuery(this).data('size');
			// setter
			jQuery(this).data('size', data);
			// refresh size
			jQuery(this).removeClass(prev).addClass(data);
		} else {
			// getter
			var size = jQuery(this).data('size');
			return size ? size : 'normal';
		}
	} else if (method.match(/^darkMode$/i)) {
		// look for getter or setter
		if (typeof data !== 'undefined') {
			// setter
			jQuery(this).data('darkMode', data ? true : false);
			// refresh darkMode
			if (data) {
				// enable
				jQuery(this).addClass('dark');
			} else {
				// disable
				jQuery(this).removeClass('dark');
			}
		} else {
			// getter
			return jQuery(this).data('darkMode') ? true : false;
		}
	} else if (method.match(/^timer$/i)) {
		// look for getter or setter
		if (typeof data !== 'undefined') {
			// get previous
			var prev = jQuery(this).data('timer');
			// setter
			jQuery(this).data('timer', data);
			// refresh timer
			jQuery(this).removeClass(prev).addClass(data);
		} else {
			// getter
			var timer = parseInt(jQuery(this).data('timer'));
			return timer && !isNaN(timer) ? timer : 16;
		}
	}

	return jQuery(this);
}
