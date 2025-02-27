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
 * called "onDisplayViewTkmenuSidebar".
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$sidebarForms = $this->onDisplayView('Sidebar');

?>

<div class="row-fluid">

	<!-- MAIN -->

	<div class="span8 full-width">

		<!-- MENU -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRTKMENUFIELDSET1'));
				echo $this->loadTemplate('details_menu');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkmenu","key":"menu","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed within
				 * the "Take-Away Menu" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewTkmenu" hook.
				 *
				 * @since 1.9
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

		<!-- DESCRIPTION -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGETKMENU2'));

				echo $this->formFactory->createField()
					->type('editor')
					->name('description')
					->value($menu->description)
					->hiddenLabel(true);
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkmenu","key":"description","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed below
				 * the "Description" fieldset (left-side).
				 *
				 * @since 1.9
				 */
				if (isset($forms['description']))
				{
					echo $forms['description'];

					// unset details form to avoid displaying it twice
					unset($forms['description']);
				}
				
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

	<!-- SIDEBAR -->

	<div class="span4 full-width">

		<!-- PUBLISHING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
				echo $this->loadTemplate('details_publishing');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkmenu","key":"publishing","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Publishing" fieldset (sidebar).
				 *
				 * NOTE: retrieved from "onDisplayViewTkmenu" hook.
				 *
				 * @since 1.9
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

		<!-- PUBLISHING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRE_SETTINGS_FIELDSET'), 'form-vertical');
				echo $this->loadTemplate('details_settings');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkmenu","key":"settings","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Settings" fieldset (sidebar).
				 *
				 * NOTE: retrieved from "onDisplayViewTkmenu" hook.
				 *
				 * @since 1.9.1
				 */
				if (isset($this->forms['settings']))
				{
					echo $this->forms['settings'];

					// unset details form to avoid displaying it twice
					unset($this->forms['settings']);
				}

				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkmenuSidebar","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the sidebar (below "Publishing" fieldset).
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
