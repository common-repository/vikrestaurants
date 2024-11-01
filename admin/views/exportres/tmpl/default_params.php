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

?>

<!-- PARAMETERS -->

<div class="vikpayparamdiv">
	<?php
	echo $this->formFactory->createField()
		->type('alert')
		->hiddenLabel(true)
		->text(JText::translate('VRMANAGEPAYMENT9'));
	?>
</div>

<!-- CONNECTION ERROR -->

<div id="vikparamerr" style="display: none;">
	<?php
	echo $this->formFactory->createField()
		->type('alert')
		->style('error')
		->hiddenLabel(true)
		->text(JText::translate('VRE_AJAX_GENERIC_ERROR'));
	?>
</div>

<?php
JText::script('JGLOBAL_SELECT_AN_OPTION');
?>

<script>
	(function($, w) {
		'use strict';

		w.vrRenderExportParams = () => {
			// render select
			$('.vikpayparamdiv select').each(function() {
				let option = $(this).find('option').first();

				let data = {
					// disable search for select with 3 or lower options
					minimumResultsForSearch: $(this).find('option').length > 3 ? 0 : -1,
					// allow clear selection in case the value of the first option is empty
					allowClear: option.val() || $(this).hasClass('required') ? false : true,
					// take the whole space
					width: '90%',
				};

				if (!option.val()) {
					// set placeholder by using the option text
					data.placeholder = option.text() || Joomla.JText._('JGLOBAL_SELECT_AN_OPTION');
					// unset the text from the option for a correct rendering
					option.text('');
				}

				$(this).select2(data);
			});

			// register form fields for validation
			w.validator.registerFields('.vikpayparamdiv .required');

			// init helpers
			$('.vikpayparamdiv .vr-quest-popover').popover({
				sanitize: false,
				container: 'body',
				trigger: 'hover focus',
				html: true,
			});
		}

		w.vrExportDriverChanged = () => {
			const driver = $('#vr-driver-sel').val();

			// destroy select2 
			$('.vikpayparamdiv select').select2('destroy');
			// unregister form fields
			w.validator.unregisterFields('.vikpayparamdiv .required');
			
			$('.vikpayparamdiv').html('');
			$('#vikparamerr').hide();

			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=exportres.getdriverformajax'); ?>',
				{
					driver: driver,
					type:   $('input[name="type"]').val(),
				},
				(html) => {
					$('.vikpayparamdiv').html(html);

					w.vrRenderExportParams();

					$('.vikpayparamdiv').trigger('payment.load');
				},
				(error) => {
					$('#vikparamerr').show();
				}
			);
		}
	})(jQuery, window);
</script>