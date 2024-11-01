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

JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.fontawesome');

$topping = $this->topping;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTktopping". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$forms = $this->onDisplayView();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- LEFT SIDE -->
	
		<div class="span8 full-width">

			<!-- TOPPING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRTOPPING'));
					echo $this->loadTemplate('topping');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTktopping","key":"topping","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Topping" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['topping']))
					{
						echo $forms['topping'];

						// unset details form to avoid displaying it twice
						unset($forms['topping']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- DESCRIPTION -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGELANG3'));
					echo $this->loadTemplate('description');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTktopping","key":"description","type":"field"} -->

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

		<!-- RIGHT SIDE -->
	
		<div class="span4 full-width">

			<!-- PRICING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_PRICING_FIELDSET'), 'form-vertical');
					echo $this->loadTemplate('pricing');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTktopping","key":"pricing","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Pricing" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['pricing']))
					{
						echo $forms['pricing'];

						// unset details form to avoid displaying it twice
						unset($forms['pricing']);
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
					echo $this->loadTemplate('publishing');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTktopping","key":"publishing","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Publishing" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['publishing']))
					{
						echo $forms['publishing'];

						// unset details form to avoid displaying it twice
						unset($forms['publishing']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewTktopping","type":"fieldset"} -->

			<?php
			// iterate forms to be displayed within the sidebar panel
			foreach ($forms as $formName => $formHtml)
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

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $topping->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
if ($this->products)
{
	// render inspector to manage the price quick update
	echo JHtml::fetch(
		'vrehtml.inspector.render',
		'price-quick-update-inspector',
		array(
			'title'       => JText::translate('VRMANAGETKTOPPING6'),
			'closeButton' => true,
			'keyboard'    => false,
			'width'       => 600,
			'footer'      => '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>',
		),
		$this->loadTemplate('pricing_modal')
	);
}
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			w.validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>