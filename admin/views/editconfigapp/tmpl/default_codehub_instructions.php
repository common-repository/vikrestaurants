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

$codeHandlers = $this->codeHub->getCodeHandlers();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigappCodeHubInstructions". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('CodeHubInstructions');

?>

<div class="config-fieldset">

    <div class="config-fieldset-head">
        <h3><?php echo JText::translate('VRMANAGECONFIGTITLE0'); ?></h3>
    </div>

    <div class="config-fieldset-body">

        <?php
        $str = [];

        foreach ($codeHandlers as $key => $handler)
        {
            if ($handler instanceof E4J\VikRestaurants\CodeHub\CodeHandlerDescriptor)
            {
                $key = $handler->getName();
            }

            $str[] = '<code>' . $key . '</code>';
        }

        echo JText::sprintf('VRE_CODE_BLOCK_INSTR_GLOBAL', implode(', ', $str));
        ?>

        <!-- Define role to detect the supported hook -->
        <!-- {"rule":"customizer","event":"onDisplayViewConfigappCodeHubInstructions","key":"basic","type":"field"} -->

        <?php   
        /**
         * Look for any additional fields to be pushed within
         * the CodeHub > Instructions > Details fieldset.
         *
         * @since 1.9
         */
        if (isset($forms['basic']))
        {
            echo $forms['basic'];

            // unset details form to avoid displaying it twice
            unset($forms['basic']);
        }
        ?>

    </div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigappCodeHubInstructions","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the CodeHub > Instructions tab.
 *
 * @since 1.9
 */
foreach ($forms as $formTitle => $formHtml)
{
    ?>
    <div class="config-fieldset">
        
        <div class="config-fieldset-head">
            <h3><?php echo JText::translate($formTitle); ?></h3>
        </div>

        <div class="config-fieldset-body">
            <?php echo $formHtml; ?>
        </div>
        
    </div>
    <?php
}

foreach ($codeHandlers as $handler)
{
    if ($handler instanceof E4J\VikRestaurants\CodeHub\CodeHandlerDescriptor)
    {
        ?>
        <div class="config-fieldset">

            <div class="config-fieldset-head">
                <h3><?php echo $handler->getName(); ?></h3>
            </div>

            <div class="config-fieldset-body">
                <?php echo $handler->getHelp(); ?>
            </div>

        </div>
        <?php
    }
}
