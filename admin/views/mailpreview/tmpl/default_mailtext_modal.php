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

JHtml::fetch('vrehtml.assets.contextmenu');

?>

<div style="padding: 10px;">

    <!-- NAME - Text -->

    <?php
    echo $this->formFactory->createField()
        ->type('text')
        ->id('mailtext_name')
        ->hiddenLabel(true)
        ->placeholder(JText::translate('VRMANAGELANG2'))
        ->class('input-xxlarge input-large-text')
        ->render(function($data, $input) {
            ?>
            <div class="multi-field">
                <?php echo $input; ?>

                <a href="javascript:void(0)" id="mailtext_config">
                    <i class="fas fa-cog fa-2x"></i>
                </a>
            </div>
            <?php
        });
    ?>

    <!-- BODY - Editor -->

    <?php
    echo $this->formFactory->createField()
        ->type('editor')
        ->name('mailtext_body')
        ->hiddenLabel(true);
    ?>

    <input type="hidden" id="mailtext_position" value="" />
    <input type="hidden" id="mailtext_language" value="" />
    <input type="hidden" id="mailtext_template" value="" />

</div>

<?php
JText::script('VRANYLANG');
JText::script('VRANYTMPL');
?>

<script>
    (function($) {
        'use strict';

        window.openConditionalTextEditor = (data) => {
            // hide customizer filters button
            $('#customizer-filters-btn').hide();

            // obtain observed data
            data = Object.assign(getMailpreviewInspectorData(), data || {});

            // create readable template name
            let templateName = data.group[0].toUpperCase() + data.group.substr(1)
                + ' - ' + data.alias[0].toUpperCase() + data.alias.substr(1);

            // create default name
            let name = templateName + ' - Body (' + data.position.replace(/^custom_position_/, '') + ')';

            // fill default name
            $('#mailtext_name').val(name);

            // clear body value
            Joomla.editors.instances.mailtext_body.setValue('');

            // set position with the specified one
            $('#mailtext_position').val(data.position);

            // set language with the specified one
            $('#mailtext_language').val(data.langtag);

            // set mail template with the specified one
            $('#mailtext_template').val(data.group + '.' + data.alias);

            // display modal
            openModal('conditional-text', '', true);

            // take the last available button and update template text and value
            let buttons = $('#mailtext_config').vikContextMenu('buttons');
            buttons[buttons.length - 1].text     = templateName;
            buttons[buttons.length - 1].template = data.group + '.' + data.alias;
            $('#mailtext_config').vikContextMenu('buttons', buttons);
        }

        const openModal = (id, url, jqmodal) => {
            <?php echo VREApplication::getInstance()->bootOpenModalJS(); ?>
        }

        // CONTEXT MENUS HELPERS

        // language button click implementor
        const handleMailtextLanguageChange = function(root, event) {
            // register selected language
            $('#mailtext_language').val(this.language);
        };

        // check whether the current language button is selected
        const isMailtextLanguageSelected = function(root, event) {
            if (this.language == $('#mailtext_language').val()) {
                return 'fas fa-check';
            }

            return '';
        };

        // template button click implementor
        const handleMailtextTemplateChange = function(root, event) {
            // register selected template
            $('#mailtext_template').val(this.template);
        };

        // check whether the current template button is selected
        const isMailtextTemplateSelected = function(root, event) {
            if (this.template == $('#mailtext_template').val()) {
                return 'fas fa-check';
            }

            return '';
        };

        $(function() {
            const buttons = [];

            //////////////////////////
            ///// LANGUAGE RADIO /////
            //////////////////////////

            buttons.push({
                text: Joomla.JText._('VRANYLANG'),
                action: handleMailtextLanguageChange,
                icon: isMailtextLanguageSelected,
                language: '*',
            });

            <?php foreach (JHtml::fetch('contentlanguage.existing', $all = false, $translate = true) as $lang): ?>
                buttons.push({
                    text: '<?php echo addslashes($lang->text); ?>',
                    action: handleMailtextLanguageChange,
                    icon: isMailtextLanguageSelected,
                    language: '<?php echo $lang->value; ?>',
                });
            <?php endforeach; ?>

            // last button is always a separator
            buttons[buttons.length - 1].separator = true;

            /////////////////////////
            ///// MAIL TEMPLATE /////
            /////////////////////////

            buttons.push({
                text: Joomla.JText._('VRANYTMPL'),
                action: handleMailtextTemplateChange,
                icon: isMailtextTemplateSelected,
                template: '',
            });

            buttons.push({
                text: '/',
                action: handleMailtextTemplateChange,
                icon: isMailtextTemplateSelected,
                template: '/',
            });

            // set up context menu for conditional text filters configuration
            $('#mailtext_config').vikContextMenu({
                clickable: true,
                buttons: buttons,
            });

            ////////////////////////
            ///// SAVE PROCESS /////
            ////////////////////////

            $('#jmodal-conditional-text button[data-role="mailtext.save"]').on('click', function() {
                // prevent multiple requests
                $(this).prop('disabled', true);

                // make request to save the conditional text
                new Promise((resolve, reject) => {
                    UIAjax.do(
                        '<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=mailpreview.savemailtext'); ?>',
                        {
                            name: $('#mailtext_name').val(),
                            body: Joomla.editors.instances.mailtext_body.getValue(),
                            position: $('#mailtext_position').val(),
                            language: $('#mailtext_language').val(),
                            template: $('#mailtext_template').val(),
                        },
                        (data) => {
                            resolve(data);
                        },
                        (err) => {
                            reject(err.responseText);
                        }
                    );
                }).then((data) => {
                    // auto-dismiss modal
                    $('#jmodal-conditional-text').modal('hide');

                    // refresh template preview
                    $('#mailpreview-filters-inspector').trigger('inspector.save');

                    // display successful message
                    VREToast.dispatch(new VREToastMessageOS({
                        title: Joomla.JText._('VRE_UISVG_SAVED'),
                        body: Joomla.JText._('VRE_CUSTOMIZER_SAVE_MESSAGE'),
                        creation: Joomla.JText._('VRJUSTNOW'),
                        icon: 'fas fa-save',
                        status: VREToast.SUCCESS_STATUS,
                        delay: 3500,
                        action: () => {
                            VREToast.dispose(true);
                        },
                    }));
                }).catch((error) => {
                    // an error has occurred
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
                }).finally(() => {
                    // enable button again
                    $(this).prop('disabled', false);
                })
            });

            $('#jmodal-conditional-text').on('hide', () => {
                let editor = Joomla.editors.instances.mailtext_body;

                editor.setValue('');

                if (editor.onSave) {
                    editor.onSave();
                }

                // flag TinyMCE editor as clean because every time we edit
                // something and we close the modal, the editor might
                // prompt an alert saying if we wish to stay or leave
                if (editor.instance && editor.instance.isNotDirty === false) {
                    editor.instance.isNotDirty = true;
                }

                // show customizer filters button again
                $('#customizer-filters-btn').show();
            });
        });
    })(jQuery);
</script>