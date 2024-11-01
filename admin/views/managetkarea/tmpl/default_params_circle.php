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
				echo $this->loadTemplate('params_circle_map');
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

	<!-- LEFT SIDE -->

	<div class="span4 full-width">

		<!-- CIRCLE -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRTKAREATYPE2'), 'form-vertical');
				echo $this->loadTemplate('params_circle_details');
				?>
				
				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkarea","key":"circle","type":"field"} -->

				<?php   
				/**
				 * Look for any additional fields to be pushed within
				 * the "Circle" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewTkarea" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['circle']))
				{
					echo $this->forms['circle'];

					// unset details form to avoid displaying it twice
					unset($this->forms['circle']);
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
				echo $this->loadTemplate('params_circle_attributes');
				?>
				
				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkarea","key":"circle.attributes","type":"field"} -->

				<?php   
				/**
				 * Look for any additional fields to be pushed within
				 * the "Attributes" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewTkarea" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['circle.attributes']))
				{
					echo $this->forms['circle.attributes'];

					// unset details form to avoid displaying it twice
					unset($this->forms['circle.attributes']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

</div>
