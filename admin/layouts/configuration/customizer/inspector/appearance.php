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

?>

<!-- BACKGROUND - Select -->

<?php
echo $formFactory->createField()
    ->type('select')
    ->id('customizer_inspector_appearance_background_type')
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_BACKGROUND'))
    ->options([
        'color'           => JText::translate('VRE_CUSTOMIZER_PARAM_COLOR'),
        'linear-gradient' => JText::translate('VRE_CUSTOMIZER_PARAM_LINEAR_GRADIENT'),
        'radial-gradient' => JText::translate('VRE_CUSTOMIZER_PARAM_RADIAL_GRADIENT'),
        'image'           => JText::translate('VRE_UISVG_NEW_CMD_PARAM_SHAPE_TYPE_IMAGE'),
    ]);
?>

<!-- BACKGROUND COLOR - Color -->

<?php
echo $formFactory->createField()
    ->type('color')
    ->id('customizer_inspector_appearance_background')
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->preview(true)
    ->hiddenLabel(true)
    ->control([
        'visible' => false,
        'class'   => 'bg-related-field bg-color-related-field',
    ]);
?>

<!-- BACKGROUND OPACITY - Color -->

<?php
echo $formFactory->createField()
    ->type('range')
    ->id('customizer_inspector_appearance_background_opacity')
    ->title(JText::translate('VRE_CUSTOMIZER_PARAM_OPACITY'))
    ->class('hasTooltip')
    ->min(0)->max(100)->value(100)
    ->hiddenLabel(true)
    ->control([
        'visible' => false,
        'class'   => 'bg-related-field bg-color-related-field',
    ]);
?>

<!-- BACKGROUND GRADIENT - Form -->

<?php
$bgGradientColor = $formFactory->createField()
    ->type('color')
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->preview(true)
    ->hidden(true);

$bgGradientAngle = $formFactory->createField()
    ->type('number')
    ->id('customizer_inspector_appearance_background_gradient_angle')
    ->title(JText::translate('VRE_CUSTOMIZER_PARAM_ANGLE'))
    ->class('hasTooltip')
    ->step(1)
    ->hiddenLabel(true)
    ->control([
        'visible' => false,
        'class'   => 'bg-related-field bg-linear-gradient-related-field',
    ]);

$bgGradientOffset = $formFactory->createField()
    ->type('range')
    ->id('customizer_inspector_appearance_background_gradient_offset')
    ->title(JText::translate('VRE_CUSTOMIZER_PARAM_OFFSET'))
    ->class('hasTooltip')
    ->min(0)->max(100)->value(50)
    ->hidden(true);

echo $formFactory->createField()
    ->hiddenLabel(true)
    ->control([
        'visible' => false,
        'class'   => 'bg-related-field bg-linear-gradient-related-field bg-radial-gradient-related-field',
    ])
    ->render(function($data) use ($bgGradientColor, $bgGradientAngle, $bgGradientOffset) {
        ?>
        <div class="multi-field width-50">
            <?php echo $bgGradientColor->id('customizer_inspector_appearance_background_gradient_color_1'); ?>
            <?php echo $bgGradientColor->id('customizer_inspector_appearance_background_gradient_color_2'); ?>
        </div>
        <div class="multi-field width-50" style="margin-top: 10px;">
            <?php echo $bgGradientAngle->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('°')); ?>
            <?php echo $bgGradientOffset; ?>
        </div>
        <?php
    });
?>

<!-- BACKGROUND IMAGE - Form -->

<?php
$bgImageUrl = $formFactory->createField()
    ->type('media')
    ->id('customizer_inspector_appearance_background_image_url')
    ->attributes(['preview' => false])
    ->hidden(true);

$bgImageSize = $formFactory->createField()
    ->type('select')
    ->id('customizer_inspector_appearance_background_image_size')
    ->hidden(true)
    ->options([
        'cover'     => JText::translate('VRE_UISVG_COVER'),
        'repeat'    => JText::translate('VRE_UISVG_REPEAT'),
        'repeat-x'  => JText::translate('VRE_UISVG_HOR_REPEAT'),
        'repeat-y'  => JText::translate('VRE_UISVG_VER_REPEAT'),
        'no-repeat' => JText::translate('VRE_UISVG_NO_REPEAT'),
    ]);

