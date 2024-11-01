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

$tax = $this->tax;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTax". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php if ($this->isTmpl): ?>
		<div class="btn-toolbar vr-btn-toolbar" style="display:none;">
			<div class="btn-group pull-left">
				<button type="button" class="btn btn-success" name="tmplSaveButton" onclick="taxSaveButtonPressed(this);">
					<i class="icon-apply"></i>&nbsp;<?php echo JText::translate('VRSAVE'); ?>
				</button>
			</div>
		</div>
	<?php endif; ?>

	<?php echo $vik->openCard(); ?>

		<!-- MAIN -->

		<div class="span8">

			<!-- TAX -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRINVOICEFIELDSET2'));
					echo $this->loadTemplate('tax');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTax","key":"tax","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Details" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['tax']))
					{
						echo $forms['tax'];

						// unset details form to avoid displaying it twice
						unset($forms['tax']);
					}
						
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- DESCRIPTION -->

			<div class="row-fluid">
				<div class="span12">
					<?php echo $vik->openFieldset(JText::translate('VRINVITEMDESC'), 'form-vertical'); ?>

					<textarea name="description" class="full-width" style="height: 160px; resize: vertical;"><?php echo $tax->description; ?></textarea>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTax","key":"description","type":"field"} -->
					
					<?php
					/**
					 * Look for any additional fields to be pushed within
					 * the "Description" fieldset (left-side).
					 *
					 * @since 1.7
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

		<div class="span4">

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewTax","type":"fieldset"} -->

			<?php
			/**
			 * Iterate remaining forms to be displayed within
			 * the sidebar (above "Rules" fieldset).
			 *
			 * @since 1.9
			 */
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

			<!-- RULES -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRETAXRULEFIELDSET'));
					echo $vik->alert(JText::translate('VRE_EDIT_SORT_DRAG_DROP'), 'info');
					echo $this->loadTemplate('rules');
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>

	<?php if ($this->isTmpl): ?>
		<input type="hidden" name="tmpl" value="component" />
	<?php endif; ?>

	<input type="hidden" name="id" value="<?php echo (int) $tax->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage tax rules
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'tax-rule-inspector',
	array(
		'title'       => JText::translate('VRMANAGECUSTOMF11'),
		'closeButton' => true,
		'keyboard'    => true,
		'footer'      => $footer,
		'width'       => 500,
	),
	$this->loadTemplate('rules_modal')
);
?>

<script>
	(function($, w) {
		'use strict';

		let validator;

		w.taxSaveButtonPressed = (button) => {
			if ($(button).prop('disabled')) {
				// button already submitted
				return false;
			}

			if (!validator.validate()) {
				// missing required field
				return false;
			}

			// disable button to prevent duplicate submissions
			$(button).prop('disabled', true);

			// DO NOT use submit button to avoid validating the form again
			Joomla.submitform('tax.save', document.adminForm);
		}

		<?php if ($this->isTmpl): ?>
			// transfer submit button instance to parent for being clicked
			window.parent.modalTaxSaveButton = document.adminForm.tmplSaveButton;

			<?php if ($tax->id): ?>
				// transfer saved tax to parent
				window.parent.modalSavedTax = <?php echo json_encode($tax); ?>;
			<?php endif; ?>
		<?php endif; ?>

		$(function() {
			validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>