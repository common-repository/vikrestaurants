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

foreach ($this->conditionalTextFactory->getSupportedActions() as $action)
{
    $id = $action->getID();
    ?>
    <div class="inspector-fieldset inspector-action-fieldset" id="inspector-action-fieldset-<?php echo $id; ?>">
    
        <h3><?php echo $action->getName(); ?></h3>

        <?php
        // fetch action configuration form
        $form = $action->getForm();

        $forms[$id] = [
            'name' => $action->getName(),
            'icon' => $action->getIcon(),
            'form' => $form,
        ];

        if ($description = $action->getDescription())
        {
            // introduce action description as first field
            $form = array_merge([
                '__action_description__' => [
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

        window.conditionalTextActions = <?php echo json_encode($forms); ?>;
    })(jQuery);
</script>