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

$vik = VREApplication::getInstance();

?>

<div style="padding: 10px;">

	<div class="row-fluid">
		<div class="span12">
			<?php echo $vik->openEmptyFieldset(); ?>

				<!-- ACTION - Select -->

				<?php
				echo $this->formFactory->createField()
					->type('select')
					->name('action')
					->id('vre-create-action-sel')
					->label(JText::translate('VRMANAGEMEDIA5'))
					->options([
						JHtml::fetch('select.option', 'create', JText::translate('VRE_BACKUP_ACTION_CREATE')),
						JHtml::fetch('select.option', 'upload', JText::translate('VRE_BACKUP_ACTION_UPLOAD')),
					]);
				?>

				<!-- TYPE - Select -->

				<?php
				$options = [];
				
				foreach ($this->exportTypes as $id => $type)
				{
					$options[] = JHtml::fetch('select.option', $id, $type->getName());
				}

				echo $this->formFactory->createField()
					->type('select')
					->name('type')
					->value(VREFactory::getConfig()->get('backuptype'))
					->id('vre-create-type-sel')
					->label(JText::translate('VRE_BACKUP_CONFIG_TYPE_LABEL'))
					->description(JText::translate('VRE_BACKUP_CONFIG_TYPE_DESC'))
					->options($options)
					->control([
						'visible' => true,
						'class'   => 'backup-action-create',
					]);
				?>

			<?php echo $vik->closeEmptyFieldset(); ?>
		</div>
	</div>

	<div class="row-fluid backup-action-upload" style="display: none;">
		<div class="span12">
			<?php echo $vik->openEmptyFieldset(); ?>

				<div class="vre-media-droptarget" style="position: relative;">

					<p class="icon">
						<i class="fas fa-upload" style="font-size: 48px;"></i>
					</p>

					<div class="lead">
						<a href="javascript: void(0);" id="upload-file"><?php echo JText::translate('VRE_MANUAL_UPLOAD'); ?></a>&nbsp;<?php echo JText::translate('VRE_BACKUP_DRAGDROP'); ?>
					</div>

					<p class="maxsize">
						<?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', JHtml::fetch('vikrestaurants.maxuploadsize')); ?>
					</p>

					<input type="file" id="legacy-upload" multiple style="display: none;"/>

					<div class="vre-selected-archives" style="position: absolute; bottom: 6px; left: 6px; display: none;">
					
					</div>

					<div class="vre-upload-progress" style="position: absolute; bottom: 6px; right: 6px; display: flex; visibility: hidden;">
						<progress value="0" max="100">0%</progress>
					</div>

				</div>

			<?php echo $vik->closeEmptyFieldset(); ?>
		</div>
	</div>

</div>

<?php
JText::script('VRSYSTEMCONNECTIONERR');
?>

<script>
	(function($) {
		'use strict';

		let dragCounter = 0;
		let file = 0;

		const addFile = (files) => {
			const bar = $('.vre-selected-archives');

			if (files && files.length) {
				file = files[0];
				const badge = $('<span class="badge badge-info"></span>').text(file.name);
				bar.html(badge).show();
			} else {
				file = null;
				bar.hide().html('');
			}
		}

		const saveBackup = (btn) => {
			const formData = new FormData();

			const action = $('#vre-create-action-sel').val();
			formData.append('ajax', 1);
			formData.append('backup_action', action);

			if (action === 'create') {
				formData.append('type', $('#vre-create-type-sel').val());
			} else {
				formData.append('file', file);
			}

			const progressBox = $('.vre-upload-progress');
			progressBox.css('visibility', 'visible');

			$(btn).prop('disabled', true);

			UIAjax.upload(
				// end-point URL
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=backup.save'); ?>',
				// file post data
				formData,
				// success callback
				(resp) => {
					// auto-close the modal
					vrCloseJModal('newbackup');

					// then schedule an auto-refresh by clearing all the filters
					setTimeout(() => {
						clearFilters();
					}, 1000);
				},
				// failure callback
				(error) => {
					alert(error.responseText || Joomla.JText._('VRSYSTEMCONNECTIONERR'));
					$(btn).prop('disabled', false);

					progressBox.css('visibility', 'hidden');
				},
				// progress callback
				(progress) => {
					// update progress
					progressBox.find('progress').val(progress).text(progress + '%');
				}
			);
		}

		$(function() {
			VikRenderer.chosen('#vre-create-action-sel, #vre-create-type-sel');

			$('#vre-create-action-sel').on('change', function() {
				if ($(this).val() === 'create') {
					$('.backup-action-upload').hide();
					$('.backup-action-create').show();
				} else {
					$('.backup-action-create').hide();
					$('.backup-action-upload').show();
				}
			});

			// drag&drop actions on target div

			$('.vre-media-droptarget').on('drag dragstart dragend dragover dragenter dragleave drop', (e) => {
				e.preventDefault();
				e.stopPropagation();
			});

			$('.vre-media-droptarget').on('dragenter', function(e) {
				// increase the drag counter because we may
				// enter into a child element
				dragCounter++;

				$(this).addClass('drag-enter');
			});

			$('.vre-media-droptarget').on('dragleave', function(e) {
				// decrease the drag counter to check if we 
				// left the main container
				dragCounter--;

				if (dragCounter <= 0) {
					$(this).removeClass('drag-enter');
				}
			});

			$('.vre-media-droptarget').on('drop', function(e) {
				$(this).removeClass('drag-enter');
				
				addFile(e.originalEvent.dataTransfer.files);
			});

			$('.vre-media-droptarget #upload-file').on('click', function() {
				// unset selected files before showing the dialog
				$('input#legacy-upload').val(null).trigger('click');
			});

			$('input#legacy-upload').on('change', function() {
				addFile($(this)[0].files);
			});

			$('button[data-role="backup.save"]').on('click', function() {
				saveBackup(this);
			});
		});
	})(jQuery);
</script>