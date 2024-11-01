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

$codeHandlers = $this->codeHub->getCodeHandlers();

$vik = VREApplication::getInstance();

JText::script('VRIMPORT');
JText::script('VREXPORT');

?>

<div class="inspector-form" id="inspector-codehub-block-form">

	<?php echo $vik->bootStartTabSet('codehub_block', ['active' => 'codehub_block_details']); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('codehub_block', 'codehub_block_details', JText::translate('JDETAILS')); ?>

			<div class="inspector-fieldset">

				<?php
				echo $this->formFactory->createField()
					->type('hidden')
					->id('code_block_id')
				?>

				<!-- TITLE - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('code_block_title')
					->required(true)
					->label(JText::translate('VRMANAGETKMENU1'));
				?>

				<!-- EXTENSION - Select -->

				<?php
				$options = [];

				foreach ($codeHandlers as $extension => $handler)
				{
					if ($handler instanceof E4J\VikRestaurants\CodeHub\CodeHandlerDescriptor)
					{
						$extName = $handler->getName();
					}
					else
					{
						$extName = strtoupper($extension);
					}

					$options[] = JHtml::fetch('select.option', $extension, $extName);
				}

				echo $this->formFactory->createField()
					->type('select')
					->id('code_block_extension')
					->label(JText::translate('VRE_CODE_BLOCK_EXTENSION'))
					->class('medium')
					->required(true)
					->value('php')
					->options($options);
				?>

				<!-- DESCRIPTION - Textarea -->

				<?php
				echo $this->formFactory->createField()
					->type('textarea')
					->id('code_block_description')
					->label(JText::translate('VRMANAGETKMENU2'))
					->class('full-width')
					->height(120)
					->style('resize: vertical; max-height: 120px;');
				?>

				<!-- AUTHOR - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('code_block_author')
					->label(JText::translate('VRE_CODE_BLOCK_AUTHOR'));
				?>

				<!-- VERSION - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('code_block_version')
					->label(JText::translate('VRE_CODE_BLOCK_VERSION'));
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- SNIPPET -->

		<?php echo $vik->bootAddTab('codehub_block', 'codehub_block_snippet', JText::translate('VRE_CODE_BLOCK_SNIPPET')); ?>

			<!-- CODE - Text -->

			<?php
			// create an editor for each supported extension because, as far as I know,
			// there is no way to change syntax highlighter without reloading the page
			foreach ($codeHandlers as $extension => $handler)
			{
				echo $this->formFactory->createField()
					->type('editor')
					->name('code_block_editor_' . $extension)
					->editor('codemirror')
					->hiddenLabel(true)
					->buttons(false)
					->params(['syntax' => $extension == 'js' ? 'javascript' : $extension])
					->control([
						'class'   => 'code-block-editor-control ' . $extension . '-extension',
						'visible' => false,
					]);
				}
			?>

		<?php echo $vik->bootEndTab(); ?>

		<!-- IMPORT/EXPORT -->

		<?php echo $vik->bootAddTab('codehub_block', 'codehub_block_import_export', '<span id="codehub_block_import_export_title"></span>'); ?>

			<!-- IMPORT -->

			<div class="vre-media-droptarget codehub-import-control" style="position: relative; display: none;">

				<p class="icon">
					<i class="fas fa-upload" style="font-size: 48px;"></i>
				</p>

				<div class="lead">
					<a href="javascript:void(0)" id="upload-file"><?php echo JText::translate('VRE_MANUAL_UPLOAD'); ?></a>&nbsp;<?php echo JText::translate('VRE_BACKUP_DRAGDROP'); ?>
				</div>

				<p class="maxsize">
					<?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', JHtml::fetch('vikrestaurants.maxuploadsize')); ?>
				</p>

				<input type="file" id="legacy-upload" multiple style="display: none;" />

				<div class="vre-upload-progress" style="position: absolute; bottom: 6px; right: 6px; display: flex; visibility: hidden;">
					<progress value="0" max="100">0%</progress>
				</div>

			</div>

			<!-- EXPORT -->

			<?php
			echo $this->formFactory->createField()
				->type('button')
				->id('code_block_download_link')
				->text(JText::translate('VRDOWNLOAD'))
				->description(JText::translate('VRE_CODE_BLOCK_EXPORT_DESC'))
				->hiddenLabel(true)
				->control([
					'class'   => 'codehub-export-control',
					'visible' => false,
				]);
			?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>

</div>

