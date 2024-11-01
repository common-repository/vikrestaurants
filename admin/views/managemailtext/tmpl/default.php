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
JHtml::fetch('formbehavior.chosen');

$mailtext = $this->mailtext;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewMailtext". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView();

// Obtain media manager modal before displaying the first field.
// In this way, we can display the modal outside the inspector.
$mediaManagerModal = JHtml::fetch('vrehtml.mediamanager.modal');

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- LEFT SIDE -->
	
		<div class="span8 full-width">

			<!-- DETAILS -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JDETAILS'));
					echo $this->loadTemplate('details');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewMailText","key":"details","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Details" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['details']))
					{
						echo $forms['details'];

						// unset details form to avoid displaying it twice
						unset($forms['details']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- ACTIONS -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMAPACTIONSBUTTON'));
					echo $this->loadTemplate('actions');
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

		<!-- RIGHT SIDE -->
	
		<div class="span4 full-width">

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewMailText","type":"fieldset"} -->

			<?php
			// iterate forms to be displayed within the sidebar panel
			foreach ($forms as $formName => $formHtml): ?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset(JText::translate($formName), 'form-vertical');
						echo $formHtml;
						echo $vik->closeFieldset();
						?>
					</div>
				</div>
			<?php endforeach; ?>

			<!-- FILTERS -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_FILTERS_FIELDSET'));
					echo $this->loadTemplate('filters');
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $mailtext->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';
$footer .= '<button type="button" class="btn" data-role="back" style="float:right;">' . JText::translate('JTOOLBAR_BACK') . '</button>';

echo JHtml::fetch(
	'vrehtml.inspector.render',
	'mailtext-action-inspector',
	[
		'title'       => JText::translate('VRE_MAILTEXT_ADD_ACTION'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 600,
	],
	$this->loadTemplate('action_modal')
);

echo JHtml::fetch(
	'vrehtml.inspector.render',
	'mailtext-filter-inspector',
	[
		'title'       => JText::translate('VRE_MAILTEXT_ADD_FILTER'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 600,
	],
	$this->loadTemplate('filter_modal')
);

echo $mediaManagerModal;
?>

<script>
	(function($) {
		'use strict';

		let validator;

		window.conditionalTextFormSetter = (data, formData, fieldset) => {
			let id     = data.id;
			let params = data.options || {};

			if (!formData.hasOwnProperty(id)) {
				// driver not found
				return params;
			}

			for (let fieldName in formData[id].form) {
				if (!formData[id].form.hasOwnProperty(fieldName)) {
					continue;
				}

				// assign field data to a local variable
				let field = formData[id].form[fieldName];

				// find field node
				let fieldNode = $(fieldset).find('*[name^="' + id + '_' + fieldName + '"]');

				if (field.type === 'media') {
					$(fieldset).find('*[data-name^="' + id + '_' + fieldName + '"]').mediamanager('val', params[fieldName] || null);
				} else if (field.type === 'editor') {
					Joomla.editors.instances[id + '_' + fieldName].setValue(params[fieldName] || '');
				} else if (field.type === 'checkbox') {
					$(fieldNode).prop('checked', params[fieldName] ? true : false);
				} else if (field.type === 'select' || field.type === 'groupedlist') {
					$(fieldNode).updateChosen(params[fieldName] || (field.multiple ? [] : $(fieldNode).find('option').first().val()));
				} else if (field.type === 'date') {
					$(fieldNode).val(params[fieldName] || '').attr('data-alt-value', params[fieldName] || '');
				} else {
					$(fieldNode).val(params[fieldName] || '');
				}
			}
		}

		window.conditionalTextFormGetter = (id, formData, fieldset) => {
			let params = {};

			if (!formData.hasOwnProperty(id)) {
				// driver not found
				return params;
			}

			for (let fieldName in formData[id].form) {
				if (!formData[id].form.hasOwnProperty(fieldName)) {
					continue;
				}

				// assign field data to a local variable
				let field = formData[id].form[fieldName];

				// find field node
				let fieldNode = $(fieldset).find('*[name^="' + id + '_' + fieldName + '"]');

				if (field.type === 'media') {
					params[fieldName] = $(fieldset).find('*[data-name^="' + id + '_' + fieldName + '"]').mediamanager('val');
				} else if (field.type === 'editor') {
					params[fieldName] = Joomla.editors.instances[id + '_' + fieldName].getValue();
				} else if (field.type === 'checkbox') {
					params[fieldName] = $(fieldNode).is(':checked') ? 1 : 0;
				} else {
					params[fieldName] = $(fieldNode).val();
				}
			}

			return params;
		}

		window.conditionalTextFormEditors = (formData) => {
			let editors = [];

			for (let id in formData) {
				if (!formData.hasOwnProperty(id)) {
					continue;
				}

				for (let fieldName in formData[id].form) {
					if (!formData[id].form.hasOwnProperty(fieldName)) {
						continue;
					}

					// assign field data to a local variable
					let field = formData[id].form[fieldName];

					if (field.type === 'editor') {
						editors.push(Joomla.editors.instances[id + '_' + fieldName]);
					}
				}
			}

			return editors;
		}

		$(function() {
			validator = new VikFormValidator('#adminForm');

			VikRenderer.chosen('#mailtext-action-inspector');
			VikRenderer.chosen('#mailtext-filter-inspector');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery);
</script>