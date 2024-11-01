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

?>

<!-- SELECTOR - Textarea -->

<?php
echo $formFactory->createField()
    ->type('textarea')
    ->id('customizer_inspector_element_selector')
    ->readonly(true)
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_ELEMENT_SELECTOR'))
    ->description(JText::translate('VRE_CUSTOMIZER_INSPECTOR_ELEMENT_SELECTOR_DESC'))
    ->style('resize: none;')
    ->height(80);
?>

<!-- IMPORTANT - Checkbox -->

<?php
// hide important parameter and make it active by default
echo $formFactory->createField()
    ->type('checkbox')
    ->id('customizer_inspector_element_important')
    ->checked(true)
    ->label(JText::translate('VRE_CUSTOMIZER_INSPECTOR_ELEMENT_IMPORTANT'))
    ->description(JText::translate('VRE_CUSTOMIZER_INSPECTOR_ELEMENT_IMPORTANT_DESC'))
    ->control(['visible' => false]);
?>

<script>
    (function($) {
        'use strict';

        window.fillCustomizerInspectorElementForm = (style) => {
            if (!style.selector) {
                throw "Element selector not provided";
            }

            // set up HTML element(s) selector
            $('#customizer_inspector_element_selector').val(style.selector);

            // set up important checkbox
            // $('#customizer_inspector_element_important').prop('checked', style.important);
        }

        window.getCustomizerInspectorElementData = (data) => {
            // set element selector
            data.selector = $('#customizer_inspector_element_selector').val();

            // set important directive
            data.important = $('#customizer_inspector_element_important').is(':checked');
        }
    })(jQuery);
</script>