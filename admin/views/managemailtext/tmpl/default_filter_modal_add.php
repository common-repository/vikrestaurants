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

$fltLayout = new JLayoutFile('blocks.card');

?>
            
<div class="vre-cards-container add-cards-mailtext-filters" id="add-cards-mailtext-filters">

    <?php foreach ($this->conditionalTextFactory->getSupportedFilters() as $filter): ?>
        <div class="vre-card-fieldset up-to-1" id="filter-fieldset-<?php echo $filter->getID(); ?>">

            <?php
            $displayData = [];

            // fetch card ID
            $displayData['id'] = 'filter-card-' . $filter->getID();

            // fetch badge
            $displayData['badge'] = '<i class="' . $filter->getIcon() . '"></i>';

            // fetch primary text
            $displayData['primary']  = $filter->getName();

            if ($description = $filter->getDescription())
            {
                // fetch secondary text
                $displayData['secondary'] = '<small>' . $description . '</small>';
            }

            // fetch edit button
            $displayData['edit'] = 'fillMailtextFilterForm(\'' . $filter->getID() . '\');';
            $displayData['editText'] = JText::translate('VRADD');

            // render layout
            echo $fltLayout->render($displayData);
            ?>

        </div>
    <?php endforeach; ?>

</div>
