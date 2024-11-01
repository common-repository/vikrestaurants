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

$table = $this->getPreviewData();

$vik = VREApplication::getInstance();

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

<?php
if (!$table->body)
{
	echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 'warning', false, ['style' => 'margin-top: 10px;']);
}
else
{
	?>
	<div class="scrollable-hor">
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
			<?php echo $vik->openTableHead(); ?>
				<tr>
					<?php foreach ($table->head as $k => $column): ?>
						<th class="<?php echo $vik->getAdminThClass('nowrap'); ?>" style="text-align: center;">
							<input type="checkbox" name="columns[]" value="<?php echo (int) $k; ?>" id="export_col_<?php echo (int) $k; ?>" checked="checked" />
							
							<label for="export_col_<?php echo (int) $k; ?>">
								<?php echo $column; ?>
							</label>
						</th>
					<?php endforeach; ?>
				</tr>
			<?php echo $vik->closeTableHead(); ?>
			
			<?php
			for ($i = 0, $n = count($table->body); $i < $n; $i++)
			{
				$row = $table->body[$i];
				?>
				<tr class="row<?php echo ($i % 2); ?>">
					<?php foreach ($row as $k => $value): ?>
						<td style="text-align: center;">
							<div>
								<?php
								// remove HTML from value
								$value = strip_tags((string) $value);
								
								// show the first 64 characters when higher than 80
								if (mb_strlen($value, 'UTF-8') > 80)
								{
									echo trim(mb_substr($value, 0, 64, 'UTF-8')) . '...';

									if (mb_strlen($value, 'UTF-8') > 800)
									{
										// avoid to display more than 800 characters
										$value = trim(mb_substr($value, 0, 800, 'UTF-8')) . '...';
									}

									// then display a tooltip with the remaining text
									?>
									<i class="fas fa-info-circle hasTooltip" title="<?php echo $this->escape($value); ?>"></i>
									<?php
								}
								else
								{
									echo $value;
								}
								?>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php
			}
			?>
		
		</table>
	</div>

	<div style="text-align: center;">
		<small><?php echo JText::sprintf('VREXPORTTABLEFOOTER', $n, count($this->dataSheet->getBody())); ?></small>
	</div>
	<?php
}
?>

	<?php echo JHtml::fetch('form.token'); ?>

	<input type="hidden" name="task" value="export.download" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="type" value="<?php echo $this->escape($this->type); ?>" />
	<input type="hidden" name="return" value="<?php echo $this->escape($this->return); ?>" />

	<?php foreach ($this->cid as $id): ?>
		<input type="hidden" name="cid[]" value="<?php echo $this->escape($id); ?>" />
	<?php endforeach; ?>

	<?php
	$footer  = '<button type="button" class="btn btn-success" data-role="download">' . JText::translate('VRDOWNLOAD') . '</button>';

	// render inspector to manage tax rules
	echo JHtml::fetch(
		'vrehtml.inspector.render',
		'export-download-inspector',
		[
			'title'       => JText::translate('VRMAINTITLEVIEWEXPORT'),
			'closeButton' => true,
			'keyboard'    => true,
			'footer'      => $footer,
			'width'       => 500,
		],
		$this->loadTemplate('params')
	);
	?>

</form>

<style>
	.scrollable-hor {
		overflow-x: auto;
		/* reserve some space on the bottom for the horizontal scrollbar */
		margin-bottom: 20px;
		min-width: initial;
	}
	.export-column-disabled > * {
		opacity: 0.4;
	}
</style>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			w.validator = new VikFormValidator('#adminForm');

			const columns = $('input[name="columns[]"]');

			// handle change event to toggle the status of the columns
			// to include within the export query
			columns.on('change', function() {
				const checked = $(this).is(':checked');
				const index   = columns.index(this);

				$('#adminForm table tr').each(function() {
					let td = $(this).find('td').eq(index);

					if (td.length && checked) {
						td.removeClass('export-column-disabled');
					} else {
						td.addClass('export-column-disabled');
					}
				});

				// cache column status
				cacheColumnStatus($(this).val(), checked);
			});

			const cacheKey = 'export.<?php echo $this->type; ?>.disabled';

			/**
			 * Helper function used to cache the status of the disabled
			 * columns within the browser session storage.
			 */
			const cacheColumnStatus = (column, status) => {
				if (typeof sessionStorage === 'undefined') {
					// the browser doesn't support session storage
					return false;
				}

				// get list of disabled columns
				let disabled = getDisabledColumns();

				// find index of columns within the list, if any
				let index = disabled.indexOf(column);

				if (status) {
					// enabled, so the column must be removed from the list
					if (index !== -1) {
						disabled.splice(index, 1);
					}
				} else {
					// disabled, so the column must be included within the list
					if (index === -1) {
						disabled.push(column);
					}
				}

				// save cache
				sessionStorage.setItem(cacheKey, disabled.join(','));
			};

			/**
			 * Helper function used to retrieve the disabled
			 * columns from the browser session storage.
			 */
			const getDisabledColumns = () => {
				if (typeof sessionStorage === 'undefined') {
					// the browser doesn't support session storage
					return [];
				}

				// get list of disabled columns
				let disabled = sessionStorage.getItem(cacheKey);

				if (!disabled) {
					// create new array from scratch
					disabled = [];
				} else {
					// create array from string
					disabled = disabled.split(/\s*,\s*/g);
				}

				return disabled;
			};

			// auto-disable cached columns during page loading
			getDisabledColumns().forEach((column) => {
				$('input[name="columns[]"][value="' + column + '"]')
					.prop('checked', false)
					.trigger('change');
			});

			$('#export-download-inspector').on('inspector.download', function() {
				if (!w.validator.validate()) {
					return false;
				}

				$(this).inspector('close');
				Joomla.submitform('export.download', document.adminForm);
			});

			$('#adminForm input').on('keydown', (event) => {
				// do not auto-submit the form on enter
				if (event.which == 13) {
					event.preventDefault();
					return false;
				}
			});

			Joomla.submitbutton = function(task) {
				if (task == 'export') {
					// open inspector
					vreOpenInspector('export-download-inspector');
				} else {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>