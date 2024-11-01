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

/**
 * Layout variables
 * -----------------
 * @var  JRegistry             $data         The field data registry.
 * @var  FormFactoryInterface  $formFactory  The form factory instance.
 */
extract($displayData);

?>

<div class="multi-field">

	<?php
	// render dropdown to select the tax
	$select = $data->getProperties();
	$select['type'] = 'select';
	$select['hidden'] = true;
	echo $formFactory->createField($select);

	if ($data->get('create', true) !== false): ?>
		<button
			type="button"
			class="btn"
			style="margin-left: 6px;"
			id="<?php echo $data->get('id'); ?>-create-btn"
		><?php echo JText::translate('JACTION_CREATE'); ?></button>
	<?php endif; ?>

</div>

<?php
if ($data->get('create', true) !== false)
{
	// tax management modal
	echo JHtml::fetch(
		'bootstrap.renderModal',
		'jmodal-managetax-' . $data->get('id'),
		array(
			'title'       => JText::translate('VREMAINTITLENEWTAX'),
			'closeButton' => true,
			'keyboard'    => false, 
			'bodyHeight'  => 80,
			'url'         => 'index.php?option=com_vikrestaurants&task=tax.add&tmpl=component',
			'footer'      => '<button type="button" class="btn" data-role="tax.save">' . JText::translate('JTOOLBAR_APPLY') . '</button>',
		)
	);
}
?>

<script>
	(function($) {
		'use strict';

		const openModal = (id, url, jqmodal) => {
			<?php echo VREApplication::getInstance()->bootOpenModalJS(); ?>
		}

		const addNewTaxIntoSelect = (tax, selector) => {
			if (selector.find('option[value="' + tax.id + '"]').length) {
				// tax already in list
				return false;
			}

			// insert new option within the select
			$(selector).each(function() {
				$(this).append('<option value="' + tax.id + '">' + tax.name + '</option>');
			});

			return true;
		}

		$(function() {
			$('#<?php echo $data->get('id'); ?>').select2({
				minimumResultsForSearch: -1,
				width: 'auto',
				allowClear: <?php echo $data->get('allowClear', false) ? 'true' : 'false'; ?>,
				<?php if ($placeholder = $data->get('placeholder')): ?>
					placeholder: '<?php echo addslashes($placeholder); ?>',
				<?php endif; ?>
			});

			$('#<?php echo $data->get('id'); ?>-create-btn').on('click', () => {
				openModal('managetax-<?php echo $data->get('id'); ?>', null, true);
			});

			$('#jmodal-managetax-<?php echo $data->get('id'); ?> button[data-role="tax.save"]').on('click', () => {
				// trigger click of save button contained in managetax view
				window.modalTaxSaveButton.click();
			});

			$('#jmodal-managetax-<?php echo $data->get('id'); ?>').on('hidden', () => {
				// check if the tax was saved
				if (window.modalSavedTax) {
					let selector = $('select#<?php echo $data->get('id'); ?>');

					// insert tax in target dropdown
					if (addNewTaxIntoSelect(window.modalSavedTax, selector)) {
						// auto-select new option for the related select
						$(selector).select2('val', window.modalSavedTax.id);
					}
				}
			});
		});
	})(jQuery);
</script>
