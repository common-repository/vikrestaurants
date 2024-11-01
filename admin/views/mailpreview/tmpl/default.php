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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('formbehavior.chosen');
JHtml::fetch('vrehtml.assets.toast', 'top-right');

// Obtain media manager modal before displaying the first field.
// In this way, we can display the modal outside the inspector.
$mediaManagerModal = JHtml::fetch('vrehtml.mediamanager.modal');

echo JHtml::fetch(
    'vrehtml.inspector.render',
    'mailpreview-filters-inspector',
    [
        'title'       => JText::translate('VRECONFIGMAILPREVIEW'),
        'placement'   => 'left',
        'closeButton' => true,
        'keyboard'    => false,
        'width'       => 500,
        'footer'      => '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>'
            . '<button type="button" class="btn btn-danger" data-role="restore" style="float:left;display:none;">' . JText::translate('VRMAPGPRESTOREBUTTON') . '</button>',
    ],
    $this->loadTemplate('filters')
);

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

// conditional text modal
echo JHtml::fetch(
    'bootstrap.renderModal',
    'jmodal-conditional-text',
    [
        'title'       => JText::translate('VRMAINTITLENEWMAILTEXT'),
        'backdrop'    => 'static', // does not close the modal when clicked outside
        'closeButton' => true,
        'keyboard'    => false, 
        'bodyHeight'  => 80,
        'modalWidth'  => 80,
        'url'         => '',
        'footer'      => '<button type="button" class="btn btn-success" data-role="mailtext.save">' . JText::translate('JAPPLY') . '</button>',
    ],
    $this->loadTemplate('mailtext_modal')
);

JText::script('VRERROR');
JText::script('VRJUSTNOW');
JText::script('VRE_UISVG_SAVED');
JText::script('VRE_CUSTOMIZER_SAVE_MESSAGE');
JText::script('VRE_CUSTOMIZER_RESTORE_TITLE');
JText::script('VRE_CUSTOMIZER_RESTORE_MESSAGE');
JText::script('VRE_AJAX_GENERIC_ERROR');
JText::script('VRE_CUSTOMIZER_RESTORE_FACTORY_SETTINGS');

?>

<style>
    iframe#customizer-preview {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
    }
    iframe#customizer-preview.expand {
        z-index: 999999;
        top: 10px;
        left: 510px;
        width: calc(100vw - 520px);
        margin: 0;
        height: calc(100vh - 20px);
        border: 0;
    }

    a#customizer-filters-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
    }

    #jmodal-mediamanager {
        z-index: 999999;
    }
    #jmodal-mediamanager .modal-dialog {
        width: 80vw;
        max-width: none;
    }
    #jmodal-mediamanager .modal-body {
        height: calc(100vh - 200px);
        overflow-y: scroll;
    }

    div#inspector-keyboard-legend {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 9999;
        max-width: 400px;
        background: #f1f2f8dd;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 0 8px 0 rgb(57, 51, 51);
        cursor: pointer;
        font-size: 14px;
    }
    div#inspector-keyboard-legend.dismissed {
        display: none !important;
    }
    div#inspector-keyboard-legend ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    @media screen and (max-width : 800px) {
        div#inspector-keyboard-legend {
            display: none !important;
        }
    }
</style>

<iframe id="customizer-preview"></iframe>

<div id="inspector-keyboard-legend" style="display: none;">
    <ul>
        <li>
            <b>
                <i class="fas fa-arrow-up"></i>
                <i class="fas fa-arrow-right"></i>
                <i class="fas fa-arrow-down"></i>
                <i class="fas fa-arrow-left"></i>
            </b> - <?php echo JText::translate('VRE_CUSTOMIZER_INSPECTOR_ARROWS_LEGEND'); ?>
        </li>
        <li>
            <b>&#9166;</b> - <?php echo JText::translate('VRE_CUSTOMIZER_INSPECTOR_ENTER_LEGEND'); ?>
        </li>
    </ul>
</div>

<a href="javascript:void(0)" id="customizer-filters-btn">
    <i class="fas fa-sliders-h fa-3x"></i>
</a>

<?php
echo $mediaManagerModal;
?>

