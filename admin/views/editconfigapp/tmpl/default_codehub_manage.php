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

$codeBlocks   = $this->codeHub->load();
$codeHandlers = $this->codeHub->getCodeHandlers();

$codeBlockLayout = new JLayoutFile('blocks.card');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<div class="vre-cards-container cards-codehub-blocks" id="cards-codehub-blocks">

			<?php foreach ($codeBlocks as $i => $codeBlock): ?>
				<div class="vre-card-fieldset up-to-3" id="codehub-block-fieldset-<?php echo (int) $i; ?>">

					<?php
					$displayData = [];

					// fetch primary text
					$displayData['primary'] = $codeBlock->getTitle();

					if ($descr = $codeBlock->getDescription())
					{
						$displayData['primary'] .= '<i class="fas fa-info-circle hasTooltip" style="margin-left: 4px;" title="' . $this->escape($descr) . '"></i>';
					}
				
					// fetch secondary text
					$displayData['secondary'] = '<span class="badge badge-info">' . $codeBlock->getAuthor() . '</span>'
						. '<span class="badge badge-success">' . $codeBlock->getVersion() . '</span>';

					$codeHandler = $codeHandlers[$codeBlock->getExtension()];

					if ($codeHandler instanceof E4J\VikRestaurants\CodeHub\CodeHandlerDescriptor)
					{
						$icon = $codeHandler->getIcon();
					}
					else
					{
						$icon = 'fas fa-file-code';
					}

					// fetch badge
					$displayData['badge'] = '<i class="' . $icon . '"></i>';

					// fetch edit button
					$displayData['edit'] = 'vreOpenCodeBlockCard(\'' . $i . '\');';

					// render layout
					echo $codeBlockLayout->render($displayData);
					?>
					
					<input type="hidden" class="code_block_json" value="<?php echo $this->escape(json_encode($codeBlock)); ?>" />

				</div>
			<?php endforeach; ?>

			<!-- ADD PLACEHOLDER -->

			<div class="vre-card-fieldset up-to-3 add add-codehub-block">
				<div class="vre-card compress">
					<i class="fas fa-plus"></i>
				</div>
			</div>

		</div>

		<div style="display:none;" id="codehub-block-struct">
	
			<?php
			// create code block structure for new items
			$displayData = array();
			$displayData['class']     = '';
			$displayData['primary']   = '';
			$displayData['secondary'] = '';
			$displayData['badge']     = '<i class="fas fa-file-code"></i>';
			$displayData['edit']      = true;

			echo $codeBlockLayout->render($displayData);
			?>

		</div>

	</div>
	
</div>

<style>
	@media screen and (max-width: 1200px) {
		#codehub-blocks-inspector {
			min-width: 100% !important;
			max-width: 100% !important;
			width: 100% !important;
		}
	}
</style>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage code blocks
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'codehub-blocks-inspector',
	array(
		'title'       => JText::translate('VRE_CODEHUB_BLOCK'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 1200,
	),
	$this->loadTemplate('codehub_modal')
);

$jsonCodeHandlers = [];

foreach ($codeHandlers as $extension => $codeHandler)
{
	$jsonCodeHandlers[$extension] = $codeHandler instanceof E4J\VikRestaurants\CodeHub\CodeHandlerDescriptor
		? $codeHandler->getIcon()
		: 'fas fa-file-code';
}

JText::script('VRCONNECTIONLOST');
?>

