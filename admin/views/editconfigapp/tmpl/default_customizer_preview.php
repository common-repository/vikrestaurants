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

$previewPage = new JUri($this->filters['preview_page']);
$previewPage->setVar('vikrestaurants_customizer', 1);

?>

<style>
	iframe#customizer-preview {
		min-height: 600px;
		width: 60%;
		margin-left: 15px;
		border: 1px solid #ddd;

		transition: all 400ms ease-in-out 0s;
		-moz-transition: all 400ms ease-in-out 0s;
		-webkit-transition: all 400ms ease-in-out 0s;
		-o-transition: all 400ms ease-in-out 0s;
	}
	iframe#customizer-preview.expand {
		z-index: 999999;
		position: fixed;
		top: 10px;
		left: 510px;
		width: calc(100vw - 520px);
		margin: 0;
		height: calc(100vh - 20px);
		border: 0;
	}
	iframe#customizer-preview.__hide {
		width: 0 !important;
		border: 0 !important;
		margin-left: 0 !important;
	}
	#vretabview3 .config-panel-tabview {
		display: flex;
		flex-wrap: wrap;
	}
	#vretabview3 .config-panel-tabview-inner {
		flex: 1;
	}
	.config-panel-tabview-inner[data-id="vre_customizer_tab_additionalcss"] #jmodal-mediamanager {
		z-index: 9999999 !important;
	}

	@media screen and (max-width: 938px) {
		iframe#customizer-preview {
			width: 100%;
			margin: 15px 0 0 0;
		}
		iframe#customizer-preview.__hide {
			display: none;
		}
	}
</style>

<iframe id="customizer-preview" src="<?php echo (string) $previewPage; ?>" class="hidden-phone<?php echo $this->filters['preview_status'] ? '' : ' __hide'; ?>"></iframe>

<script>
	(function($) {
		'use strict';

		window['getCustomizerPreview'] = () => {
			return onInstanceReady(() => {
				// load iframe
				const iframe = $('iframe#customizer-preview');

				if (!iframe) {
					// iframe not yet ready
					return false;
				}

				// load iframe contents
				const iframeContents = iframe.contents();

				if (!iframeContents) {
					// cannot access iframe
					return false;
				}

				// access the root of the iframe preview
				return iframeContents[0].querySelector(':root');
			});
		}

		window['applyCustomizerPreviewCSS'] = (css) => {
			getCustomizerPreview().then((preview) => {
				// delete existing custom CSS file
				$(preview).find('head link[href*="assets/css/vre-custom.css"]').remove();

				// get existing inline block
				let inlineBlock = $(preview).find('head style#vre-customizer-inline-css');

				if (!inlineBlock.length) {
					// create style block
					inlineBlock = $('<style id="vre-customizer-inline-css"></style>');
					// append block into the head
					$(preview).find('head').append(inlineBlock);
				}

				// update CSS code
				if (typeof css !== 'string') {
					inlineBlock.html(Joomla.editors.instances.custom_css_code.getValue());
				} else {
					inlineBlock.html(css);
				}
			});
		}

		window['refreshCustomizerEnvironmentVars'] = () => {
			getCustomizerPreview().then((preview) => {
				$('[name^="customizer["]').each(function() {
					injectEnvironmentVar(this, preview);
				});
			});
		}

		window['toggleCustomizerPreview'] = () => {
			const iframe = $('iframe#customizer-preview');

			let status = 1;

			if (iframe.hasClass('__hide')) {
				iframe.removeClass('__hide');
				$('.preview-related-field').show();
			} else {
				iframe.addClass('__hide');
				$('.preview-related-field').hide();
				status = 0;
			}

			return status;
		}

		window['changeCustomizerPreviewPage'] = (url, script) => {
			if (!url) {
				url    = '<?php echo $this->filters['preview_page']; ?>';
				script = true;
			}

			if (script) {
				// include customizer script
				url += (!url.match(/\?/) ? '?' : '&') + 'vikrestaurants_customizer=1';
			}

			$('iframe#customizer-preview').attr('src', url);
		}

		window['expandCustomizerPreview'] = (expand) => {
			if (expand) {
				$('iframe#customizer-preview').addClass('expand');
			} else {
				$('iframe#customizer-preview').removeClass('expand');
			}
		}

		const injectEnvironmentVar = (field, preview) => {
			// extract CSS var from name
			const match = $(field).attr('name').match(/^customizer\[(.*?)\]$/);
			const key   = match[1];

			// extract value from input
			let value = $(field).val();

			if ($(field).hasClass('color')) {
				// sanitize HEX color
				value = '#' + value.replace(/^#/, '');
			} else if ($(field).hasClass('number')) {
				// include pixel
				value = parseFloat(value) + 'px';
			}

			// overwrite CSS var
			preview.style.setProperty(key, value);
		}

		$(function() {
			$('[name^="customizer["]').on('change', function() {
				getCustomizerPreview().then((preview) => {
					injectEnvironmentVar(this, preview);
				});
			});

			// refresh custom contents every time the preview page changes
			$('iframe#customizer-preview').on('load', () => {
				// refresh custom CSS
				applyCustomizerPreviewCSS();
				// refresh environment vars
				refreshCustomizerEnvironmentVars();

				// wait until the editor is ready
                onInstanceReady(() => {
                	if (typeof VRECustomizer === 'undefined') {
                		// customizer not yet ready
                		return false;
                	}

                    return Joomla.editors.instances.custom_css_code;
                }).then((editor) => {
                    // always reconnect the customizer to the CSS editor
                    VRECustomizer.connectEditor(editor);

                    // toggle customizer according to the current settings
					if ($('#enable_customizer_inspector').is(':checked')) {
						VRECustomizer.enable(true);
					}
                });
			});
		});
	})(jQuery);
</script>