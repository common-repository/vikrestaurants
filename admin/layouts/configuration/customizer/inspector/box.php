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

/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
$formFactory = $displayData['formFactory'];

// create generic length field
$lengthField = $formFactory->createField()
    ->type('text')
    ->class('hasTooltip')
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->hidden(true);

?>

<!-- SIZE - Form -->

<?php
echo $formFactory->createField()
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_BOX_SIZE'))
    ->description(JText::translate('VRE_CUSTOMIZER_INSPECTOR_BOX_SIZE_DESC'))
    ->render(function($data) use ($lengthField) {
        ?>
        <div class="multi-field width-50">
            <?php
            // width
            echo $lengthField->id('customizer_inspector_box_size_width')
                ->title(JText::translate('VRE_CUSTOMIZER_PARAM_WIDTH'))
                ->style('text-align: center;');
            
            // height
            echo $lengthField->id('customizer_inspector_box_size_height')
                ->title(JText::translate('VRE_CUSTOMIZER_PARAM_HEIGHT'))
                ->style('text-align: center;');
            ?>
        </div>
        <?php
    });
?>

<!-- PADDING - Form -->

<?php
echo $formFactory->createField()
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_PADDING'))
    ->description(JText::translate('VRE_CUSTOMIZER_INSPECTOR_BOX_PADDING_DESC'))
    ->render(function($data) use ($lengthField) {
        // top
        echo $lengthField->id('customizer_inspector_box_padding_top')
            ->title(JText::translate('VRE_CUSTOMIZER_PARAM_TOP'))
            ->style('text-align: center;');
        ?>
        <div class="multi-field width-50" style="margin: 10px 0;">
            <?php
            // left
            echo $lengthField->id('customizer_inspector_box_padding_left')
                ->title(JText::translate('VRE_CUSTOMIZER_PARAM_LEFT'))
                ->style('text-align: left;');
            
            // right
            echo $lengthField->id('customizer_inspector_box_padding_right')
                ->title(JText::translate('VRE_CUSTOMIZER_PARAM_RIGHT'))
                ->style('text-align: right;');
            ?>
        </div>
        <?php
        // bottom
        echo $lengthField->id('customizer_inspector_box_padding_bottom')
            ->style('text-align: center;')
            ->title(JText::translate('VRE_CUSTOMIZER_PARAM_BOTTOM'));
    });
?>

<!-- MARGIN - Form -->

<?php
echo $formFactory->createField()
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_MARGIN'))
    ->description(JText::translate('VRE_CUSTOMIZER_INSPECTOR_BOX_MARGIN_DESC'))
    ->render(function($data) use ($lengthField) {
        // top
        echo $lengthField->id('customizer_inspector_box_margin_top')
            ->title(JText::translate('VRE_CUSTOMIZER_PARAM_TOP'))
            ->style('text-align: center;');
        ?>
        <div class="multi-field width-50" style="margin: 10px 0;">
            <?php
            // left
            echo $lengthField->id('customizer_inspector_box_margin_left')
                ->title(JText::translate('VRE_CUSTOMIZER_PARAM_LEFT'))
                ->style('text-align: left;');
            
            // right
            echo $lengthField->id('customizer_inspector_box_margin_right')
                ->title(JText::translate('VRE_CUSTOMIZER_PARAM_RIGHT'))
                ->style('text-align: right;');
            ?>
        </div>
        <?php
        // bottom
        echo $lengthField->id('customizer_inspector_box_margin_bottom')
            ->style('text-align: center;')
            ->title(JText::translate('VRE_CUSTOMIZER_PARAM_BOTTOM'));
    });
?>

<script>
    (function($) {
        'use strict';

        window.fillCustomizerInspectorBoxForm = (style) => {

            /////////////////
            ///// WIDTH /////
            /////////////////

            let width = null;

            if (style.attributes.hasOwnProperty('width')) {
                width = style.attributes.width;
            }

            // set up width
            $('#customizer_inspector_box_size_width').val(width);

            //////////////////
            ///// HEIGHT /////
            //////////////////

            let height = null;

            if (style.attributes.hasOwnProperty('height')) {
                height = style.attributes.height;
            }

            // set up height
            $('#customizer_inspector_box_size_height').val(height);

            ///////////////////
            ///// PADDING /////
            ///////////////////

            let padding = {};

            if (style.attributes.hasOwnProperty('padding')) {
                padding = VRECustomizer.extractRectangle(style.attributes.padding);
            }

            // set up padding
            ['top', 'right', 'bottom', 'left'].forEach((side) => {
                // also look for direct CSS rules (padding-top, padding-right, padding-bottom and padding-left)
                if (style.attributes.hasOwnProperty('padding-' + side)) {
                    padding[side] = style.attributes['padding-' + side];
                }

                if (typeof padding[side] !== 'undefined') {
                    $('#customizer_inspector_box_padding_' + side).val(padding[side]);
                } else {
                    $('#customizer_inspector_box_padding_' + side).val('');
                }
            });

            //////////////////
            ///// MARGIN /////
            //////////////////

            let margin = {};

            if (style.attributes.hasOwnProperty('margin')) {
                margin = VRECustomizer.extractRectangle(style.attributes.margin);
            }

            // set up margin
            ['top', 'right', 'bottom', 'left'].forEach((side) => {
                // also look for direct CSS rules (margin-top, margin-right, margin-bottom and margin-left)
                if (style.attributes.hasOwnProperty('margin-' + side)) {
                    margin[side] = style.attributes['margin-' + side];
                }

                if (typeof margin[side] !== 'undefined') {
                    $('#customizer_inspector_box_margin_' + side).val(margin[side]);
                } else {
                    $('#customizer_inspector_box_margin_' + side).val('');
                }
            });
        }

        window.getCustomizerInspectorBoxData = (data) => {
            
            ////////////////
            ///// SIZE /////
            ////////////////

            ['width', 'height'].forEach((name) => {
                let length = $('#customizer_inspector_box_size_' + name).val();

                if (length !== null && length !== '') {
                    // check whether the size specify the unit too
                    if (VRECustomizer.isNumeric(length)) {
                        // nope, manually include the default unit (px)
                        length += 'px';
                    }
                    
                    data.attributes[name] = VRECustomizer.cleanNumber(length);
                }
            });

            ///////////////////
            ///// PADDING /////
            ///////////////////

            ['top', 'right', 'bottom', 'left'].forEach((side) => {
                let length = $('#customizer_inspector_box_padding_' + side).val();

                if (length !== null && length !== '') {
                    // check whether the size specify the unit too
                    if (VRECustomizer.isNumeric(length)) {
                        // nope, manually include the default unit (px)
                        length += 'px';
                    }

                    data.attributes['padding-' + side] = VRECustomizer.cleanNumber(length);
                }
            });

            //////////////////
            ///// MARGIN /////
            //////////////////

            ['top', 'right', 'bottom', 'left'].forEach((side) => {
                let length = $('#customizer_inspector_box_margin_' + side).val();

                if (length !== null && length !== '') {
                    // check whether the size specify the unit too
                    if (VRECustomizer.isNumeric(length)) {
                        // nope, manually include the default unit (px)
                        length += 'px';
                    }

                    data.attributes['margin-' + side] = VRECustomizer.cleanNumber(length);
                }
            });
        }
    })(jQuery);
</script>