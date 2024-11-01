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

// reuse units field
$unitsField = $displayData['unitsField'];

// quickly create a button field
$createButton = function(string $icon) use ($formFactory) {
    return $formFactory->createField()
        ->type('button')
        ->class('inspector-font-btn font-btn-' . $icon)
        ->text('<i class="fas fa-' . $icon . '"></i>')
        ->hidden(true);
};

?>

<!-- COLOR - Color -->

<?php
echo $formFactory->createField()
    ->type('color')
    ->id('customizer_inspector_text_color')
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_COLOR'))
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->preview(true);
?>

<!-- FONT SIZE - Text -->

<?php
echo $formFactory->createField()
    ->type('text')
    ->id('customizer_inspector_text_font_size')
    ->class('support-unit')
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_SIZE'))
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->render(function($data, $input) use ($unitsField) {
        ?>
        <div class="multi-field width-80-20">
            <?php echo $input; ?>
            <?php echo $unitsField->id('customizer_inspector_text_font_size_unit'); ?>
        </div>
        <?php
    });
?>

<!-- LINE HEIGHT - Text -->

<?php
echo $formFactory->createField()
    ->type('text')
    ->id('customizer_inspector_text_line_height')
    ->class('support-unit')
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_LINE_HEIGHT'))
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->render(function($data, $input) use ($unitsField) {
        ?>
        <div class="multi-field width-80-20">
            <?php
            echo $input;

            // the line-height does not support percentage amounts
            echo $unitsField->id('customizer_inspector_text_line_height_unit')
                ->options(['px', 'em', 'rem']);

            // include "%" again after displaying the units dropdown
            $unitsField->options(['px', '%', 'em', 'rem']);
            ?>
        </div>
        <?php
    });
?>

<!-- FONT STYLE - Form -->

<?php
echo $formFactory->createField()
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_STYLE'))
    ->render(function($data) use ($createButton) {
        ?>
        <div class="btn-group">
            <?php
            echo $createButton('bold')->onclick('ciFontStyleButtonClicked(this, \'bold\')');
            echo $createButton('italic')->onclick('ciFontStyleButtonClicked(this, \'italic\')');
            echo $createButton('underline')->onclick('ciFontStyleButtonClicked(this, \'underline\')');
            ?>
        </div>

        <input type="hidden" id="customizer_inspector_text_font_style" value="" />
        <?php
    });
?>

<!-- TEXT ALIGN - Form -->

<?php
echo $formFactory->createField()
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_FONT_ALIGN'))
    ->render(function($data) use ($createButton) {
        ?>
        <div class="btn-group">
            <?php
            echo $createButton('align-left')->onclick('ciFontAlignmentButtonClicked(this, \'left\')');
            echo $createButton('align-center')->onclick('ciFontAlignmentButtonClicked(this, \'center\')');
            echo $createButton('align-right')->onclick('ciFontAlignmentButtonClicked(this, \'right\')');
            echo $createButton('align-justify')->onclick('ciFontAlignmentButtonClicked(this, \'justify\')');
            ?>
        </div>

        <input type="hidden" id="customizer_inspector_text_font_align" value="" />
        <?php
    });
?>

