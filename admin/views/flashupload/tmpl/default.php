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

<div class="vre-flashupload">

	<div class="control">
		<div class="vre-media-droptarget"><?php echo JText::translate('VRMEDIADRAGDROP'); ?></div>
	</div>

	<div class="control" id="vr-uploads-cont" style="margin-top: 20px;"></div>

</div>

<script>
	(function($, w) {
		'use strict';

		const vreDispatchMediaUploads = (files) => {
			const up_cont = $('#vr-uploads-cont');

			for (let i = 0; i < files.length; i++) {
				if (files[i].name.match(/\.(png|jpe?g|gif|bmp)$/)) {
					let status = new createStatusBar();
					status.setFileNameSize(files[i].name, files[i].size);
					status.setProgress(0);
					up_cont.append(status.getHtml());
					
					vreMediaFileUploadThread(status, files[i]);
				} else {
					alert('File [' + files[i].name + '] not supported');
				}
			}
		}

		const vreMediaFileUploadThread = (status, file) => {
			let formData = new FormData();
			formData.append('file', file);

			const xhr = UIAjax.upload(
				// end-point URL
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=media.dropupload'); ?>',
				// file post data
				formData,
				// success callback
				(resp) => {
					if (resp) {
						status.complete();
						status.filename.html(resp.name);

						// register uploaded image in parent pool, if specified
						if (typeof w.parent.UPLOADED_MEDIAS !== 'undefined') {
							w.parent.UPLOADED_MEDIAS.push(resp.uri);
						}
					} else {
						status.progressBar.find('div').addClass('aborted');
					}
				},
				// failure callback
				(error) => {
					status.progressBar.find('div').addClass('aborted');
				},
				// progress callback
				(progress) => {
					// update progress
					status.setProgress(progress);
				}
			);

			status.setAbort(xhr);
		}
		
		let fileCount = 0;

		function createStatusBar() {
			fileCount++;
			this.statusbar = $("<div class='vr-progressbar-status'></div>");
			this.filename = $("<div class='vr-progressbar-filename'></div>").appendTo(this.statusbar);
			this.size = $("<div class='vr-progressbar-filesize hidden-phone'></div>").appendTo(this.statusbar);
			this.progressBar = $("<div class='vr-progressbar'><div></div></div>").appendTo(this.statusbar);
			this.abort = $("<div class='vr-progressbar-abort hidden-phone'>Abort</div>").appendTo(this.statusbar);
			this.statusinfo = $("<div class='vr-progressbar-info hidden-phone' style='display:none;'><?php echo addslashes(JText::translate('VRMANAGEMEDIA9')); ?></div>").appendTo(this.statusbar);
			this.completed = false;
		 
			this.setFileNameSize = function(name, size) {
				let sizeStr = "";
				if(size > 1024*1024) {
					let sizeMB = size/(1024*1024);
					sizeStr = sizeMB.toFixed(2)+" MB";
				} else if(size > 1024) {
					let sizeKB = size/1024;
					sizeStr = sizeKB.toFixed(2)+" kB";
				} else {
					sizeStr = size.toFixed(2)+" B";
				}
		 
				this.filename.html(name);
				this.size.html(sizeStr);
			}
			
			this.setProgress = function(progress) {       
				let progressBarWidth = progress*this.progressBar.width()/100;  
				this.progressBar.find('div').css('width', progressBarWidth+'px').html(progress + "% ");
				if (parseInt(progress) >= 100) {
					if (!this.completed) {
						this.abort.hide();
						this.statusinfo.show();
					}
				}
			}
			
			this.complete = function() {
				this.completed = true;
				this.abort.hide();
				this.statusinfo.hide();
				this.setProgress(100);
				this.progressBar.find('div').addClass('completed');
			}
			
			this.setAbort = function(jqxhr) {
				const bar = this.progressBar;
				this.abort.click(function() {
					jqxhr.abort();
					this.hide();
					bar.find('div').addClass('aborted');
				});
			}
			
			this.getHtml = function() {
				return this.statusbar;
			}
		}

		// files upload

		$(function() {
			let dragCounter = 0;

			// drag&drop actions on target div

			$('.vre-media-droptarget').on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
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
				vreDispatchMediaUploads(e.originalEvent.dataTransfer.files);
			});

			$('.vre-media-droptarget #upload-file').on('click', function() {
				// unset selected files before showing the dialog
				$('input#legacy-upload').val(null).trigger('click');
			});

			$('input#legacy-upload').on('change', function() {
				// execute AJAX uploads after selecting the files
				vreDispatchMediaUploads($(this)[0].files);
			});
		});

	})(jQuery, window);
</script>