(function($, w) {
	'use strict';

	/**
	 * The class that will be used to apply the highlighted style
	 * to the inspected elements.
	 * 
	 * @var string
	 */
	const inspectedElementClass = 'customizer-inspected-element';

	/**
	 * Stack containing all the highlighted elements.
	 * 
	 * @var object[]
	 */
	let hoverStack = [];

	/**
	 * Registers the provided element within the stack so that we can easily
	 * apply and restore the "highlighted" style.
	 * Before registering the new element(s), the stack is processed to 
	 * immediately restore the original background of the previous elements. 
	 * 
	 * @param   mixed  node  The element to highlight.
	 * 
	 * @return  void
	 */
	const registerHoveredElement = (node) => {
		processHoverStack();

		let selector = getSelector(node);

		if (VRECustomizer.highlightSharedNodes) {
			// highlight all the nodes that share the same selector
			node = selector;
		}

		$(node).each(function() {
			hoverStack.push({
				node: this,
				background: $(this).css('background-color'),
			});

			// $(this).css('background-color', '#A8C5E599');
			$(this).addClass(inspectedElementClass);
			$(this).attr('data-customizer-selector', selector);

			if (VRECustomizer.highlightSharedNodes) {
				// highlight all shared elements even if they are not hovered
				$(this).addClass('customizer-shared-element');
			}

			if ($(this).offset().top < 30) {
				// element too close to top, place the tooltip in a different position
				$(this).addClass('customizer-tooltip-stick-top');
			}
		});
	}

	/**
	 * Restores the original background of the previously highlighted elements.
	 * 
	 * @return  void
	 */
	const processHoverStack = () => {
		let stack = hoverStack.slice();
		hoverStack = [];

		stack.forEach((prev) => {
			// $(prev.node).css('background-color', prev.background);
			$(prev.node).removeClass(inspectedElementClass);
			$(prev.node).removeClass('customizer-shared-element');
			$(prev.node).removeClass('customizer-force-inspection');
		});
	}

	/**
	 * Extracts a DOM selector from the provided HTML node.
	 * 
	 * @param   mixed   node  The HTML element.
	 * 
	 * @return  string  The selector.
	 */
	const getSelector = (node) => {
		// extract relevant properties from node
		let id  = $(node).attr('id');
		let tag = $(node).prop('tagName').toLowerCase();

		// construct a proper selector
		let selector = tag;

		// include ID only in case it doesn't end with a number, which
		// is commonly used for access purposes only
		if (id && !id.match(/[0-9]+$/)) {
			selector += '#' + id;
		}

		let classes = [];

		$(node)[0].classList.forEach((clazz) => {
			// ignore the classes used by the customizer
			if (clazz.toString().match(/^customizer[_\-]/)) {
				return;
			}

			classes.push(clazz);
		});

		if (classes.length) {
			selector += '.' + classes.join('.');
		}

		return selector
	}

	/**
	 * Fetches the details of the CSS selector from the provided code.
	 * 
	 * @param   string  selector  The selector to look for.
	 * @param   string  css       The full CSS code.
	 * 
	 * @return  object  An object containing the CSS code and the attributes.
	 */
	const fetchSelectorStyle = (selector, css) => {
		if (typeof css !== 'string') {
			// extract CSS code from editor
			const editor = VRECustomizer.editor;

			if (!editor) {
				throw 'CSS editor not found';
			}

			css = editor.getValue() || '';
		}

		// check whether the provided selector is already contained within the provided CSS
		let escapedSelector = selector.replace(/\./g, '\\.');
		let matches = css.match(new RegExp('(?:[^,]\\s*|^)(' + escapedSelector + '\\s*{(?:.*?)})', 's'));

		if (!matches) {
			// selector not found
			return null;
		}

		// remove any comments
		let declaration = matches[1].replace(/\/\*(.*?)\*\//gs, '');

		// extract attributes from match
		const attributesMatch = [...declaration.matchAll(/([a-zA-Z\-]+)\s*:\s*(.*?);/gs)];
		let attributes = {};
		
		// map attributes
		attributesMatch.forEach((m) => {
			attributes[m[1]] = m[2];
		});

		return {
			match: matches[1],
			attributes: attributes,
		};
	}

	/**
	 * Updates the editor by injecting the provided selector and the given attributes.
	 * In case the selector already exists, the provided attributes will be merged 
	 * with the existing ones.
	 * 
	 * @param   string  selector    The selector to inject.
	 * @param   object  attributes  The CSS attributes.
	 * @param   bool    replace     Whether the existing attributes should be replaced.
	 * 
	 * @return  void
	 */
	const registerEditorSelector = (selector, attributes, replace) => {
		// find editor
		const editor = VRECustomizer.editor;

		if (!editor) {
			throw 'CSS editor not found';
		}

		if (typeof attributes !== 'object') {
			attributes = [];
		}

		// create default CSS selector rule
		let code = selector + " {\n  \n}\n";

		// get all the CSS value
		let fullCSS = editor.getValue() || '';

		// obtain style information
		let style = fetchSelectorStyle(selector, fullCSS);

		// in case the selector already exists, remove it and append it at the end
		if (style) {
			fullCSS = fullCSS.replace(style.match, '');

			// merge existing attributes with the provided ones
			attributes = Object.assign(replace ? {} : style.attributes, attributes);

			// make sure we have some attributes
			if (Object.keys(attributes).length) {
				code = selector + " {\n";

				for (let attrName in attributes) {
					code += '  ' + attrName + ': ' + attributes[attrName] + ';\n';
				}

				code += '}\n';
			}
		}

		// avoid to display more than 2 contiguous new lines
		fullCSS = fullCSS.replace(/(\r?\n){2,}/g, "\n\n");
		fullCSS = fullCSS.trim() + "\n";

		// register CSS statement within the editor
		fullCSS += "\n" + code;

		// replace all CSS code
		editor.setValue(fullCSS);

		// always scroll the editor at the end
		if (editor.scrollTo) {
			// Joomla 4 or lower
			editor.scrollTo(0, 999999);
		} else if (editor.instance && editor.instance.scrollDOM) {
			// Joomla 5 or higher
			editor.instance.scrollDOM.scrollTo(0, 999999);
		} else if (editor.element && editor.element.codemirror) {
			// WordPress
			editor.element.codemirror.scrollTo(0, 999999);
		}
	}

	/**
	 * Applies an "highlighted" style to all the HTML elements on mouse over.
	 * 
	 * @see mouseover
	 */
	const inspectorMouseOver = function(event) {
		if (!VRECustomizer.enabled) {
			return true;
		}

		if ($(this).hasClass('customizer-conditional-text-add')) {
			// we hovered the button to add a conditional text, propagate
			// the event to the next element
			return true;
		}

		// apply "highlighted" style
		registerHoveredElement(this);

		event.preventDefault();
		event.stopPropagation();
		return false;
	}

	/**
	 * Removes the "highlighted" style from all the HTML elements on mouse out.
	 * 
	 * @see mouseout
	 */
	const inspectorMouseOut = function(event) {
		if (!VRECustomizer.enabled) {
			return true;
		}

		if ($(this).hasClass('customizer-conditional-text-add')) {
			// we left the button to add a conditional text, propagate
			// the event to the next element
			return true;
		}

		// remove "highlighted" style
		processHoverStack();

		event.preventDefault();
		event.stopPropagation();
		return false;
	}

	/**
	 * Registers a new CSS statement according to the clicked element.
	 * 
	 * @see click
	 */
	const inspectorMouseClick = function(event) {
		if ($(this).hasClass('customizer-conditional-text-add')) {
			// we clicked the button to add a conditional text, therefore we should
			// open the editor to create new conditional texts
			w.parent.openConditionalTextEditor({
				position: $(this).data('position'),
			});

			event.preventDefault();
			event.stopPropagation();
			return false;
		}
		
		if (!VRECustomizer.enabled) {
			return true;
		}

		// extract selector from clicked element
		let selector = getSelector(this);

		// register the style for the resulting selector
		registerEditorSelector(selector);

		// fetch selector style
		let style = fetchSelectorStyle(selector);

		// find editor
		setTimeout(() => {
			w.parent.openCustomizerInspector({
				element: event.target,
				selector: selector,
				attributes: style ? style.attributes : {},
				match: style ? style.match : '',
			});
		}, 256);

		event.preventDefault();
		event.stopPropagation();
		return false;
	}

	$(function() {
		// auto-set root only if not yet specified
		if (!VRECustomizer.root) {
			VRECustomizer.setRoot(document);
		}

		// beware that the keyup is triggered only when the window owns the focus
		$(window).on('keyup', (event) => {
			if (!VRECustomizer.enabled || hoverStack.length == 0) {
				// ignore in case the customizer is currently disabled
				// or if haven't inspected any elements
				return true;
			}

			let element = hoverStack[0].node;
			let tagName = null;

			do {
				switch (event.key) {
					case 'ArrowUp':
						element = $(element).parent();
						break;

					case 'ArrowDown':
						element = $(element).children().first();
						break;

					case 'ArrowLeft':
						element = $(element).prev();
						break;

					case 'ArrowRight':
						element = $(element).next();
						break;

					case 'Enter':
						// inspect element
						$(element).trigger('click');
						return true;

					default:
						// key not observed
						return true;
				}

				// get tag name of the new element
				tagName = ($(element).prop('tagName') || '').toLowerCase();

				// repeat command if we have a node that should not be inspected
			} while (['thead', 'tbody', 'tr'].indexOf(tagName) !== -1);

			event.preventDefault();
			event.stopPropagation();

			if (!element || element.length == 0) {
				// no element found
				return false;
			}

			if ($(element).has(VRECustomizer.root).length || $(element).is(VRECustomizer.root)) {
				// cannot reach or exceed the root
				return false;
			}

			// temporarily deactivate shared nodes to only inspect an element per time
			let supportSharedNodes = VRECustomizer.highlightSharedNodes;
			VRECustomizer.highlightSharedNodes = false;

			// hover the new element
			registerHoveredElement(element);

			// force inspection to apply also those CSS rules that take effect only with :hover
			$(element).addClass('customizer-force-inspection');

			// restore the status previously set for the shared nodes
			VRECustomizer.highlightSharedNodes = supportSharedNodes;

			return false;
		});
	});

	/**
	 * Static helper class used to deal with the customizer.
	 */
	w['VRECustomizer'] = class VRECustomizer {

		/**
		 * Setter to choose whether the customizer inspector is enabled or not.
		 * 
		 * @param   bool  status  True to enable the inspector, false otherwise.
		 * 
		 * @return  void
		 */
		static enable(status) {
			VRECustomizer.enabled = status ? true : false;

			if (VRECustomizer.enabled) {
				// grab the focus when the customizer is enabled
				window.focus();	
			} else {
				// clear highlighted elements when the inspector gets disabled
				processHoverStack();
			}
		}

		/**
		 * Connects the customizer to the provided CMS editor.
		 * 
		 * @param   object  editor  The editor holding the CSS code.
		 * 
		 * @return  void
		 */
		static connectEditor(editor) {
			VRECustomizer.editor = editor;
		}

		/**
		 * Enables the buttons to add new conditional texts.
		 * 
		 * @param   bool  status  True to enable the buttons, false otherwise.
		 * 
		 * @return  void
		 */
		static enableConditionalTexts(status) {
			if (status) {
				$('.customizer-conditional-text-add').addClass('enabled');
			} else {
				$('.customizer-conditional-text-add').removeClass('enabled');
			}
		}

		/**
		 * Changes the root containing all the inspectable elements.
		 * 
		 * @param   string|object  root
		 * 
		 * @return  void
		 */
		static setRoot(root) {
			// if already have a root, don't forget to detach the previously registered events
			if (VRECustomizer.root) {
				$(VRECustomizer.root).off('mouseover', '*', inspectorMouseOver);
				$(VRECustomizer.root).off('mouseout', '*', inspectorMouseOut);
				$(VRECustomizer.root).off('click', '*', inspectorMouseClick);
			}

			// applies an "highlighted" style to all the HTML elements on mouse over
			$(root).on('mouseover', '*', inspectorMouseOver);

			// removes the "highlighted" style from all the HTML elements on mouse out
			$(root).on('mouseout', '*', inspectorMouseOut);

			// registers a new CSS statement according to the clicked element
			$(root).on('click', '*', inspectorMouseClick);

			// update root
			VRECustomizer.root = root;
		}

		/**
		 * @see registerEditorSelector
		 */
		static updateCode(selector, attributes, replace) {
			registerEditorSelector(selector, attributes, replace);
		}

		/**
		 * Tries to extract an hexadecimal color from the provided CSS value.
		 * 
		 * @param   string  value  The CSS value.
		 * 
		 * @return  string|null  The HEX color if available (w/o leading #), null otherwise.
		 */
		static getHexColor(value) {
			if (typeof value !== 'string') {
				return null;
			}

			// take only the hexadecimal value
			let match = value.match(/#([0-9a-fA-F]+)/);

			if (!match) {
				// unable to deal with the provided format (maybe RGB?)
				return null;
			}

			if (match[1].length === 4) {
				// we have a HEX built as #rgba, get rid of the alpha
				return match[1].substr(0, 3);
			} else if (match[1].length === 8) {
				// we have a HEX built as #rrggbbaa, get rid of the alpha
				return match[1].substr(0, 6);
			} 

			// return HEX as is
			return match[1];
		}

		/**
		 * Tries to extract the opacity defined by an hexadecimal color from the provided CSS value.
		 * 
		 * @param   string  value  The CSS value.
		 * 
		 * @return  float   The opacity ([0-1]).
		 */
		static getHexColorOpacity(value) {
			if (typeof value !== 'string') {
				return 1;
			}

			// take only the hexadecimal value
			let match = value.match(/#([0-9a-fA-F]+)/);

			if (!match) {
				// unable to deal with the provided format (maybe RGB?)
				return 1;
			}

			let opacity = 'FF';

			if (match[1].length === 4) {
				// we have a HEX built as #rgba, take only the alpha
				opacity = match[1].substr(3);
				// make sure we have 2 chunks
				opacity += opacity;
			} else if (match[1].length === 8) {
				// we have a HEX built as #rrggbbaa, take only the alpha
				opacity = match[1].substr(6);
			}

			// convert the opacity from HEX to decimal
			opacity = parseInt(opacity, 16);

			// proportionally recalculate the percentage opacity
			// 255 : 100 = opacity : X
			opacity = opacity * 100 / 255;

			// return a floating point between 0 and 1
			return Math.round(opacity) / 100;
		}

		/**
		 * Tries to extract the length/percentage unit from the provided CSS value.
		 * 
		 * @param   string  value    The CSS value.
		 * @param   array   allowed  A list of allowed units (px, em, rem, '%' by default).
		 * 
		 * @return  object|null  An object containing the value and the unit, null otherwise.
		 */
		static extractUnit(value, allowed) {
			if (typeof value !== 'string') {
				return null;
			}

			if (typeof allowed === 'undefined') {
				// use default units
				allowed = ['px', 'em', 'rem', '%'];
			}

			// prepare regex
			let exp = new RegExp('^(\\d+(?:[\\.,]\\d+)?)\\s*(' + allowed.join('|') + ')$');

			// fetch amount and unit
			let match = value.match(exp);

			if (!match) {
				// the value does not include any supported unit
				return null;
			}

			return {
				value: match[1],
				unit:  match[2],
			};
		}

		/**
		 * Checks whether the provided CSS value contains only a numeric string.
		 * 
		 * @param   string  value    The CSS value.
		 * 
		 * @return  bool    True if numeric, false otherwise.
		 */
		static isNumeric(value) {
			if (typeof value !== 'string') {
				return false;
			}

			return value.match(/^\s*(\d+(?:[\.,]\d+)?)\s*$/) ? true : false;
		}

		/**
		 * Cleans the number contained within the provided CSS value.
		 * 
		 * @param   string  value    The CSS value.
		 * 
		 * @return  string  The cleaned number.
		 */
		static cleanNumber(value) {
			return value.toString().replace(/,/, '.').trim();
		}

		/**
		 * Tries to extract the sides of a rectangle from the provided CSS rule.
		 * 
		 * @param   string  value  The CSS value.
		 * 
		 * @return  object  An object containing all the fetched sides (top, left, right, bottom).
		 */
		static extractRectangle(value) {
			let rect = {};

			if (typeof value !== 'string') {
				return rect;
			}

			// extract chunks 
			let chunks = value.split(/\s+/);

			if (chunks.length === 1) {
				// apply to all 4 sides
				rect.top    = chunks[0];
				rect.right  = chunks[0];
				rect.bottom = chunks[0];
				rect.left   = chunks[0];
			} else if (chunks.length === 2) {
				/* top and bottom | left and right */
				rect.top    = chunks[0];
				rect.bottom = chunks[0];
				rect.right  = chunks[1];
				rect.left   = chunks[1];
			}
			else if (chunks.length === 3) {
				/* top | left and right | bottom */
				rect.top    = chunks[0];
				rect.right  = chunks[1];
				rect.left   = chunks[1];
				rect.bottom = chunks[2];
			}
			else if (chunks.length === 4) {
				/* top | right | bottom | left */
				rect.top    = chunks[0];
				rect.right  = chunks[1];
				rect.bottom = chunks[2];
				rect.left   = chunks[3];
			}

			return rect;
		}

		/**
		 * Tries to extract the border details from the provided CSS rule.
		 * 
		 * @param   string  value  The CSS value.
		 * 
		 * @return  object  An object containing the width, the style and the color of the border.
		 */
		static extractBorder(value) {
			let border = {}, match = null;

			if (typeof value !== 'string') {
				return border;
			}

			// fetch border width
			match = value.match(/^(\d+(?:\.\d+)?)([a-z]+)/);
			border.width = match ? match[0] : null;

			// fetch border style
			match = value.match(/\b(solid|dashed|dotted|double)\b/);
			border.style = match ? match[1] : null;

			// fetch border color
			match = value.match(/#([0-9a-fA-F]+)$/);
			border.color = match ? match[1] : null;

			return border;
		}

		/**
		 * Tries to extract the background details from the provided CSS rule.
		 * 
		 * @param   string  value  The CSS value.
		 * 
		 * @return  object  An object containing the background details, null otherwise.
		 */
		static extractBackground(value) {
			let background = {
				type: 'color',
			};

			if (typeof value !== 'string') {
				return background;
			}

			// look for a linear gradient first
			let match = value.match(/^linear-gradient\s*\(\s*([0-9]+)\s*deg\s*,\s*#([0-9a-f]+)\s*,\s*([0-9]+)%\s*,\s*#([0-9a-f]+)\s*\)$/i);

			if (match) {
				// linear gradient found, inject details
				background.type   = 'linear-gradient';
				background.angle  = match[1];
				background.color  = [match[2], match[4]];
				background.offset = match[3];

				return background;
			}

			// now look for a radial gradient
			match = value.match(/^radial-gradient\s*\(\s*#([0-9a-f]+)\s*,\s*([0-9]+)%\s*,\s*#([0-9a-f]+)\s*\)$/i);

			if (match) {
				// radial gradient found, inject details
				background.type   = 'radial-gradient';
				background.color  = [match[1], match[3]];
				background.offset = match[2];
				
				return background;
			}

			// now look for an image
			match = value.match(/^([a-z\s]+)\s*\/\s*(cover|auto)\s+([a-z\-]+)\s+url\(["'\s]*(.*?)["'\s]*\)$/i);

			if (match) {
				// background image found, inject details
				background.type     = 'image';
				background.position = match[1].trim();
				background.size     = match[2];
				background.repeat   = match[3];
				background.url      = match[4].trim();
				
				return background;
			}

			// gradient not found, attempt with a color
			background.color   = VRECustomizer.getHexColor(value);
			background.opacity = VRECustomizer.getHexColorOpacity(value);

			return background;
		}

	}

	/**
	 * Flag used to check whether the customizer is active or not.
	 * The customizer can be used to easily extract a selector from
	 * a clicked HTML element.
	 * 
	 * @var bool
	 */
	VRECustomizer.enabled = false;

	/**
	 * The CMS editor holding the custom CSS code.
	 * 
	 * @var object
	 */
	VRECustomizer.editor = null;

	/**
	 * Flag used to check whether the the customizer should highlight
	 * all the elements sharing the same selector of the clicked node
	 * or not.
	 * 
	 * @var bool
	 */
	VRECustomizer.highlightSharedNodes = true;

	/**
	 * The root element from which the nodes can be inspected.
	 * Any parent node, and the element itself, will be ignored.
	 * 
	 * @var string|object
	 */
	VRECustomizer.root = null;

	// make customizer available also on the parent page
	w.parent['VRECustomizer'] = VRECustomizer;

})(jQuery, window);