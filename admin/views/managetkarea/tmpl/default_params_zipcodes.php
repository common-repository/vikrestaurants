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

$itemLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-cards-container cards-tkarea-zipcodes" id="cards-tkarea-zipcodes">

    <?php
    if ($this->area->type === 'zipcodes')
    {
        foreach ($this->area->content as $i => $zip)
        {
            ?>
            <div class="vre-card-fieldset up-to-3" id="tkarea-zipcodes-fieldset-<?php echo (int) $i; ?>">

                <?php
                $displayData = array();

                // fetch card ID
                $displayData['id'] = 'tkarea-zipcodes-card-' . $i;

                // fetch card class
                if (!empty($zip->published))
                {
                    $displayData['class'] = 'published';
                }

                $range = [
                    $zip->from ?? null,
                    $zip->to   ?? null,
                ];

                // fetch primary text
                $displayData['primary'] = implode(' - ', array_filter($range));

                // fetch badge
                $displayData['badge'] = '<i class="fas fa-' . (!empty($zip->published) ? 'check-circle' : 'dot-circle') . '"></i>';

                // fetch edit button
                $displayData['edit'] = 'vreOpenTkareaZIPCodeCard(' . $i . ');';

                // render layout
                echo $itemLayout->render($displayData);
                ?>
                
                <input type="hidden" name="content[zipcodes][]" value="<?php echo $this->escape(json_encode($zip)); ?>" />

            </div>
            <?php
        }
    }
    ?>

    <div class="vre-card-fieldset up-to-3 add add-tkarea-zipcodes">
        <div class="vre-card compress">
            <i class="fas fa-plus"></i>
        </div>
    </div>

</div>

<div style="display:none;" id="tkarea-zipcodes-struct">
    
    <?php
    // create structure for new items
    $displayData = array();
    $displayData['id']        = 'tkarea-zipcodes-card-{id}';
    $displayData['primary']   = '';
    $displayData['secondary'] = '';
    $displayData['badge']     = '<i class="fas fa-check-circle"></i>';
    $displayData['edit']      = true;

    echo $itemLayout->render($displayData);
    ?>

</div>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage delivery area zip codes
echo JHtml::fetch(
    'vrehtml.inspector.render',
    'tkarea-zipcodes-inspector',
    array(
        'title'       => JText::translate('VRTKAREAZIPADD'),
        'closeButton' => true,
        'keyboard'    => false,
        'footer'      => $footer,
    ),
    $this->loadTemplate('params_zipcodes_modal')
);

JText::script('VRTKAREAZIPADD');
JText::script('VRTKAREAZIPEDIT');
?>

<script>
    (function($, w) {
        'use strict';

        let OPTIONS_COUNT   = <?php echo $this->area->type === 'zipcodes' ? count($this->area->content) : 0; ?>;
        let SELECTED_OPTION = null;

        $(function() {
            // open inspector for new zip codes
            $('.vre-card-fieldset.add-tkarea-zipcodes').on('click', () => {
                vreOpenTkareaZIPCodeCard();
            });

            // fill the form before showing the inspector
            $('#tkarea-zipcodes-inspector').on('inspector.show', function() {
                let data = {};

                // fetch JSON data
                if (SELECTED_OPTION) {
                    const fieldset = $('#' + SELECTED_OPTION);

                    data = fieldset.find('input[name="content[zipcodes][]"]').val();

                    try {
                        data = JSON.parse(data);
                    } catch (err) {
                        data = {};
                    }
                }

                if (data.from === undefined) {
                    // creating new record, hide delete button
                    $('#tkarea-zipcodes-inspector [data-role="delete"]').hide();
                } else {
                    // editing existing record, show delete button
                    $('#tkarea-zipcodes-inspector [data-role="delete"]').show();
                }

                // fill the form with the retrieved data
                fillTkareaZIPCodeForm(data);
            });

            $('#tkarea-zipcodes-inspector').on('inspector.save', function() {
                // get updated zip code data
                const data = getTkareaZIPCodeData();

                if (!data.from) {
                    return false;
                }

                let fieldset;

                if (SELECTED_OPTION) {
                    fieldset = $('#' + SELECTED_OPTION);
                } else {
                    fieldset = vreAddTkareaZIPCodeCard(data);
                }

                if (fieldset.length == 0) {
                    // an error occurred, abort
                    return false;
                }

                // save JSON data
                fieldset.find('input[name="content[zipcodes][]"]').val(JSON.stringify(data));

                // refresh details shown in card
                vreRefreshTkareaZIPCodeCard(fieldset, data);

                // auto-close on save
                $(this).inspector('dismiss');
            });

            $('#tkarea-zipcodes-inspector').on('inspector.delete', function() {
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
        });

        w.vreOpenTkareaZIPCodeCard = (index) => {
            let title;

            if (typeof index === 'undefined') {
                title = Joomla.JText._('VRTKAREAZIPADD');
                SELECTED_OPTION = null;
            } else {
                title = Joomla.JText._('VRTKAREAZIPEDIT');
                SELECTED_OPTION = 'tkarea-zipcodes-fieldset-' + index;
            }
            
            // open inspector
            vreOpenInspector('tkarea-zipcodes-inspector', {title: title});
        }

        const vreAddTkareaZIPCodeCard = (data) => {
            let index = OPTIONS_COUNT++;

            let optionIdAttribute = 'tkarea-zipcodes-fieldset-' + index;

            let html = $('#tkarea-zipcodes-struct').clone().html();
            html = html.replace(/{id}/, index);

            $(
                '<div class="vre-card-fieldset up-to-3" id="' + optionIdAttribute + '">' + html + '</div>'
            ).insertBefore('.vre-card-fieldset.add-tkarea-zipcodes');

            // get created fieldset
            const fieldset = $('#' + optionIdAttribute);

            fieldset.vrecard('edit', 'vreOpenTkareaZIPCodeCard(' + index + ')');

            // create input to hold JSON data
            const input = $('<input type="hidden" name="content[zipcodes][]" />').val(JSON.stringify(data));

            // append input to fieldset
            fieldset.append(input);

            return fieldset;
        }

        const vreRefreshTkareaZIPCodeCard = (elem, data) => {
            const range = [
                data.from,
                data.to,
            ];

            // update primary text
            elem.vrecard('primary', range.filter(s => s).join(' - '));

            if (data.published == 1) {
                elem.find('.vre-card').addClass('published');
            } else {
                elem.find('.vre-card').removeClass('published');
            }

            // update published badge
            elem.vrecard('badge', '<i class="fas ' + (data.published == 1 ? 'fa-check-circle' : 'fa-dot-circle') + '"></i>');
        }
    })(jQuery, window);
</script>