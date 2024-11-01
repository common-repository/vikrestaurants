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

JHtml::fetch('formbehavior.chosen');
JHtml::fetch('bootstrap.popover');

?>

<div class="inspector-form" id="inspector-export-download-form">

	<div class="inspector-fieldset">

		<!-- NAME - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->name('filename')
			->value($this->type)
			->label(JText::translate('VREXPORTRES1'));
		?>

		<!-- EXPORT CLASS - Select -->

		<?php
		$options = [
			JHtml::fetch('select.option', '', JText::translate('JGLOBAL_SELECT_AN_OPTION')),
		];

		$description = [];

		foreach ($this->exportDrivers as $key => $driver)
		{
			if ($driver instanceof E4J\VikRestaurants\DataSheet\ConfigurableDriver)
			{
				$name = $driver->getName();

				$description[] = '<driverdesc class="export-driver-desc" id="export-driver-desc-' . $key . '" style="display: none; margin-top: 2px;">'
					. $driver->getDescription()
					. '</driverdesc>';
			}
			else
			{
				$name = strtoupper($key);
			}

			$options[] = JHtml::fetch('select.option', $key, $name);
		}

		echo $this->formFactory->createField()
			->type('select')
			->name('driver')
			->label(JText::translate('VREXPORTRES2'))
			->description(implode("\n", $description))
			->onchange('vrExportDriverChanged(this)')
			->required(true)
			->options($options);
		?>

		<!-- RAW - Checkbox -->

		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->name('raw')
			->label(JText::translate('VRE_EXPORT_RAW'))
			->description(JText::translate('VRE_EXPORT_RAW_DESC'));
		?>

		<!-- DRIVER PARAMETERS -->

		<div class="vikpayparamdiv" style="display: none;">&nbsp;</div>

	</div>

</div>

<?php
JText::script('VRE_AJAX_GENERIC_ERROR');
?>

<script>
	(function($, w) {
		'use strict';

		const renderExportParams = () => {
			// render new select
			VikRenderer.chosen('.vikpayparamdiv');

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

		w.vrExportDriverChanged = (select) => {
			const driver = $(select).val();

			$('.export-driver-desc').hide();
			$('#export-driver-desc-' + driver).show();

			// unregister form fields
			w.validator.unregisterFields('.vikpayparamdiv .required');
			
			$('.vikpayparamdiv').hide().html('');

			UIAjax.do(
				'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=export.params'); ?>',
				{
					driver: driver,
				},
				(html) => {
					if (html) {
						$('.vikpayparamdiv').html(html);
						$('.vikpayparamdiv').show();
						renderExportParams();
					}

					$('.vikpayparamdiv').trigger('payment.load');
				},
				(error) => {
					$(select).val('');

					setTimeout(() => {
						alert(error.responseText || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));
					}, 128);
				}
			);
		}

		$(function() {
			VikRenderer.chosen('#adminForm');

			$('#export-btn').on('click', () => {
				if (!w.validator.validate()) {
					return false;
				}

				$('#jmodal-download').modal('hide');

				Joomla.submitform('export.download', document.adminForm);
			});
		});
	})(jQuery, window);
</script>