$bgImagePosition = $formFactory->createField()
    ->type('select')
    ->id('customizer_inspector_appearance_background_image_position')
    ->hidden(true)
    ->options([
        'top left'      => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TOP_LEFT'),
        'top center'    => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TOP_CENTER'),
        'top right'     => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_TOP_RIGHT'),
        'center left'   => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER_LEFT'),
        'center'        => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER'),
        'center right'  => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_CENTER_RIGHT'),
        'bottom left'   => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_BOTTOM_LEFT'),
        'bottom center' => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_BOTTOM_CENTER'),
        'bottom right'  => JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_BOTTOM_RIGHT'),
    ]);

echo $formFactory->createField()
    ->hiddenLabel(true)
    ->control([
        'visible' => false,
        'class'   => 'bg-related-field bg-image-related-field',
    ])
    ->render(function($data) use ($bgImageUrl, $bgImageSize, $bgImagePosition) {
        ?>
        <div>
            <?php echo $bgImageUrl; ?>
        </div>
        <div class="multi-field width-50" style="margin-top: 10px;">
            <?php echo $bgImageSize; ?>
            <?php echo $bgImagePosition; ?>
        </div>
        <?php
    });
?>

<!-- BORDER - Separator -->

<?php
echo $formFactory->createField()
    ->type('separator')
    ->hiddenLabel(true)
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_BORDER'));
?>

<!-- BORDER RADIUS - Text -->

<?php
echo $formFactory->createField()
    ->type('text')
    ->id('customizer_inspector_appearance_border_radius')
    ->class('support-unit')
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_RADIUS'))
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->render(function($data, $input) use ($unitsField) {
        ?>
        <div class="multi-field width-80-20">
            <?php echo $input; ?>
            <?php echo $unitsField->id('customizer_inspector_appearance_border_radius_unit'); ?>
        </div>
        <?php
    });
?>

<!-- BORDER WIDTH - Text -->

<?php
echo $formFactory->createField()
    ->type('text')
    ->id('customizer_inspector_appearance_border_width')
    ->class('support-unit')
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_WIDTH'))
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->render(function($data, $input) use ($unitsField) {
        ?>
        <div class="multi-field width-80-20">
            <?php
            echo $input;

            // the border-width does not support percentage amounts
            echo $unitsField->id('customizer_inspector_appearance_border_width_unit')
                ->options(['px', 'em', 'rem']);

            // include "%" again after displaying the units dropdown
            $unitsField->options(['px', '%', 'em', 'rem']);
            ?>
        </div>
        <?php
    });
?>

<!-- BORDER COLOR - Color -->

