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

$data = array(
	'name'   => basename($this->item->id),
	'path'   => $this->item->id,
	'base64' => base64_encode($this->item->id),
);

$vik = VREApplication::getInstance();

?>

<style>
	.vr-file-box .CodeMirror {
		height: 100% !important;
		max-height: 100% !important;
	}
</style>

<form action="index.php" method="POST" name="adminForm" id="adminForm">

	<?php if ($this->blank): ?>
		<div class="btn-toolbar vr-btn-toolbar" style="display: none;">
			<div class="btn-group pull-left">
				<button type="button" class="btn btn-success" name="tmplSaveButton" onclick="fileSaveButtonPressed(this);">
					<i class="icon-apply"></i>&nbsp;<?php echo JText::translate('VRSAVE'); ?>
				</button>

				<button type="button" class="btn btn-success" name="tmplSaveCopyButton" onclick="fileSaveAsCopyButtonPressed(this);">
					<i class="icon-apply"></i>&nbsp;<?php echo JText::translate('VRSAVE'); ?>
				</button>
			</div>
		</div>
	<?php endif; ?>

	<?php
	if ($this->blank)
	{
		?><div class="managefile-wrapper" style="padding: 10px;"><?php
	}
	else
	{
		echo $vik->openCard();
	}
	?>
	
		<div class="vr-file-path">
			<?php
			echo $this->formFactory->createField()
				->type('text')
				->value(basename($this->item->id))
				->readonly(true)
				->hiddenLabel(true);
			?>
		</div>
		
		<div class="vr-file-box">
			<?php
			echo $this->formFactory->createField()
				->type('editor')
				->name('content')
				->value($this->item->content)
				->editor('codemirror')
				->buttons(false)
				->hiddenLabel(true);
			?>
		</div>

	<?php
	if ($this->blank)
	{
		?></div><?php
	}
	else
	{
		echo $vik->closeCard();
	}
	?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="file" value="<?php echo $this->escape(base64_encode($this->item->id)); ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />

	<?php if ($this->blank): ?>
		<input type="hidden" name="tmpl" value="component" />
	<?php endif; ?>
	
</form>

<?php
JText::script('VREXPORTRES1');
?>

<script>
	(function($, w) {
		'use strict';

		w.fileSaveButtonPressed = (button) => {
			if ($(button).prop('disabled')) {
				// button already submitted
				return false;
			}

			// disable button
			$(button).prop('disabled', true);

			<?php
			/**
			 * In WordPress the codemirror seems to have rendering problems while
			 * initialized on a hidden panel. For this reason, we need to refresh
			 * its contents when the modal is displayed.
			 * @wponly
			 */
			if (VersionListener::isWordpress()): ?>
				Joomla.editors.instances.content.element.codemirror.save();
			<?php endif; ?>

			Joomla.submitform('file.save', document.adminForm);
		}

		w.fileSaveAsCopyButtonPressed = (button) => {
			if ($(button).prop('disabled')) {
				// button already submitted
				return false;
			}

			// disable button
			$(button).prop('disabled', true);

			// ask for the new name
			var name = prompt(Joomla.JText._('VREXPORTRES1'), 'file.php');

			if (!name) {
				// invalid name
				return false;
			}

			<?php
			/**
			 * In WordPress the codemirror seems to have rendering problems while
			 * initialized on a hidden panel. For this reason, we need to refresh
			 * its contents when the modal is displayed.
			 * @wponly
			 */
			if (VersionListener::isWordpress()): ?>
				Joomla.editors.instances.content.element.codemirror.save();
			<?php endif; ?>

			if (!name.match(/\.php$/i)) {
				// append ".php" if not specified
				name += '.php';
			}

			$('#adminForm').append('<input type="hidden" name="dir" value="<?php echo base64_encode(dirname($this->item->id)); ?>" />');
			$('#adminForm').append('<input type="hidden" name="filename" value="' + name + '" />');

			Joomla.submitform('file.savecopy', document.adminForm);
		}

		<?php if ($this->blank): ?>
			// transfer submit buttons instances to parent for being clicked
			w.parent.modalFileSaveButton     = document.adminForm.tmplSaveButton;
			w.parent.modalFileSaveCopyButton = document.adminForm.tmplSaveCopyButton;

			// transfer saved file path to parent
			w.parent.modalSavedFile = <?php echo json_encode($data); ?>;
		<?php else: ?>
			Joomla.submitbutton = function(task) {
				if (task == 'file.savecopy') {
					fileSaveAsCopyButtonPressed(null);
				} else {
					Joomla.submitform(task, document.adminForm);
				}
			}
		<?php endif; ?>
	})(jQuery, window);
</script>