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

$area = $this->area;

$vik = VREApplication::getInstance();

?>

<div class="row-fluid">

    <!-- RIGHT SIDE -->

    <div class="span8">

        <!-- MAP -->

        <div class="row-fluid">
            <div class="span12">
                <?php
                echo $vik->openFieldset(JText::translate('VRTKAREAFIELDSET4'));
                echo $this->loadTemplate('params_polygon_map');
                echo $vik->closeFieldset();
                ?>
            </div>
        </div>

    </div>

    <!-- LEFT SIDE -->

    <div class="span4 full-width">

        <!-- POLYGON -->

        <div class="row-fluid">
            <div class="span12">
                <?php
                echo $vik->openFieldset(JText::translate('VRTKAREATYPE1'), 'form-vertical');
                echo $this->loadTemplate('params_polygon_details');
                ?>
                
                <!-- Define role to detect the supported hook -->
                <!-- {"rule":"customizer","event":"onDisplayViewTkarea","key":"polygon","type":"field"} -->

                <?php   
                /**
                 * Look for any additional fields to be pushed within
                 * the "Polygon" fieldset (left-side).
                 *
                 * NOTE: retrieved from "onDisplayViewTkarea" hook.
                 *
                 * @since 1.9
                 */
                if (isset($this->forms['polygon']))
                {
                    echo $this->forms['polygon'];

                    // unset details form to avoid displaying it twice
                    unset($this->forms['polygon']);
                }
                    
                echo $vik->closeFieldset();
                ?>
            </div>
        </div>

        <!-- ATTRIBUTES -->

        <div class="row-fluid">
            <div class="span12">
                <?php
                echo $vik->openFieldset(JText::translate('VRTKAREAFIELDSET3'), 'form-vertical');
                echo $this->loadTemplate('params_polygon_attributes');
                ?>
                
                <!-- Define role to detect the supported hook -->
                <!-- {"rule":"customizer","event":"onDisplayViewTkarea","key":"polygon.attributes","type":"field"} -->

                <?php   
                /**
                 * Look for any additional fields to be pushed within
                 * the "Attributes" fieldset (left-side).
                 *
                 * NOTE: retrieved from "onDisplayViewTkarea" hook.
                 *
                 * @since 1.9
                 */
                if (isset($this->forms['polygon.attributes']))
                {
                    echo $this->forms['polygon.attributes'];

                    // unset details form to avoid displaying it twice
                    unset($this->forms['polygon.attributes']);
                }
                    
                echo $vik->closeFieldset();
                ?>
            </div>
        </div>

    </div>

</div>

<?php
// render inspector to manage delivery area polygon points
echo JHtml::fetch(
    'vrehtml.inspector.render',
    'tkarea-polygon-inspector',
    array(
        'title'       => JText::translate('VRTKAREATYPE1'),
        'closeButton' => true,
        'keyboard'    => false,
        'footer'      => '<button type="button" class="btn btn-success" data-role="dismiss">' . JText::translate('JTOOLBAR_CLOSE') . '</button>',
    ),
    $this->loadTemplate('params_polygon_modal')
);
?>