<?php
echo $formFactory->createField()
    ->type('color')
    ->id('customizer_inspector_appearance_border_color')
    ->label(JText::translate('VRE_CUSTOMIZER_PARAM_COLOR'))
    ->placeholder(JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'))
    ->preview(true)
    ->control([
        'class'   => 'ci-border-related-field',
        'visible' => false,
    ]);
?>

<!-- BORDER STYLE - Select -->

<?php
echo $formFactory->createField()
    ->type('select')
    ->id('customizer_inspector_appearance_border_style')
    ->label(JText::translate('VRE_CUSTOMIZER_FIELDS_STYLE'))
    ->options([
        ''       => JText::translate('VRE_CUSTOMIZER_PARAM_INHERIT'),
        'solid'  => JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_SOLID'),
        'dashed' => JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DASHED'),
        'dotted' => JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DOTTED'),
        'double' => JText::translate('VRE_CUSTOMIZER_PARAM_BORDER_STYLE_DOUBLE'),
    ])
    ->control([
        'class'   => 'ci-border-related-field',
        'visible' => false,
    ]);
?>

<!-- BORDER SIDES - Form -->

<?php
echo $formFactory->createField()
    ->id('customizer_inspector_appearance_border_sides')
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_SIDES'))
    ->description(JText::translate('VRE_CUSTOMIZER_INSPECTOR_APPEARANCE_SIDES_DESC'))
    ->control([
        'class'   => 'ci-border-related-field',
        'visible' => false,
    ])->render(function($data) {
        $allSides = [
            // all 4 sides
            'top right bottom left',
            // top-bottom and another side
            'top bottom left', 'top right bottom',
            // right-left and another side
            'top right left', 'right bottom left',
            // 2 opposite sides only
            'top bottom', 'right left',
            // 2 contiguous sides
            'top left', 'bottom left', 'right bottom', 'top right',
            // single sides
            'top', 'right', 'bottom', 'left',
            // none
            '',
        ];
        
        ?>
        <style>
            .select-border-field-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }

            .select-border-field-container .select-border-btn-wrapper {
                width: calc(100% / 4);
                padding: 10px;
                margin: 4px 0;
                text-align: center;
            }

            .select-border-side-btn {
                padding: 8px;
                display: inline-block;
                cursor: pointer;
                text-decoration: none;
            }
            .select-border-side-btn:hover {
                background: #9fccfb8f;
                border-radius: 4px;
                text-decoration: none;
            }
            .select-border-side-btn.active {
                background: #78db9f8f !important;
                border-radius: 4px;
            }

            .select-border-side-btn-inner {
                width: 32px;
                height: 32px;
                border-style: dashed;
            }
        </style>

        <input
            type="hidden"
            name="<?php echo htmlspecialchars($data->get('name', ''), ENT_QUOTES); ?>"
            id="<?php echo htmlspecialchars($data->get('id', ''), ENT_QUOTES); ?>"
            value="<?php echo htmlspecialchars($data->get('value', ''), ENT_QUOTES); ?>"
        />
        
        <div class="select-border-field-container" id="<?php echo htmlspecialchars($data->get('id'), ENT_QUOTES); ?>-field">
            <?php foreach ($allSides as $sides): ?>
                <?php
                $active = array_filter(explode(' ', $sides));

                $dataSides = [];
                $style = [];
                
                foreach (['top', 'right', 'bottom', 'left'] as $side)
                {
                    if (in_array($side, $active))
                    {
                        $dataSides[] = 'data-side-' . $side . '="1"';
                        $style[] = 'border-' . $side . '-style: solid;';
                        $style[] = 'border-' . $side . '-width: 2px;';
                        $style[] = 'border-' . $side . '-color: #333;';
                    }
                    else
                    {
                        $dataSides[] = 'data-side-' . $side . '="0"';
                        $style[] = 'border-' . $side . '-style: dashed;';
                        $style[] = 'border-' . $side . '-width: 1px;';
                        $style[] = 'border-' . $side . '-color: #bbb;';
                    }
                }

                ?>
                <div class="select-border-btn-wrapper">
                    <a href="javascript:void(0)" class="select-border-side-btn" <?php echo implode(' ', $dataSides); ?>>
                        <div class="select-border-side-btn-inner" style="<?php echo implode(' ', $style); ?>">
                            
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    });
?>

