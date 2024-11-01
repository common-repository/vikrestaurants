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

$actLayout = new JLayoutFile('blocks.card');

?>
            
<div class="vre-cards-container add-cards-mailtext-actions" id="add-cards-mailtext-actions">

    <?php foreach ($this->conditionalTextFactory->getSupportedActions() as $action): ?>
        <div class="vre-card-fieldset up-to-1" id="action-fieldset-<?php echo $action->getID(); ?>">

            <?php
            $displayData = [];

            // fetch card ID
            $displayData['id'] = 'action-card-' . $action->getID();

            // fetch badge class
            $displayData['class'] = 'published';

            // fetch badge
            $displayData['badge'] = '<i class="' . $action->getIcon() . '"></i>';

            // fetch primary text
            $displayData['primary']  = $action->getName();

            if ($description = $action->getDescription())
            {
                // fetch secondary text
                $displayData['secondary'] = '<small>' . $description . '</small>';
            }

            // fetch edit button
            $displayData['edit'] = 'fillMailtextActionForm(\'' . $action->getID() . '\');';
            $displayData['editText'] = JText::translate('VRADD');

            // render layout
            echo $actLayout->render($displayData);
            ?>

        </div>
    <?php endforeach; ?>

</div>
