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

$entry = $this->entry;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTkentrySidebar".
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$sidebarForms = $this->onDisplayView('Sidebar');

?>

<div class="row-fluid">

	<!-- MAIN -->

	<div class="span8 full-width">

		<!-- ENTRY -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGETKSTOCK1'));
				echo $this->loadTemplate('details_product');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkentry","key":"product","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed within
				 * the "Product" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewTkentry" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['product']))
				{
					echo $this->forms['product'];

					// unset details form to avoid displaying it twice
					unset($this->forms['product']);
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
					->value($entry->description)
					->hiddenLabel(true);
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkentry","key":"description","type":"field"} -->

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

		<!-- PRICING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRE_PRICING_FIELDSET'), 'form-vertical');
				echo $this->loadTemplate('details_pricing');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkentry","key":"pricing","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Pricing" fieldset (sidebar).
				 *
				 * NOTE: retrieved from "onDisplayViewTkentry" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['pricing']))
				{
					echo $this->forms['pricing'];

					// unset details form to avoid displaying it twice
					unset($this->forms['pricing']);
				}

				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- PUBLISHING -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
				echo $this->loadTemplate('details_publishing');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkentry","key":"publishing","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Publishing" fieldset (sidebar).
				 *
				 * NOTE: retrieved from "onDisplayViewTkentry" hook.
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

		<!-- PROPERTIES -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMAPPROPERTIESBUTTON'), 'form-vertical');
				echo $this->loadTemplate('details_properties');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkentry","key":"properties","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Properties" fieldset (sidebar).
				 *
				 * NOTE: retrieved from "onDisplayViewTkentry" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['properties']))
				{
					echo $this->forms['properties'];

					// unset details form to avoid displaying it twice
					unset($this->forms['properties']);
				}

				echo $vik->closeFieldset();
				?>
			</div>
		</div>

		<!-- STOCKS -->

		<?php if (VREFactory::getConfig()->getBool('tkenablestock')): ?>
			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGECONFIGTKSECTION2'), 'form-vertical');
					echo $this->loadTemplate('details_stocks');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTkentry","key":"stocks","type":"field"} -->
					
					<?php
					/**
					 * Look for any additional fields to be pushed within
					 * the "Stocks" fieldset (sidebar).
					 *
					 * NOTE: retrieved from "onDisplayViewTkentry" hook.
					 *
					 * @since 1.9
					 */
					if (isset($this->forms['stocks']))
					{
						echo $this->forms['stocks'];

						// unset details form to avoid displaying it twice
						unset($this->forms['stocks']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkentrySidebar","type":"fieldset"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the right sidebar.
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
