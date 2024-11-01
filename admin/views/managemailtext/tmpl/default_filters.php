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

$fltLayout = new JLayoutFile('blocks.card');

?>
            
<div class="vre-cards-container cards-mailtext-filters" id="cards-mailtext-filters">

    <?php
    $i = 0;

    foreach ($this->mailtext->getFilters() as $filter)
    {
        ?>
        <div class="vre-card-fieldset up-to-1" id="mailtext-filter-fieldset-<?php echo (int) $i; ?>">

            <?php
            $displayData = [];

            // fetch card ID
            $displayData['id'] = 'mailtext-filter-card-' . $i;

            // fetch primary text
            $displayData['primary'] = $filter->getName();

            // fetch secondary text
            $displayData['secondary'] = '<small>' . $filter->getSummary() . '</small>';

            // fetch badge
            $displayData['badge'] = '<i class="' . $filter->getIcon() . '"></i>';

            // fetch edit button
            $displayData['edit'] = 'vreOpenMailtextFilterCard(' . $i . ');';

            $data = [
                'id'      => $filter->getID(),
                'options' => $filter->getData(),
            ];

            // render layout
            echo $fltLayout->render($displayData);
            ?>
            
            <input type="hidden" name="filter_json[]" value="<?php echo $this->escape(json_encode($data)); ?>" />

        </div>
        <?php
        $i++;
    }
    ?>

    <!-- ADD PLACEHOLDER -->

    <div class="vre-card-fieldset up-to-1 add add-mailtext-filter">
        <div class="vre-card compress">
            <i class="fas fa-plus"></i>
        </div>
    </div>

</div>

<div style="display:none;" id="mailtext-filter-struct">
    
    <?php
    // create filter structure for new items
    $displayData = array();
    $displayData['id']          = 'mailtext-filter-card-{id}';
    $displayData['primary']     = '';
    $displayData['description'] = '';
    $displayData['badge']       = '<i></i>';
    $displayData['edit']        = true;

    echo $fltLayout->render($displayData);
    ?>

</div>

<?php
JText::script('VRE_MAILTEXT_ADD_FILTER');
JText::script('VRE_MAILTEXT_EDIT_FILTER');
?>

<script>
    (function($, w) {
        'use strict';

        let OPTIONS_COUNT   = <?php echo count($this->mailtext->filters); ?>;
        let SELECTED_OPTION = null;

        $(function() {
            // open inspector for new filters
            $('.vre-card-fieldset.add-mailtext-filter').on('click', () => {
                vreOpenMailtextFilterCard();
            });

            $('#mailtext-filter-inspector').on('inspector.observer.init', (event) => {
                // register a reference to the observer used by the inspector
                w.filterInspectorObserver = event.observer;
            });

            // fill the form before showing the inspector
            $('#mailtext-filter-inspector').on('inspector.show', function() {
                let data = [];

                // fetch JSON data
                if (SELECTED_OPTION) {
                    const fieldset = $('#' + SELECTED_OPTION);

                    data = fieldset.find('input[name="filter_json[]"]').val();

                    try {
                        data = JSON.parse(data);
                    } catch (err) {
                        data = {};
                    }
                }

                $('#mailtext-filter-inspector button[data-role="back"]').hide();

                if (data.id === undefined) {
                    // creating new record, hide delete button
                    $('#mailtext-filter-inspector [data-role="delete"]').hide();
                } else {
                    // editing existing record, show delete button
                    $('#mailtext-filter-inspector [data-role="delete"]').show();
                }

                // fill the form with the retrieved data
                fillMailtextFilterForm(data);
            });

            /**
             * Handle inspector hide.
             *
             * We need to bind the event by using a handler in order to have a lower priority,
             * since the hook used to observe any form changes may be attached after this one.
             */
            $(document).on('inspector.close', '#mailtext-filter-inspector', function() {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {

                    // get all editors
                    let editors = window.conditionalTextFormEditors(window.conditionalTextFilters);

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

            $('#mailtext-filter-inspector').on('inspector.save', function() {
                // get updated filter data
                const data = getMailtextFilterData();

                let fieldset;

                if (SELECTED_OPTION) {
                    fieldset = $('#' + SELECTED_OPTION);
                } else {
                    fieldset = vreAddMailtextFilterCard(data);
                }

                if (fieldset.length == 0) {
                    // an error occurred, abort
                    return false;
                }

                // save JSON data
                fieldset.find('input[name="filter_json[]"]').val(JSON.stringify(data));

                // refresh details shown in card
                vreRefreshMailtextFilterCard(fieldset, data);

                // auto-close on save
                $(this).inspector('dismiss');
            });

            $('#mailtext-filter-inspector').on('inspector.delete', function() {
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
            $('#mailtext-filter-inspector').on('inspector.back', () => {
                if (w.filterInspectorObserver.isChanged()) {
                    // something has changed, warn the user about the
                    // possibility of losing any changes
                    if (!confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'))) {
                        return false;
                    }
                }

                $('#mailtext-filter-inspector button[data-role="back"]').hide();
                fillMailtextFilterForm({});

                // freeze the form to discard any pending changes
                w.filterInspectorObserver.freeze();
            });
        });

        w['vreOpenMailtextFilterCard'] = (index) => {
            let title;

            if (typeof index === 'undefined') {
                title = Joomla.JText._('VRE_MAILTEXT_ADD_FILTER');
                SELECTED_OPTION = null;
            } else {
                title = Joomla.JText._('VRE_MAILTEXT_EDIT_FILTER');
                SELECTED_OPTION = 'mailtext-filter-fieldset-' + index;
            }
            
            // open inspector
            vreOpenInspector('mailtext-filter-inspector', {title: title});
        }

        const vreAddMailtextFilterCard = (data) => {
            let index = OPTIONS_COUNT++;

            SELECTED_OPTION = 'mailtext-filter-fieldset-' + index;

            let html = $('#mailtext-filter-struct').clone().html();
            html = html.replace(/{id}/, index);

            $(
                '<div class="vre-card-fieldset up-to-1" id="' + SELECTED_OPTION + '">' + html + '</div>'
            ).insertBefore('.vre-card-fieldset.add-mailtext-filter');

            // get created fieldset
            const fieldset = $('#' + SELECTED_OPTION);

            fieldset.vrecard('edit', 'vreOpenMailtextFilterCard(' + index + ')');

            // create input to hold JSON data
            const input = $('<input type="hidden" name="filter_json[]" />').val(JSON.stringify(data));

            // append input to fieldset
            fieldset.append(input);

            return fieldset;
        }

        const vreRefreshMailtextFilterCard = (elem, data) => {
            let filter = w.conditionalTextFilters[data.id];

            // update primary text
            elem.vrecard('primary', filter ? filter.name : '/');

            // update secondary text
            elem.vrecard('secondary', '');

            UIAjax.do(
                '<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=mailtext.refreshsummary'); ?>',
                {
                    filter: data.id,
                    options: data.options,
                },
                (summary) => {
                    // refresh secondary text
                    elem.vrecard('secondary', $('<small></small>').html(summary));
                }
            )

            // update badge
            elem.vrecard('badge', '<i class="' + filter.icon + '"></i>');
        }
    })(jQuery, window);
</script>