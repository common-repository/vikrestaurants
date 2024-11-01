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

$forms = [];

foreach ($this->conditionalTextFactory->getSupportedFilters() as $filter)
{
    $id = $filter->getID();
    ?>
    <div class="inspector-fieldset inspector-filter-fieldset" id="inspector-filter-fieldset-<?php echo $id; ?>">
    
        <h3><?php echo $filter->getName(); ?></h3>

        <?php
        // fetch filter configuration form
        $form = $filter->getForm();

        $forms[$id] = [
            'name' => $filter->getName(),
            'icon' => $filter->getIcon(),
            'form' => $form,
        ];

        if ($description = $filter->getDescription())
        {
            // introduce filter description as first field
            $form = array_merge([
                '__filter_description__' => [
                    'type' => 'alert',
                    'style' => 'info',
                    'text' => $description,
                    'hiddenLabel' => true,
                ],
            ], $form);
        }

        // render form fields
        echo JLayoutHelper::render('form.fields', [
            'fields' => $form,
            'prefix' => $id . '_',
        ]);
        ?>

    </div>
    <?php
}
?>

<script>
    (function($) {
        'use strict';

        window.conditionalTextFilters = <?php echo json_encode($forms); ?>;
    })(jQuery);
</script>