<script>
	(function($, w) {
		'use strict';

		w.fillCodeBlockForm = (data) => {
			// set up ID
			$('#code_block_id').val(data.id || '');

			// set up title
			$('#code_block_title').val(data.title || '');

			// set up file extension (cannot be changed on update)
			$('#code_block_extension').select2('val', data.extension || 'php').trigger('change').prop('disabled', data.extension ? true : false);

			// set up description
			$('#code_block_description').val(data.description || '');

			// set up author
			$('#code_block_author').val(data.author || '');

			// set up version
			$('#code_block_version').val(data.version || '');

			// reset all editors
			for (let editorName in Joomla.editors.instances)
			{
				let match = editorName.match(/^code_block_editor_([a-z0-9]+)/);

				if (match && match.length) {					
					if (match[1] === 'php') {
						/**
						 * SmartSVN seems to have problems in compiling an escaped declaration of PHP.
						 * Therefore we should decode the "<" character at runtime to make sure the
						 * compiler excludes the following code as part of PHP code.
						 */
						Joomla.editors.instances[editorName].setValue('<?php echo html_entity_decode('&lt;') . "?php"; ?>\n');
					} else {
						Joomla.editors.instances[editorName].setValue('');
					}
				}
			}

			// set up code
			if (data.code) {
				Joomla.editors.instances['code_block_editor_' + (data.extension || 'php')].setValue(data.code);
			}

			if (data.title) {
				$('#codehub_block_import_export_title').text(Joomla.JText._('VREXPORT'));
				$('.codehub-import-control').hide();
				$('.codehub-export-control').show();
			} else {
				$('#codehub_block_import_export_title').text(Joomla.JText._('VRIMPORT'));
				$('.codehub-export-control').hide();
				$('.codehub-import-control').show();
			}

			<?php
			/**
			 * In WordPress the codemirror seems to have rendering problems while
			 * initialized on a hidden panel. For this reason, we need to refresh
			 * its contents when the inspector is open, as the editor pane might
			 * be immediately visible.
			 * @wponly
			 */
			if (VersionListener::isWordpress()): ?>
				setTimeout(() => {
					Joomla.editors.instances['code_block_editor_' + (data.extension || 'php')].element.codemirror.refresh();
				}, 64);
			<?php endif; ?>
		}

		w.getCodeBlockData = () => {
			const data = {};

			// set up ID
			data.id = $('#code_block_id').val();

			// set up title
			data.title = $('#code_block_title').val();

			// set up file extension
			data.extension = $('#code_block_extension').select2('val');

			// set up description
			data.description = $('#code_block_description').val();

			// set up author
			data.author = $('#code_block_author').val();

			// set up version
			data.version = $('#code_block_version').val();

			// set up code
			data.code = Joomla.editors.instances['code_block_editor_' + data.extension].getValue();
			 
			return data;
		}

		let dragCounter = 0;
		let isImporting = false;

		const importCodeBlock = (file) => {
			if (isImporting) {
				return;
			}

			isImporting = true;

			const formData = new FormData();
			formData.append('file', file);

			const progressBox = $('.vre-upload-progress');
			progressBox.css('visibility', 'visible');

			UIAjax.upload(
				// end-point URL
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=codehub.import'); ?>',
				// file post data
				formData,
				// success callback
				(data) => {
					isImporting = false;

					// auto-fill the form
					fillCodeBlockForm(data);
					// auto-save the code block (commit the changes first to bypass the confirm dialog)
					$('#codehub-blocks-inspector').trigger('inspector.commit').trigger('inspector.save');

					progressBox.css('visibility', 'hidden');
				},
				// failure callback
				(error) => {
					isImporting = false;

					alert(error.responseText || Joomla.JText._('VRSYSTEMCONNECTIONERR'));

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
			w.codeBlockValidator = new VikFormValidator('#inspector-codehub-block-form');

			w.codeBlockValidator.addCallback(() => {
				// fetch currently selected extension
				const extension = $('#code_block_extension').select2('val');

				// fetch code from the editor assigned to the selected extension
				const code = Joomla.editors.instances['code_block_editor_' + extension].getValue();

				// make sure we have some code
				return code;
			});

			$('#code_block_extension').on('change', function() {
				const extension = $(this).select2('val');

				$('.code-block-editor-control').hide();
				$('.' + extension + '-extension').show();
			});

			$('#code_block_download_link').on('click', () => {
				$('#codehub-blocks-inspector').trigger('inspector.export');
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
				
				importCodeBlock(e.originalEvent.dataTransfer.files[0]);
			});

			$('.vre-media-droptarget #upload-file').on('click', function() {
				// unset selected files before showing the dialog
				$('input#legacy-upload').val(null).trigger('click');
			});

			$('input#legacy-upload').on('change', function() {
				importCodeBlock($(this)[0].files[0]);
			});

			<?php
			/**
			 * In WordPress the codemirror seems to have rendering problems while
			 * initialized on a hidden panel. For this reason, we need to refresh
			 * its contents when the editor panel is clicked.
			 * @wponly
			 */
			if (VersionListener::isWordpress()): ?>
				$('#codehub_blockTabs a[href="#codehub_block_snippet"]').on('click', () => {
					setTimeout(() => {
						const editorName = 'code_block_editor_' + $('#code_block_extension').select2('val');
						Joomla.editors.instances[editorName].element.codemirror.refresh();
					}, 64);
				});
			<?php endif; ?>
		});

	})(jQuery, window);
</script>