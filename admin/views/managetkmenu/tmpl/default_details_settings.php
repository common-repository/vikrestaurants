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

$menu = $this->menu;

?>

<!-- LAYOUT - Select -->

<?php
echo $this->formFactory->createField()
    ->type('select')
    ->name('layout')
    ->value($menu->layout)
    ->label(JText::translate('VRE_UISVG_LAYOUT'))
    ->description(JText::translate('VRE_MENUS_LAYOUT_DESC'))
    ->options([
        'list' => JText::translate('VRE_MENUS_LAYOUT_OPT_LIST'),
        'grid' => JText::translate('VRE_MENUS_LAYOUT_OPT_GRID'),
    ]);
?>

<script>
    (function($) {
        'use strict';

        $(function() {
            $('select[name="layout"]').select2({
                minimumResultsForSearch: -1,
                allowClear: false,
                width: '90%',
            });
        });
    })(jQuery);
</script>