<script>
    (function($) {
        'use strict';

        window.fillCustomizerInspectorAppearanceForm = (style) => {

            const border = style.attributes.hasOwnProperty('border') ? VRECustomizer.extractBorder(style.attributes.border) : {};

            ///////////////////////////
            ///// BACKGROUND TYPE /////
            ///////////////////////////

            let background = null, bgType = 'color', gradient = null, image = null;

            if (style.attributes.hasOwnProperty('background-color')) {
                // try first with "background-color"
                let tmp = VRECustomizer.getHexColor(style.attributes['background-color']);

                if (tmp) {
                    background = {
                        color: tmp,
                        opacity: VRECustomizer.getHexColorOpacity(style.attributes['background-color']),
                    };
                }
            }

            if (!background && style.attributes.hasOwnProperty('background')) {
                // fallback to "background"
                const backgroundData = VRECustomizer.extractBackground(style.attributes.background);

                if (backgroundData.type === 'color') {
                    background = backgroundData;
                } else if (backgroundData.type === 'image') {
                    image = backgroundData;
                    bgType = 'image';
                } else {
                    // use gradient
                    gradient = backgroundData;
                    bgType = gradient.type;
                }
            }

            $('#customizer_inspector_appearance_background_type').val(bgType).trigger('change');

            ////////////////////////////
            ///// BACKGROUND COLOR /////
            ////////////////////////////

            // set up background color
            $('#customizer_inspector_appearance_background').val(background ? background.color : '').trigger('change');

            if (!background) {
                window.useInheritedColorPreview(style.element, '#customizer_inspector_appearance_background', 'background-color');
            }

            // set up background color opacity
            $('#customizer_inspector_appearance_background_opacity').val(background && !isNaN(background.opacity) ? background.opacity * 100 : 100);

            ///////////////////////////////
            ///// BACKGROUND GRADIENT /////
            ///////////////////////////////

            // set up background gradient angle
            $('#customizer_inspector_appearance_background_gradient_angle').val(gradient ? gradient.angle : 0).trigger('change');

            // set up background gradient offset
            $('#customizer_inspector_appearance_background_gradient_offset').val(gradient ? gradient.offset : 50).trigger('input');

            // set up background gradient colors
            $('#customizer_inspector_appearance_background_gradient_color_1').val(gradient ? gradient.color[0] : '').trigger('change');
            $('#customizer_inspector_appearance_background_gradient_color_2').val(gradient ? gradient.color[1] : '').trigger('change');

            ////////////////////////////
            ///// BACKGROUND IMAGE /////
            ////////////////////////////

            // set up background image URL
            let regex = new RegExp('^<?php echo VREMEDIA_URI; ?>');
            $('#customizer_inspector_appearance_background_image_url').mediamanager('value', image && image.url ? image.url.replace(regex, '') : null).trigger('change');

            // set up background image size
            let size   = image && image.size ? image.size : 'cover';
            $('#customizer_inspector_appearance_background_image_size').val(size === 'cover' ? 'cover' : image.repeat).trigger('change');

            // set up background image position
            $('#customizer_inspector_appearance_background_image_position').val(image ? image.position : 'center').trigger('change');

            /////////////////////////
            ///// BORDER RADIUS /////
            /////////////////////////

            let borderRadius = null;

            if (style.attributes.hasOwnProperty('border-radius')) {
                // try to extract the unit from the value
                borderRadius = VRECustomizer.extractUnit(style.attributes['border-radius']);

                if (borderRadius !== null) {
                    // set the selected unit
                    $('#customizer_inspector_appearance_border_radius_unit').val(borderRadius.unit);

                    // take only the value
                    borderRadius = borderRadius.value;
                } else {
                    // use value as is
                    borderRadius = style.attributes['border-radius'];
                }
            }

            // set up border radius
            $('#customizer_inspector_appearance_border_radius').val(borderRadius || '').trigger('keyup');

            ////////////////////////
            ///// BORDER WIDTH /////
            ////////////////////////

            let borderWidth = null;

            if (border.width !== undefined && !style.attributes.hasOwnProperty('border-width')) {
                // border width not provided, use the one specified by the border directive
                style.attributes["border-width"] = border.width;
            }

            if (style.attributes.hasOwnProperty('border-width')) {
                // try to extract the unit from the value
                borderWidth = VRECustomizer.extractUnit(style.attributes['border-width'], ['px', 'em', 'rem']);

                if (borderWidth !== null) {
                    // set the selected unit
                    $('#customizer_inspector_appearance_border_width_unit').val(borderWidth.unit);

                    // take only the value
                    borderWidth = borderWidth.value;
                } else {
                    // use value as is
                    borderWidth = style.attributes['border-width'];
                }
            }

            // set up border width
            $('#customizer_inspector_appearance_border_width').val(borderWidth || '').trigger('keyup');

            ////////////////////////
            ///// BORDER COLOR /////
            ////////////////////////

            let borderColor = null;

            if (style.attributes.hasOwnProperty('border-color')) {
                // try first with "border-color"
                borderColor = VRECustomizer.getHexColor(style.attributes['border-color']);
            }

            if (!borderColor && border.color) {
                // fallback to color defined by the "border" directive
                borderColor = border.color;
            }

            // set up border color
            $('#customizer_inspector_appearance_border_color').val(borderColor || '').trigger('change');

            if (!borderColor) {
                window.useInheritedColorPreview(style.element, '#customizer_inspector_appearance_border_color', 'border-color');
            }

            ////////////////////////
            ///// BORDER STYLE /////
            ////////////////////////

            let borderStyle = null;

            if (style.attributes.hasOwnProperty('border-style')) {
                // try first with "border-style"
                borderStyle = style.attributes['border-style'];
            } else if (border.style) {
                // fallback to style defined by the "border" directive
                borderStyle = border.style;
            }

            // set up border style
            $('#customizer_inspector_appearance_border_style').val(borderStyle || '');

            ////////////////////////
            ///// BORDER SIDES /////
            ////////////////////////

            let borderSides = {};

            ['top', 'right', 'left', 'bottom'].forEach((side) => {
                if (!style.attributes.hasOwnProperty('border-' + side + '-width')) {
                    return
                }

                borderSides[side] = parseInt(style.attributes['border-' + side + '-width'].replace(/[^0-9.]+/, ''));
            });

            let borderSidesKeys = Object.keys(borderSides);

            if (borderSidesKeys.length == 0) {
                // reset border sides selection
                $('#customizer_inspector_appearance_border_sides-field .select-border-side-btn').removeClass('active');
                $('#customizer_inspector_appearance_border_sides').val('');
            } else {
                let selectedBorderSides = '';

                borderSidesKeys.forEach((side) => {
                    selectedBorderSides += '[data-side-' + side + '="' + (borderSides[side] > 0 ? 1 : 0) + '"]';
                });

                $('#customizer_inspector_appearance_border_sides-field .select-border-side-btn' + selectedBorderSides).trigger('click');
            }

        }

        window.getCustomizerInspectorAppearanceData = (data) => {

            // fetch background type
            let backgroundType = $('#customizer_inspector_appearance_background_type').val();

            //////////////////////
            ///// BACKGROUND /////
            //////////////////////

            if (backgroundType === 'color') {
                // background color
                let background = $('#customizer_inspector_appearance_background').val();

                // background opacity (in percentage)
                let opacity = $('#customizer_inspector_appearance_background_opacity').val() || 100;
                // convert percentage opacity in 8 bit
                // 255 : 100 = X : opacity
                opacity = opacity * 255 / 100;

                if (background && background.match(/^[0-9a-z]{3,8}$/i)) {
                    // check if we have an opacity lower than 100% (255)
                    if (opacity < 255) {
                        // convert 8-bit opacity in HEX value
                        opacity = Math.round(opacity).toString(16).padStart(2, '0');

                        if (background.length == 3) {
                            // we have a HEX in the form #rgb, append the first digit of alpha
                            background += opacity.substr(0, 1);
                        } else if (background.length == 6) {
                            // we have a HEX in the form #rrggbb, append the alpha
                            background += opacity;
                        }
                    }

                    data.attributes['background-color'] = '#' + background;
                }
            } else if (backgroundType === 'image') {
                // background image
                let url      = $('#customizer_inspector_appearance_background_image_url').mediamanager('val');
                let size     = $('#customizer_inspector_appearance_background_image_size').val();
                let position = $('#customizer_inspector_appearance_background_image_position').val();
                let repeat   = 'no-repeat';

                if (size != 'cover') {
                    repeat = size;
                    size   = 'auto';
                }

                // register background only if we have an image selected
                if (url) {
                    data.attributes['background'] = '{position} / {size} {repeat} url(<?php echo VREMEDIA_URI; ?>{url})'
                        .replace(/{position}/, position)
                        .replace(/{size}/, size)
                        .replace(/{repeat}/, repeat)
                        .replace(/{url}/, url);
                }
            } else {
                // background gradient
                let angle  = parseInt($('#customizer_inspector_appearance_background_gradient_angle').val());
                let offset = parseInt($('#customizer_inspector_appearance_background_gradient_offset').val());
                let color1 = $('#customizer_inspector_appearance_background_gradient_color_1').val();
                let color2 = $('#customizer_inspector_appearance_background_gradient_color_2').val();

                if (isNaN(angle) || angle < -360 || angle > 360) {
                    // invalid angle
                    angle = 0;
                }

                if (isNaN(offset) || offset < 0 || offset > 100) {
                    // invalid offset
                    offset = 50;
                }

                if (!color1 || !color1.match(/^[0-9a-z]{3,8}$/i)) {
                    // fallback to whith
                    color1 = 'fff';
                }

                if (!color2 || !color2.match(/^[0-9a-z]{3,8}$/i)) {
                    // same as first color
                    color2 = color1;
                }

                let bgPattern;

                if (backgroundType === 'linear-gradient') {
                    bgPattern = 'linear-gradient({angle}deg, #{color1}, {offset}%, #{color2})';
                } else {
                    bgPattern = 'radial-gradient(#{color1}, {offset}%, #{color2})';
                }

                data.attributes['background'] = bgPattern
                    .replace(/{angle}/, angle)
                    .replace(/{color1}/, color1)
                    .replace(/{offset}/, offset)
                    .replace(/{color2}/, color2);
            }

            /////////////////////////
            ///// BORDER RADIUS /////
            /////////////////////////

            let borderRadius = $('#customizer_inspector_appearance_border_radius').val();

            if (borderRadius !== null && borderRadius !== '') {
                // check whether the border radius specify the unit too
                if (VRECustomizer.isNumeric(borderRadius)) {
                    // nope, manually include the selected unit
                    borderRadius += $('#customizer_inspector_appearance_border_radius_unit').val();
                }

                data.attributes['border-radius'] = VRECustomizer.cleanNumber(borderRadius);
            }

            ////////////////////////
            ///// BORDER WIDTH /////
            ////////////////////////

            let borderWidth = $('#customizer_inspector_appearance_border_width').val();

            if (borderWidth !== null && borderWidth !== '') {
                // check whether the border width specify the unit too
                if (VRECustomizer.isNumeric(borderWidth)) {
                    // nope, manually include the selected unit
                    borderWidth += $('#customizer_inspector_appearance_border_width_unit').val();
                }

                data.attributes['border-width'] = VRECustomizer.cleanNumber(borderWidth);
            }

            if (borderWidth) {

                ////////////////////////
                ///// BORDER COLOR /////
                ////////////////////////

                let borderColor = $('#customizer_inspector_appearance_border_color').val();

                if (borderColor && borderColor.match(/^[0-9a-z]{3,8}$/i)) {
                    data.attributes['border-color'] = '#' + borderColor;
                }

                ////////////////////////
                ///// BORDER STYLE /////
                ////////////////////////

                let borderStyle = $('#customizer_inspector_appearance_border_style').val();

                if (borderStyle) {
                    data.attributes['border-style'] = borderStyle;
                }

                ////////////////////////
                ///// BORDER SIDES /////
                ////////////////////////

                let borderSides;

                try {
                    borderSides = JSON.parse($('#customizer_inspector_appearance_border_sides').val());

                    // iterate all the 4 sides
                    ['top', 'right', 'bottom', 'left'].forEach((side) => {
                        if (borderSides.indexOf(side) !== -1) {
                            data.attributes['border-' + side + '-width'] = borderWidth;
                        } else {
                            data.attributes['border-' + side + '-width'] = 0;
                        }
                    });
                } catch (err) {
                    
                }

            }

        }

        $(function() {
            $('#customizer_inspector_appearance_background_type').on('change', function() {
                $('.bg-related-field').hide();
                $('.bg-' + $(this).val() + '-related-field').show();
            });

            $('#customizer_inspector_appearance_background_gradient_angle').on('change', function() {
                let value = parseInt($(this).val());

                if (isNaN(value)) {
                    value = 0;
                }

                value = Math.min(value,  360);
                value = Math.max(value, -360);

                $(this).val(value);
            });

            $('#customizer_inspector_appearance_background_gradient_offset').on('input', function() {
                let value = parseInt($(this).val());

                if (isNaN(value)) {
                    value = 50;
                }

                value = Math.min(value, 100);
                value = Math.max(value,   0);

                $(this).val(value).trigger('change');
            });

            $('#customizer_inspector_appearance_border_width').on('keyup', function() {
                let borderWidth = $(this).val();

                if (borderWidth && borderWidth !== '0') {
                    $('.ci-border-related-field').show();

                    if (!$('#customizer_inspector_appearance_border_style').val()) {
                        // auto-select the solid style
                        $('#customizer_inspector_appearance_border_style').val('solid');
                    }
                } else {
                    $('.ci-border-related-field').hide();
                }
            });

            $('#customizer_inspector_appearance_border_sides-field .select-border-side-btn').on('click', function() {
                if ($(this).hasClass('active')) {
                    // clicked sides already active
                    return false;
                }

                // deselect the previously selected sides
                $('#customizer_inspector_appearance_border_sides-field .select-border-side-btn').removeClass('active');
                // select the clicked sides
                $(this).addClass('active');

                let selectedSides = [];

                ['top', 'right', 'bottom', 'left'].forEach((side) => {
                    if ($(this).attr('data-side-' + side) == "1") {
                        selectedSides.push(side);
                    }
                });

                // trigger change event
                $('#customizer_inspector_appearance_border_sides').val(JSON.stringify(selectedSides)).trigger('change');
            });
        });
    })(jQuery);
</script>