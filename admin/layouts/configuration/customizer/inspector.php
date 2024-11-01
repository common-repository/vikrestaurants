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

JHtml::fetch('formbehavior.chosen');

if (!isset($displayData['formFactory']))
{
    /** @var E4J\VikRestaurants\Platform\Form\FormFactory */
    $displayData['formFactory'] = VREFactory::getPlatform()->getFormFactory();
}

// register units field within the display data for a better reusability
$displayData['unitsField'] = $displayData['formFactory']->createField()
    ->type('select')
    ->hidden(true)
    ->value('px')
    ->options(['px', '%', 'em', 'rem']);

$vik = VREApplication::getInstance();

?>

<div class="inspector-form" id="inspector-customizer-form">

    <?php echo $vik->bootStartTabSet('customizer_inspector', ['active' => 'customizer_inspector_element']); ?>

        <!-- ELEMENT -->

        <?php echo $vik->bootAddTab('customizer_inspector', 'customizer_inspector_element', JText::translate('VRE_CUSTOMIZER_INSPECTOR_ELEMENT_TAB')); ?>

        	<div class="inspector-fieldset">
                <?php echo $this->sublayout('element', $displayData); ?>
            </div>

        <?php echo $vik->bootEndTab(); ?>

        <!-- APPEARANCE -->

        <?php echo $vik->bootAddTab('customizer_inspector', 'customizer_inspector_appearance', JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TAB')); ?>

            <div class="inspector-fieldset">
                <?php echo $this->sublayout('appearance', $displayData); ?>
            </div>

        <?php echo $vik->bootEndTab(); ?>

        <!-- TEXT -->

        <?php echo $vik->bootAddTab('customizer_inspector', 'customizer_inspector_text', JText::translate('VRE_CUSTOMIZER_INSPECTOR_TEXT_TAB')); ?>

            <div class="inspector-fieldset">
                <?php echo $this->sublayout('text', $displayData); ?>
            </div>

        <?php echo $vik->bootEndTab(); ?>

        <!-- BOX -->

        <?php echo $vik->bootAddTab('customizer_inspector', 'customizer_inspector_box', JText::translate('VRE_CUSTOMIZER_INSPECTOR_BOX_TAB')); ?>

            <div class="inspector-fieldset">
                <?php echo $this->sublayout('box', $displayData); ?>
            </div>

        <?php echo $vik->bootEndTab(); ?>

    <?php echo $vik->bootEndTabSet(); ?>

</div>

<script>
    (function($) {
        'use strict';

        let fullCSS, fieldsLoaded, customDirectives;

        const supportedDirectives = [
            // appearance
            'color', 'background-color', 'background',
            // border
            'border', 'border-radius', 'border-width', 'border-style', 'border-color',
            'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width',
            // text
            'font-size', 'font-weight', 'font-style', 'text-decoration', 'text-align', 'line-height',
            // size
            'width', 'height', 'padding', 'margin', 'padding-top', 'margin-top',
            'padding-left', 'margin-left', 'padding-bottom', 'margin-bottom',
            'padding-right', 'margin-right',
        ];

        window.fillCustomizerInspectorForm = (style) => {
            fieldsLoaded = false;

            // preserve custom directives
            customDirectives = {};

            Object.keys(style.attributes).forEach((key) => {
                if (supportedDirectives.indexOf(key) !== -1) {
                    // supported directive, ignore
                    return;
                }

                // preserve custom directive
                customDirectives[key] = style.attributes[key];
            });

            // check whether at least a rule owns the "!important" directive
            style.important = Object.values(style.attributes).some(v => v.indexOf('!important') !== -1);

            // get rid of the important directive
            Object.keys(style.attributes).forEach((key) => {
                style.attributes[key] = style.attributes[key].replace(/\s*!important\s*$/, '');
            });

            // delegate sub layouts
            fillCustomizerInspectorElementForm(style);
            fillCustomizerInspectorAppearanceForm(style);
            fillCustomizerInspectorTextForm(style);
            fillCustomizerInspectorBoxForm(style);

            // get full CSS editor
            fullCSS = VRECustomizer.editor.getValue();

            if (style.match) {
                // temporarily get rid of the inspected element
                fullCSS = fullCSS.replace(style.match, '');
            }

            fieldsLoaded = true;
        }

        window.getCustomizerInspectorData = () => {
            // prepare return data
            let data = {
                selector: null,
                important: false,
                attributes: {},
            };

            // delegate sub layouts
            getCustomizerInspectorElementData(data);
            getCustomizerInspectorAppearanceData(data);
            getCustomizerInspectorTextData(data);
            getCustomizerInspectorBoxData(data);

            if (data.important) {
                // flag all CSS directives as important
                Object.keys(data.attributes).forEach((key) => {
                    data.attributes[key] += ' !important';
                });
            }

            // preserve custom directives
            Object.keys(customDirectives).forEach((key) => {
                data.attributes[key] = customDirectives[key];
            });

            return data;
        }

        window.useInheritedColorPreview = (elem, input, rule) => {
            const preview = $(input).prev('.color-picker-preview');

            if (preview.length) {
                // inherit color set for the specified selector
                preview.css('background-color', $(elem).css(rule));
            }
        }

        $(function() {
            VikRenderer.chosen('#inspector-customizer-form');

            $('#inspector-customizer-form .support-unit').on('keyup', function() {
                let value = $(this).val();

                if (value === null || value === '' || !('' + value).match(/^\d+(\.\d+)?$/)) {
                    $(this).next().hide();
                } else {
                    $(this).next().show();
                }
            }).trigger('keyup');

            $('#inspector-customizer-form').find('input,select').on('change keyup', () => {
                // do not proceed until all the fields have been filled
                if (!fieldsLoaded) {
                    return;
                }

                // obtain inspector fields data
                const data = getCustomizerInspectorData();

                // construct temporary code for the inspected element
                let tmpCode = data.selector + ' {\n';

                Object.keys(data.attributes).forEach((key) => {
                    tmpCode += '  ' + key + ': ' + data.attributes[key] + ';\n';
                });

                tmpCode += '}';

                // apply temporary changes
                applyCustomizerPreviewCSS(fullCSS + "\n\n" + tmpCode);
            });
        });
    })(jQuery);
</script>