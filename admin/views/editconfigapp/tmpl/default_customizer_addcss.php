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

// Obtain media manager modal before displaying the first field.
// In this way, we can display the modal outside the inspector.
$mediaManagerModal = JHtml::fetch('vrehtml.mediamanager.modal');

// inspector used to manage the appearance of the inspected elements
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'customizer-addcss-inspector',
	[
		'title'       => 'CSS Inspector',
		'placement'   => 'left',
		'closeButton' => true,
		'keyboard'    => false,
		'width'       => 500,
		'footer'      => '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>'
			. '<button type="button" class="btn btn-secondary" data-role="dismiss" style="float:left;">' . JText::translate('JTOOLBAR_CLOSE') . '</button>',
	],
	JLayoutHelper::render('configuration.customizer.inspector', ['formFactory' => $this->formFactory])
);

?>

<div class="config-fieldset">

	<div class="config-fieldset-body vertical">
		<?php
		// display CSS code editor
		echo $this->formFactory->createField()
			->type('editor')
			->name('custom_css_code')
			->value($this->customizerModel->getCustomCSS())
			->editor('codemirror')
			->hidden(true)
			->buttons(false)
			->params(['syntax' => 'css']);
		?>

		<?php
		// toggle customizer inspector
		echo $this->formFactory->createField()
			->type('checkbox')
			->id('enable_customizer_inspector')
			->checked(false)
			->label(JText::translate('VRE_CUSTOMIZER_ENABLE_INSPECTOR'))
			->description(JText::translate('VRE_CUSTOMIZER_ENABLE_INSPECTOR_DESC'))
			->onchange('toggleCustomizerInspector(this)')
			->control([
				'class'   => 'preview-related-field',
				'visible' => $this->filters['preview_status'],
			]);
		?>
	</div>

</div>

<?php
echo $mediaManagerModal;

JText::script('VRERROR');
JText::script('VRE_CUSTOMIZER_ENABLE_INSPECTOR_WARN');
JText::script('VRJUSTNOW');
?>

<script>
	(function($) {
		'use strict';

		let editor;

		window.toggleCustomizerInspector = (checkbox) => {
			if (typeof VRECustomizer !== 'undefined') {
				VRECustomizer.enable(checkbox.checked);
			} else if (checkbox.checked) {
				// display an error
				VREToast.dispatch(new VREToastMessageOS({
                    title: Joomla.JText._('VRERROR'),
                    body: Joomla.JText._('VRE_CUSTOMIZER_ENABLE_INSPECTOR_WARN'),
                    creation: Joomla.JText._('VRJUSTNOW'),
                    icon: 'fas fa-times-circle',
                    status: VREToast.ERROR_STATUS,
                    delay: false,
                    action: () => {
                        VREToast.dispose(true);

                        // check whether the customizer is now available because the
                        // user might have changed page before disposing the alert
                        if (typeof VRECustomizer !== 'undefined') {
                        	// customizer available, we don't need to uncheck the input
                        	// and scroll to the dropdown element
                        	return;
                        }

                        // auto-uncheck the field after disposing the toast message
						checkbox.checked = false;

						// animate to the dropdown position
						$('html, body').animate({
							scrollTop: $('#customizer-action-page').offset().top - 200,
						}, () => {
							setTimeout(() => {
								// give a sort of "focus" effect to the dropdown 256 milliseconds
								// after completing the scroll animation
								$('#customizer-action-page').css('outline', '2px solid #1888be');
							}, 256);
						});
                    },
                }));
			}
		}

		window.openCustomizerInspector = (style) => {
			// disable customizer inspector to avoid leaving the "highlighted" effect
			// on the clicked element
			VRECustomizer.enable(false);

			// wait until the inspector has been properly disabled, otherwise the 
			// customizer might use the "highlight" background as inherited value
			setTimeout(() => {
				// set up inspector form
				fillCustomizerInspectorForm(style);

				// open the customizer inspector
				vreOpenInspector('customizer-addcss-inspector');
			}, 128);
		}

		$(function() {

			/////////////////////
			///// INSPECTOR /////
			/////////////////////

			let shouldRestoreChanges = false;

			$('#customizer-addcss-inspector').on('inspector.aftershow', () => {
				// make the customizer preview full screen
				expandCustomizerPreview(true);

				shouldRestoreChanges = true;
			});

			$('#customizer-addcss-inspector').on('inspector.close', (event) => {
				if (event.isPropagationStopped()) {
					return false;
				}

				if (shouldRestoreChanges) {
					// fetch inspector data
					let data = getCustomizerInspectorData();
					
					// restore changes
					VRECustomizer.updateCode(data.selector, {});

					// refresh preview
					applyCustomizerPreviewCSS();
				}

				// restore customizer inspector
				VRECustomizer.enable(true);

				// disable full screen from customizer preview
				expandCustomizerPreview(false);
			});

			$('#customizer-addcss-inspector').on('inspector.save', function() {
				shouldRestoreChanges = false;

				// fetch inspector data
				let data = getCustomizerInspectorData();

				// commit changes (true to replace all the existing rules)
				VRECustomizer.updateCode(data.selector, data.attributes, true);

				// refresh changes
				applyCustomizerPreviewCSS();

				// auto-dismiss inspector
				$(this).inspector('close');
			});

			$('#customizer-addcss-inspector').on('inspector.dismiss', function() {
				// auto-dismiss inspector
				$(this).inspector('close');
			});

			onInstanceReady(() => {
				return window.configObserver;
			}).then(() => {
				window.configObserver.exclude('#enable_customizer_inspector');
			});

			//////////////////
			///// EDITOR /////
			//////////////////

			onInstanceReady(() => {
				// wait until the editor is accessible
				return Joomla.editors.instances.custom_css_code;
			}).then((e) => {
				// register internal property
				editor = e;

				// check if we have a code mirror
				if (editor.element && editor.element.codemirror) {
					editor = editor.element.codemirror;
				}

				if (editor.on) {
					editor.on('keyup', VikTimer.debounce('customizer-preview-custom-css', applyCustomizerPreviewCSS, 1000));
				}

				$('li[data-id="vre_customizer_tab_additionalcss"]').on('click', function() {
					changeCustomizerPreviewPage();

					setTimeout(() => {
						if (editor.refresh) {
							editor.refresh();
						}
					}, 256);
				});
			});

		});
	})(jQuery);
</script>