<script>
    (function($) {
        'use strict';

        let filters = <?php echo json_encode($this->filters); ?>;

        const expandCustomizerPreview = (expand) => {
            if (expand) {
                $('iframe#customizer-preview').addClass('expand');
            } else {
                $('iframe#customizer-preview').removeClass('expand');
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

                // permanently hide the keyboards legend during the first inspection
                $('#inspector-keyboard-legend').trigger('click');

                // open the customizer inspector
                vreOpenInspector('customizer-addcss-inspector');
            }, 128);
        }

        window.getCustomizerPreview = () => {
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

        window.applyCustomizerPreviewCSS = (css) => {
            getCustomizerPreview().then((preview) => {
                // delete any CSS file that may alter the preview of the e-mail
                $(preview).find('head link[href*=".css"]')
                    .not('link[href*="customizer.css"]')
                    .remove();

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
                    css = getMailpreviewInspectorData().css || '';
                }

                inlineBlock.html(css);
            });
        }

        const saveCssChanges = (data) => {
            return new Promise((resolve, reject) => {
                UIAjax.do(
                    '<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=mailpreview.savecss'); ?>',
                    {
                        group: data.group,
                        alias: data.alias,
                        css:   data.css,
                    },
                    (response) => {
                        if (response == 1) {
                            resolve();
                        } else {
                            // probably the session as expired and the request made a redirect to the login panel
                            reject();
                        }
                    },
                    (error) => {
                        reject(error.responseText);
                    }
                );
            }).then(() => {
                VREToast.dispatch(new VREToastMessageOS({
                    title: data.css ? Joomla.JText._('VRE_UISVG_SAVED') : Joomla.JText._('VRE_CUSTOMIZER_RESTORE_TITLE'),
                    body: data.css ? Joomla.JText._('VRE_CUSTOMIZER_SAVE_MESSAGE') : Joomla.JText._('VRE_CUSTOMIZER_RESTORE_MESSAGE'),
                    creation: Joomla.JText._('VRJUSTNOW'),
                    icon: 'fas fa-' + (data.css ? 'save' : 'undo-alt'),
                    status: VREToast.SUCCESS_STATUS,
                    delay: 3500,
                    action: () => {
                        VREToast.dispose(true);
                    },
                }));
            }).catch((error) => {
                VREToast.dispatch(new VREToastMessageOS({
                    title: Joomla.JText._('VRERROR'),
                    body: error || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'),
                    creation: Joomla.JText._('VRJUSTNOW'),
                    icon: 'fas fa-times-circle',
                    status: VREToast.ERROR_STATUS,
                    action: () => {
                        VREToast.dispose(true);
                    },
                }));
            });
        }

        $(function() {
            $('#customizer-filters-btn').on('click', () => {
                vreOpenInspector('mailpreview-filters-inspector');
            });

            $('#mailpreview-filters-inspector').on('inspector.show', () => {
                $('#customizer-filters-btn').hide();

                fillMailpreviewInspectorForm(filters);

                if (Joomla.editors.instances.mailpreview_filter_customizer_css.getValue().trim()) {
                    $('#mailpreview-filters-inspector button[data-role="restore"]').show();
                } else {
                    $('#mailpreview-filters-inspector button[data-role="restore"]').hide();
                }
            });

            $('#mailpreview-filters-inspector').on('inspector.close', () => {
                $('#customizer-filters-btn').show();
            });

            $('#mailpreview-filters-inspector').on('inspector.save', function() {
                filters = getMailpreviewInspectorData();

                const patternURL = '<?php echo $this->patternURL; ?>';

                // construct new preview URL
                const src = patternURL.replace(/%s/, filters.group)
                    .replace(/%s/, filters.alias)
                    .replace(/%s/, filters.file)
                    .replace(/%s/, filters.langtag);

                $('#customizer-preview').attr('src', src);

                if (filters.isChangedCSS) {
                    // commit changes
                    saveCssChanges(filters);
                }

                $(this).inspector('dismiss');
            });

            $('#mailpreview-filters-inspector').on('inspector.restore', function() {
                let r = confirm(Joomla.JText._('VRE_CUSTOMIZER_RESTORE_FACTORY_SETTINGS'));

                if (!r) {
                    return false;
                }

                // clear CSS value
                Joomla.editors.instances.mailpreview_filter_customizer_css.setValue('');

                // save changes
                $('#mailpreview-filters-inspector button[data-role="save"]').trigger('click');
            });

            $('#customizer-preview').on('load', function() {
                // fetch title (mail subject) from customizer
                let title = $(this).contents()[0].title;
                // update title of the browser
                document.title = title;

                // toggle customizer inspector status
                VRECustomizer.enable(filters.customizer);
                VRECustomizer.enableConditionalTexts(filters.mailtext);

                if (filters.customizer) {
                    $('#inspector-keyboard-legend').show();
                }

                // inspect only the elements contained within the "body"
                VRECustomizer.setRoot('body');

                // wait until the editor is ready
                onInstanceReady(() => {
                    return Joomla.editors.instances.mailpreview_filter_customizer_css;
                }).then((editor) => {
                    // always reconnect the customizer to the CSS editor
                    VRECustomizer.connectEditor(editor);

                    // refresh changes
                    applyCustomizerPreviewCSS();
                });
            });

            // load the URL only after registering the load event, otherwise the contents
            // might be already loaded before having registered yet the callback 
            $('#customizer-preview').attr('src', '<?php echo $this->url; ?>');

            // dismiss keyboard legend when clicked
            $('#inspector-keyboard-legend').on('click', function() {
                $(this).addClass('dismissed');
            });

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

                // commit changes
                saveCssChanges(getMailpreviewInspectorData());
            });

            $('#customizer-addcss-inspector').on('inspector.dismiss', function() {
                // auto-dismiss inspector
                $(this).inspector('close');
            });
        });
    })(jQuery);
</script>