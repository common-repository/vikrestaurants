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

$currency = VREFactory::getCurrency();

$actLayout = new JLayoutFile('blocks.card');

?>
            
<div class="vre-cards-container cards-mailtext-actions" id="cards-mailtext-actions">

    <?php
    $i = 0;

    foreach ($this->mailtext->getActions() as $actions)
    {
        foreach ($actions as $action)
        {
            ?>
            <div class="vre-card-fieldset up-to-2" id="mailtext-action-fieldset-<?php echo (int) $i; ?>">

                <?php
                $displayData = [];

                // fetch card ID
                $displayData['id'] = 'mailtext-action-card-' . $i;

                // fetch primary text
                $displayData['primary'] = $action->getName();

                // fetch secondary text
                $displayData['secondary'] = '<small>' . $action->getSummary() . '</small>';

                // fetch card class
                $displayData['class'] = 'published';

                // fetch badge
                $displayData['badge'] = '<i class="' . $action->getIcon() . '"></i>';

                // fetch edit button
                $displayData['edit'] = 'vreOpenMailtextActionCard(' . $i . ');';

                $data = [
                    'id'      => $action->getID(),
                    'options' => $action->getData(),
                ];

                // render layout
                echo $actLayout->render($displayData);
                ?>
                
                <input type="hidden" name="action_json[]" value="<?php echo $this->escape(json_encode($data)); ?>" />

            </div>
            <?php
            $i++;
        }
    }
    ?>

    <!-- ADD PLACEHOLDER -->

    <div class="vre-card-fieldset up-to-2 add add-mailtext-action">
        <div class="vre-card compress">
            <i class="fas fa-plus"></i>
        </div>
    </div>

</div>

<div style="display:none;" id="mailtext-action-struct">
    
    <?php
    // create action structure for new items
    $displayData = array();
    $displayData['id']      = 'mailtext-action-card-{id}';
    $displayData['primary'] = '';
    $displayData['badge']   = '<i></i>';
    $displayData['edit']    = true;

    echo $actLayout->render($displayData);
    ?>

</div>

<?php
JText::script('VRE_MAILTEXT_ADD_ACTION');
JText::script('VRE_MAILTEXT_EDIT_ACTION');
?>

