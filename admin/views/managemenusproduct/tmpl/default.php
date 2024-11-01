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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.fontawesome');
JHtml::fetch('vrehtml.assets.fancybox');

$product = $this->product;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewMenusproduct". The event method receives the
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

			<!-- PRODUCT -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMENUPRODFIELDSET1'));
					echo $this->loadTemplate('product');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewMenusproduct","key":"product","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Product" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['product']))
					{
						echo $forms['product'];

						// unset details form to avoid displaying it twice
						unset($forms['product']);
					}

					echo $vik->closeFieldset();
					?>	
				</div>

			</div>

			<!-- DESCRIPTION -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGEMENUSPRODUCT3'));

					echo $this->formFactory->createField()
						->type('editor')
						->name('description')
						->value($product->description)
						->hiddenLabel(true);	
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewMenusproduct","key":"description","type":"field"} -->

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

			<!-- PUBLISHING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
					echo $this->loadTemplate('publishing');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewMenusproduct","key":"publishing","type":"field"} -->

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

			<!-- VARIATIONS -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					$help = $vik->createPopover(array(
						'title'   => JText::translate('VRMENUPRODFIELDSET2'),
						'content' => JText::translate('VRE_EDIT_SORT_DRAG_DROP'),
					));

					echo $vik->openFieldset(JText::translate('VRMENUPRODFIELDSET2') . $help, 'form-vertical');
					echo $this->loadTemplate('variations');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewMenusproduct","key":"variations","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Variations" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['variations']))
					{
						echo $forms['variations'];

						// unset details form to avoid displaying it twice
						unset($forms['variations']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewMenusproduct","type":"fieldset"} -->

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

	<?php if ($product->hidden == 1): ?>
		<input type="hidden" name="hidden" value="1" />
	<?php endif; ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $product->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage product variations
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'product-option-inspector',
	array(
		'title'       => JText::translate('VRE_ADD_VARIATION'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 400,
	),
	$this->loadTemplate('variation_modal')
);

?>

<script>
	(function($) {
		'use strict';

		let validator;

		$(function() {
			validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery);
</script>
