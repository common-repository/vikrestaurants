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

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewMenuDetails".
 * It is also possible to use "onDisplayViewMenuDetailsSidebar"
 * to include any additional fieldsets within the right sidebar.
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$detailsForms = $this->onDisplayView('Details');
$sidebarForms = $this->onDisplayView('DetailsSidebar');

?>

<div class="row-fluid">

	<!-- MAIN -->

	<div class="span7">

		<!-- MENU -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRE_MENU'));
				echo $this->loadTemplate('details_menu');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewMenu","key":"menu","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed within
				 * the "Menu" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewMenu" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['menu']))
				{
					echo $this->forms['menu'];

					// unset details form to avoid displaying it twice
					unset($this->forms['menu']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewMenuDetails","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the main panel.
		 *
		 * @since 1.9
		 */
		foreach ($detailsForms as $formName => $formHtml)
		{
			$title = JText::translate($formName);
			?>
			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset($title);
					echo $formHtml;
					echo $vik->closeFieldset();
					?>
				</div>
			</div>
			<?php
		}
		?>

		<!-- DESCRIPTION -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGEMENU17'));

				echo $this->formFactory->createField()
					->type('editor')
					->name('description')
					->value($menu->description)
					->hiddenLabel(true);
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewMenu","key":"description","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Description" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewService" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['description']))
				{
					echo $this->forms['description'];

					// unset details form to avoid displaying it twice
					unset($this->forms['description']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

	<!-- SIDEBAR -->

	<div class="span5 full-width">

		<!-- PUBLISHING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
				echo $this->loadTemplate('details_publishing');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewMenu","key":"publishing","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed within
				 * the "Publishing" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewMenu" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['publishing']))
				{
					echo $this->forms['publishing'];

					// unset details form to avoid displaying it twice
					unset($this->forms['publishing']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- BOOKING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRE_BOOKING_FIELDSET'), 'form-vertical');
				echo $this->loadTemplate('details_booking');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewMenu","key":"booking","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed within
				 * the "Booking" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewMenu" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['booking']))
				{
					echo $this->forms['booking'];

					// unset details form to avoid displaying it twice
					unset($this->forms['booking']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewMenuDetailsSidebar","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the sidebar (below "Parameters" fieldset).
		 *
		 * @since 1.9
		 */
		foreach ($sidebarForms as $formName => $formHtml)
		{
			$title = JText::translate($formName);
			?>
			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset($title, 'form-vertical');
					echo $formHtml;
					echo $vik->closeFieldset();
					?>
				</div>
			</div>
			<?php
		}
		?>

	</div>

</div>
