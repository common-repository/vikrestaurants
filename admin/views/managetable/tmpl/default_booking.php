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

$table = $this->table;

?>
				
<!-- MIN CAPACITY - Number -->

<?php
echo $this->formFactory->createField()
    ->type('number')
    ->name('min_capacity')
    ->value((int) $table->min_capacity)
    ->required(true)
    ->label(JText::translate('VRMANAGETABLE2'))
    ->description(JText::translate('VRMANAGETABLE2_HELP'))
    ->min(1)
    ->step(1);
?>

<!-- MAX CAPACITY - Number -->

<?php
echo $this->formFactory->createField()
    ->type('number')
    ->name('max_capacity')
    ->value((int) $table->max_capacity)
    ->required(true)
    ->label(JText::translate('VRMANAGETABLE3'))
    ->description(JText::translate('VRMANAGETABLE3_HELP'))
    ->min(1)
    ->step(1);
?>

<!-- CAN BE SHARED - Checkbox -->

<?php
echo $this->formFactory->createField()
    ->type('checkbox')
    ->name('multi_res')
    ->checked($table->multi_res)
    ->label(JText::translate('VRMANAGETABLE12'))
    ->description(JText::translate('VRMANAGETABLE12_HELP'))
    ->onchange('canBeSharedValueChanged(this.checked)');
?>

<script>
    (function($, w) {
        'use strict';

        w.canBeSharedValueChanged = (checked) => {
            $('#vr-cluster-sel').prop('disabled', checked);
        }
    })(jQuery, window);
</script>