<script>
    (function($, w) {
        'use strict';

        let OPTIONS_COUNT   = <?php echo count($this->mailtext->actions); ?>;
        let SELECTED_OPTION = null;

        $(function() {
            // open inspector for new actions
            $('.vre-card-fieldset.add-mailtext-action').on('click', () => {
                vreOpenMailtextActionCard();
            });

            $('#mailtext-action-inspector').on('inspector.observer.init', (event) => {
                // register a reference to the observer used by the inspector
                w.actionInspectorObserver = event.observer;
            });

            // fill the form before showing the inspector
            $('#mailtext-action-inspector').on('inspector.show', function() {
                let data = [];

                // fetch JSON data
                if (SELECTED_OPTION) {
                    const fieldset = $('#' + SELECTED_OPTION);

                    data = fieldset.find('input[name="action_json[]"]').val();

                    try {
                        data = JSON.parse(data);
                    } catch (err) {
                        data = {};
                    }
                }

                $('#mailtext-action-inspector button[data-role="back"]').hide();

                if (data.id === undefined) {
                    // creating new record, hide delete button
                    $('#mailtext-action-inspector [data-role="delete"]').hide();
                } else {
                    // editing existing record, show delete button
                    $('#mailtext-action-inspector [data-role="delete"]').show();
                }

                // fill the form with the retrieved data
                fillMailtextActionForm(data);
            });

            /**
             * Handle inspector hide.
             *
             * We need to bind the event by using a handler in order to have a lower priority,
             * since the hook used to observe any form changes may be attached after this one.
             */
            $(document).on('inspector.close', '#mailtext-action-inspector', function() {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {

                    // get all editors
                    let editors = window.conditionalTextFormEditors(window.conditionalTextActions);

                    // reset all editors on inspector close
                    editors.forEach((editor) => {
                        editor.setValue('');

                        if (editor.onSave) {
                            editor.onSave();
                        }

                        // flag TinyMCE editor as clean because every time we edit
                        // something and we close the inspector, the editor might
                        // prompt an alert saying if we wish to stay or leave
                        if (editor.instance && editor.instance.isNotDirty === false) {
                            editor.instance.isNotDirty = true;
                        }
                    });
                }
            });

            $('#mailtext-action-inspector').on('inspector.save', function() {
                // get updated action data
                const data = getMailtextActionData();

                let fieldset;

                if (SELECTED_OPTION) {
                    fieldset = $('#' + SELECTED_OPTION);
                } else {
                    fieldset = vreAddMailtextActionCard(data);
                }

                if (fieldset.length == 0) {
                    // an error occurred, abort
                    return false;
                }

                // save JSON data
                fieldset.find('input[name="action_json[]"]').val(JSON.stringify(data));

                // refresh details shown in card
                vreRefreshMailtextActionCard(fieldset, data);

                // auto-close on save
                $(this).inspector('dismiss');
            });

            $('#mailtext-action-inspector').on('inspector.delete', function() {
                const fieldset = $('#' + SELECTED_OPTION);

                if (fieldset.length == 0) {
                    // record not found
                    return false;
                }

                // auto delete fieldset
                fieldset.remove();

                // auto-close on delete
                $(this).inspector('dismiss');
            });

            // go to the previous page when clicking the back button
            $('#mailtext-action-inspector').on('inspector.back', () => {
                if (w.actionInspectorObserver.isChanged()) {
                    // something has changed, warn the user about the
                    // possibility of losing any changes
                    if (!confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'))) {
                        return false;
                    }
                }

                $('#mailtext-action-inspector button[data-role="back"]').hide();
                fillMailtextActionForm({});

                // freeze the form to discard any pending changes
                w.actionInspectorObserver.freeze();
            });
        });

        w['vreOpenMailtextActionCard'] = (index) => {
            let title;

            if (typeof index === 'undefined') {
                title = Joomla.JText._('VRE_MAILTEXT_ADD_ACTION');
                SELECTED_OPTION = null;
            } else {
                title = Joomla.JText._('VRE_MAILTEXT_EDIT_ACTION');
                SELECTED_OPTION = 'mailtext-action-fieldset-' + index;
            }
            
            // open inspector
            vreOpenInspector('mailtext-action-inspector', {title: title});
        }

        const vreAddMailtextActionCard = (data) => {
            let index = OPTIONS_COUNT++;

            SELECTED_OPTION = 'mailtext-action-fieldset-' + index;

            let html = $('#mailtext-action-struct').clone().html();
            html = html.replace(/{id}/, index);

            $(
                '<div class="vre-card-fieldset up-to-2" id="' + SELECTED_OPTION + '">' + html + '</div>'
            ).insertBefore('.vre-card-fieldset.add-mailtext-action');

            // get created fieldset
            const fieldset = $('#' + SELECTED_OPTION);

            fieldset.vrecard('edit', 'vreOpenMailtextActionCard(' + index + ')');

            // create input to hold JSON data
            const input = $('<input type="hidden" name="action_json[]" />').val(JSON.stringify(data));

            // append input to fieldset
            fieldset.append(input);

            return fieldset;
        }

        const vreRefreshMailtextActionCard = (elem, data) => {
            let action = w.conditionalTextActions[data.id];

            // update primary text
            elem.vrecard('primary', action ? action.name : '/');

            // update secondary text
            elem.vrecard('secondary', '');

            UIAjax.do(
                '<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=mailtext.refreshsummary'); ?>',
                {
                    action: data.id,
                    options: data.options,
                },
                (summary) => {
                    // refresh secondary text
                    elem.vrecard('secondary', $('<small></small>').html(summary));
                }
            )

            // update badge
            elem.vrecard('badge', '<i class="' + action.icon + '"></i>');

            // add published status
            elem.find('.vre-card').addClass('published');
        }
    })(jQuery, window);
</script>