<script>
	(function($, w) {
		'use strict';

		let CODE_BLOCKS_COUNT   = <?php echo count($codeBlocks); ?>;
		let SELECTED_CODE_BLOCK = null;

		let codeBlockFormObserver;

		// register a reference to the observer used by the inspector
		$(document).on('inspector.observer.init', '#codehub-blocks-inspector', (event) => {
			codeBlockFormObserver = event.observer;
		});

		$(function() {
			// open inspector for new code blocks
			$('.vre-card-fieldset.add-codehub-block').on('click', () => {
				vreOpenCodeBlockCard();
			});

			// fill the form before showing the inspector
			$('#codehub-blocks-inspector').on('inspector.show', () => {
				let json = [];

				// fetch JSON data
				if (SELECTED_CODE_BLOCK) {
					const fieldset = $('#' + SELECTED_CODE_BLOCK);

					json = fieldset.find('input.code_block_json').val();

					try {
						json = JSON.parse(json);
					} catch (err) {
						json = {};
					}
				}

				if (json.title === undefined) {
					// creating new record, hide delete button
					$('#codehub-blocks-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#codehub-blocks-inspector [data-role="delete"]').show();
				}

				fillCodeBlockForm(json);
			});

			// apply the changes
			$('#codehub-blocks-inspector').on('inspector.save', function() {
				// validate form
				if (!codeBlockValidator.validate()) {
					return false;
				}

				// get saved record
				let data = getCodeBlockData();

				// prevent duplicate requests
				$(this).find('button[data-role="save"]').prop('disabled', true);

				generateCodeBlockID(data).then((id) => {
					data.id = id;

					let extCodeBlocks = getExtensionSnippets(data.extension);
					extCodeBlocks.push(data);

					// attempt to save the code blocks
					saveCodeBlocks(data.extension, extCodeBlocks).then(() => {
						let fieldset;

						if (SELECTED_CODE_BLOCK) {
							fieldset = $('#' + SELECTED_CODE_BLOCK);
						} else {
							fieldset = vreAddCodeBlockCard(data);
						}

						if (fieldset.length == 0) {
							// an error occurred, abort
							return false;
						}

						// save JSON data
						fieldset.find('input.code_block_json').val(JSON.stringify(data));

						// refresh card details
						vreRefreshCodeBlockCard(fieldset, data);

						// auto-close on save
						$(this).inspector('dismiss');
					}).catch((error) => {
						alert(error);
					}).finally(() => {
						// restore save button
						$(this).find('button[data-role="save"]').prop('disabled', false);
					});
				}).catch((error) => {
					alert(error);
					// restore save button
					$(this).find('button[data-role="save"]').prop('disabled', false);
				});
			});

			// delete the record
			$('#codehub-blocks-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_CODE_BLOCK);

				// get saved record
				let data = JSON.parse(fieldset.find('input.code_block_json').val());

				// Get all the blocks of the same extension.
				// Since the active block won't be included, the system will
				// rewrite the custom file without this one.
				let extCodeBlocks = getExtensionSnippets(data.extension);

				// prevent duplicate requests
				$(this).find('button[data-role="delete"]').prop('disabled', true);

				// attempt to save the code blocks
				saveCodeBlocks(data.extension, extCodeBlocks).then(() => {
					// auto delete fieldset
					fieldset.remove();

					// auto-close on delete
					$(this).inspector('dismiss');
				}).catch((error) => {
					alert(error);
				}).finally(() => {
					// restore delete button
					$(this).find('button[data-role="delete"]').prop('disabled', false);
				});
			});

			// export the code block
			$('#codehub-blocks-inspector').on('inspector.export', function() {
				if (codeBlockFormObserver && codeBlockFormObserver.isChanged()) {
					// something has changed, warn the user about the
					// possibility of losing any changes
					let discard = confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'));

					if (!discard) {
						return false;
					}
				}

				// get selected record data
				let data = getCodeBlockData();

				const exportURL = '<?php echo VREFactory::getPlatform()->getUri()->addCSRF('index.php?option=com_vikrestaurants&task=codehub.export&id=%s'); ?>';

				// export code block
				w.open(exportURL.replace(/%s/, data.id), '_blank');
			});
		});

		w.vreOpenCodeBlockCard = (index) => {
			if (typeof index !== 'undefined') {
				SELECTED_CODE_BLOCK = 'codehub-block-fieldset-' + index;
			} else {
				SELECTED_CODE_BLOCK = null;
			}

			// open inspector
			vreOpenInspector('codehub-blocks-inspector');
		}

		const vreAddCodeBlockCard = (data) => {
			let index = CODE_BLOCKS_COUNT++;

			SELECTED_CODE_BLOCK = 'codehub-block-fieldset-' + index;

			let html = $('#codehub-block-struct').clone().html();

			html = html.replace(/{id}/, index);

			$('#cards-codehub-blocks').prepend('<div class="vre-card-fieldset" id="codehub-block-fieldset-' + index + '">' + html + '</div>');

			// get created fieldset
			let fieldset = $('#' + SELECTED_CODE_BLOCK);

			fieldset.vrecard('edit', 'vreOpenCodeBlockCard(' + index + ')');

			// create input to hold JSON data
			let input = $('<input type="hidden" class="code_block_json" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshCodeBlockCard = (elem, data) => {
			let primary = $('<span></span>').text(data.title);
			
			if (data.description.length) {
				primary = primary.add(
					$('<i class="fas fa-info-circle code-descr-tip" style="margin-left: 4px;"></i>').attr('title', data.description).tooltip()
				);
			}

			// update primary text
			elem.vrecard('primary', primary);

			// append secondary text
			let secondary = [];

			if (data.author) {
				secondary.push($('<span class="badge badge-info"></span>').text(data.author));
			}

			if (data.version) {
				secondary.push($('<span class="badge badge-success"></span>').text(data.version));
			}

			if (secondary) {
				// update secondary text
				elem.vrecard('secondary', secondary);
			}

			const iconsLookup = <?php echo json_encode($jsonCodeHandlers); ?>;

			// update badge
			elem.vrecard('badge', '<i class="' + iconsLookup[data.extension] + '"></i>');
		}

		const getExtensionSnippets = (extension) => {
			let blocks = [];

			const activeBlock = $('#' + SELECTED_CODE_BLOCK);

			$('#cards-codehub-blocks .vre-card-fieldset').not('.add').each(function() {
				// always skip the active block as it will be added later
				if (!$(this).is(activeBlock)) {
					let data = JSON.parse($(this).find('input.code_block_json').val());

					if (data.extension == extension) {
						blocks.push(data);
					}
				}
			});

			// latest changes comes after
			return blocks.reverse();
		}

		const saveCodeBlocks = (extension, blocks) => {
			return new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=codehub.save'); ?>',
					{
						filter: [extension],
						blocks: blocks
					},
					(resp) => {
						resolve(resp);
					},
					(error) => {
						reject(error.responseText || Joomla.JText._('VRCONNECTIONLOST'));
					}
				);
			});
		}

		const generateCodeBlockID = (block) => {
			return new Promise((resolve, reject) => {
				if (block.id) {
					// we already have the block ID
					resolve(block.id);
				} else {
					UIAjax.do(
						'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=codehub.generateid'); ?>',
						{
							block: block,
						},
						(resp) => {
							resolve(resp);
						},
						(error) => {
							reject(error.responseText || Joomla.JText._('VRCONNECTIONLOST'));
						}
					);
				}
			});
		}
	})(jQuery, window);
</script>