<script>
    (function($) {
        'use strict';

        window.fillCustomizerInspectorTextForm = (style) => {

            //////////////////////
            ///// TEXT COLOR /////
            //////////////////////

            let color = null;

            if (style.attributes.hasOwnProperty('color')) {
                color = VRECustomizer.getHexColor(style.attributes.color);
            }

            // set up text color
            $('#customizer_inspector_text_color').val(color || '').trigger('change');

            if (!color) {
                window.useInheritedColorPreview(style.element, '#customizer_inspector_text_color', 'color');
            }

            /////////////////////
            ///// FONT SIZE /////
            /////////////////////

            let fontSize = null;

            if (style.attributes.hasOwnProperty('font-size')) {
                fontSize = VRECustomizer.extractUnit(style.attributes['font-size']);

                if (fontSize !== null) {
                    // set the selected unit
                    $('#customizer_inspector_text_font_size_unit').val(fontSize.unit);

                    // take only the value
                    fontSize = fontSize.value;
                } else {
                    // use value as is
                    fontSize = style.attributes['font-size'];
                }
            }

            // set up text font size
            $('#customizer_inspector_text_font_size').val(fontSize || '').trigger('keyup');

            ///////////////////////
            ///// LINE HEIGHT /////
            ///////////////////////

            let lineHeight = null;

            if (style.attributes.hasOwnProperty('line-height')) {
                lineHeight = VRECustomizer.extractUnit(style.attributes['line-height'], ['px', 'em', 'rem']);

                if (lineHeight !== null) {
                    // set the selected unit
                    $('#customizer_inspector_text_line_height_unit').val(lineHeight.unit);

                    // take only the value
                    lineHeight = lineHeight.value;
                } else {
                    // use value as is
                    lineHeight = style.attributes['line-height'];
                }
            }

            // set up text line height
            $('#customizer_inspector_text_line_height').val(lineHeight || '').trigger('keyup');

            //////////////////////
            ///// FONT STYLE /////
            //////////////////////

            let fontStyles = [];

            // look for bold
            if (style.attributes.hasOwnProperty('font-weight') && style.attributes['font-weight'].match(/^bold(?:er)?$/)) {
                fontStyles.push('bold');
            } else {
                // fetch inherited value
                let fontWeight = $(style.element).css('font-weight').toString();

                // check if we have a bold weight
                if (fontWeight.match(/bold(er)?/) || parseInt(fontWeight) >= 600) {
                    fontStyles.push('bold');
                }
            }

            // look for italic
            if (style.attributes.hasOwnProperty('font-style') && style.attributes['font-style'] === 'italic') {
                fontStyles.push('italic');
            } else {
                // fetch inherited value
                let fontStyle = $(style.element).css('font-style').toString();

                // check if we have an italic style
                if (fontStyle === 'italic') {
                    fontStyles.push('italic');
                }
            }

            // look for underline
            if (style.attributes.hasOwnProperty('text-decoration') && style.attributes['text-decoration'] === 'underline') {
                fontStyles.push('underline');
            } else {
                // fetch inherited value
                let textDecoration = $(style.element).css('text-decoration').toString();

                // check if we have an underline decoration
                if (textDecoration === 'underline') {
                    fontStyles.push('underline');
                }
            }

            // reset selected styles
            $('#customizer_inspector_text_font_style').val('');
            $('.inspector-font-btn').removeClass('active');

            // set up text font styles
            fontStyles.forEach((style) => {
                // trigger click only for those buttons that match the selected styles
                $('.font-btn-' + style).trigger('click');
            });

            //////////////////////////
            ///// FONT ALIGNMENT /////
            //////////////////////////

            // reset selected alignment
            $('#customizer_inspector_text_font_align').val('');

            if (style.attributes.hasOwnProperty('text-align')) {
                let fontAlign = style.attributes['text-align'];

                // select the text alignment
                ciFontAlignmentButtonClicked(
                    $('.font-btn-align-' + fontAlign)[0],
                    fontAlign
                );
            }
        }

        window.getCustomizerInspectorTextData = (data) => {

            //////////////////////
            ///// TEXT COLOR /////
            //////////////////////

            let color = $('#customizer_inspector_text_color').val();

            if (color && color.match(/^[0-9a-z]{3,8}$/i)) {
                data.attributes.color = '#' + color;
            }
            
            /////////////////////
            ///// FONT SIZE /////
            /////////////////////

            let fontSize = $('#customizer_inspector_text_font_size').val();

            if (fontSize !== null && fontSize !== '') {
                // check whether the font size specify the unit too
                if (VRECustomizer.isNumeric(fontSize)) {
                    // nope, manually include the selected unit
                    fontSize += $('#customizer_inspector_text_font_size_unit').val();
                }

                data.attributes['font-size'] = VRECustomizer.cleanNumber(fontSize);
            }

            ///////////////////////
            ///// LINE HEIGHT /////
            ///////////////////////

            let lineHeight = $('#customizer_inspector_text_line_height').val();

            if (lineHeight !== null && lineHeight !== '') {
                // check whether the line height specify the unit too
                if (VRECustomizer.isNumeric(lineHeight)) {
                    // nope, manually include the selected unit
                    lineHeight += $('#customizer_inspector_text_line_height_unit').val();
                }

                data.attributes['line-height'] = VRECustomizer.cleanNumber(lineHeight);
            }

            //////////////////////
            ///// FONT STYLE /////
            //////////////////////

            let fontStyles = getFontStyle();

            if (fontStyles.indexOf('bold') !== -1) {
                // register bold
                data.attributes['font-weight'] = 'bold';
            } else {
                // force normal weight
                data.attributes['font-weight'] = 'normal';
            }

            if (fontStyles.indexOf('italic') !== -1) {
                // register italic
                data.attributes['font-style'] = 'italic';
            } else {
                // force normal style
                data.attributes['font-style'] = 'normal';
            }

            if (fontStyles.indexOf('underline') !== -1) {
                // register underline
                data.attributes['text-decoration'] = 'underline';
            } else {
                // register underline
                data.attributes['text-decoration'] = 'none';
            }

            //////////////////////////
            ///// FONT ALIGNMENT /////
            //////////////////////////

            let fontAlign = getFontAlignment();

            if (fontAlign) {
                data.attributes['text-align'] = fontAlign;
            }
        }

        const getFontStyle = () => {
            return ($('#customizer_inspector_text_font_style').val() || '').split('|').filter(s => s);
        }

        window.ciFontStyleButtonClicked = (button, style) => {
            const input = $('#customizer_inspector_text_font_style');
            let styles = getFontStyle();
            let index  = styles.indexOf(style);

            if (index !== -1) {
                // style already selected, unset it
                $(button).removeClass('active')
                styles.splice(index, 1);
            } else {
                // style not selected, register it
                $(button).addClass('active')
                styles.push(style);
            }

            input.val(styles.join('|')).trigger('change');
        }

        const getFontAlignment = () => {
            return $('#customizer_inspector_text_font_align').val() || '';
        }

        window.ciFontAlignmentButtonClicked = (button, align) => {
            const input = $('#customizer_inspector_text_font_align');
            let selected = getFontAlignment();

            $('button[onclick^="ciFontAlignmentButtonClicked"]').removeClass('active');

            if (selected == align) {
                // alignment already selected, unset it
                align = '';
            } else {
                // alignment not selected, replace it
                $(button).addClass('active')
            }

            input.val(align).trigger('change');
        }
    })(jQuery);
</script>