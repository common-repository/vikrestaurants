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

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTkareaSidebar".
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$sidebarForms = $this->onDisplayView('Sidebar');

?>

<div class="row-fluid">

	<!-- MAIN -->

	<div class="<?php echo ($sidebarForms ? 'span8' : 'span12'); ?> full-width">

		<!-- AREA -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRTKAREAFIELDSET1'));
				echo $this->loadTemplate('details_area');
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewTkarea","key":"area","type":"field"} -->

				<?php	
				/**
				 * Look for any additional fields to be pushed within
				 * the "Area Details" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewTkarea" hook.
				 *
				 * @since 1.9
				 */
				if (isset($this->forms['area']))
				{
					echo $this->forms['area'];

					// unset details form to avoid displaying it twice
					unset($this->forms['area']);
				}
					
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

	<!-- SIDEBAR -->

	<?php if ($sidebarForms): ?>
		<div class="span4 full-width">

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewTkareaSidebar","type":"fieldset"} -->

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
	<?php endif; ?>

</div>
