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

<div class="config-fieldset">

	<div class="config-fieldset-body">
		<?php
		// display custom fields style selection
		echo $this->formFactory->createField()
			->type('select')
			->name('fields_layout_style')
			->value($this->params['fields_layout_style'] ?? 'default')
			->label(JText::translate('VRE_CUSTOMIZER_FIELDS_STYLE'))
			->description(JText::translate('VRE_CUSTOMIZER_FIELDS_STYLE_DESC'))
			->options([
				'default'  => JText::translate('VRE_CUSTOMIZER_FIELDS_STYLE_OPT_DEFAULT'),
				'material' => JText::translate('VRE_CUSTOMIZER_FIELDS_STYLE_OPT_MATERIAL'),
			]);
		?>
	</div>

</div>

<script>
	(function($) {
		'use strict';

		const customizerUrl = '<?php echo VREFactory::getPlatform()->getUri()->route('index.php?option=com_vikrestaurants&task=customizer.custom_fields_preview&tmpl=component&layout=%s', false); ?>';

		$(function() {
			$('select[name="fields_layout_style"]').select2({
				allowClear: false,
				width: 200,
			});

			$('select[name="fields_layout_style"]').on('change', function() {
				/**
				 * In WordPress the "%s" might be safely encoded as "%25s".
				 * Therefore the regex should support both the possibilities.
				 */
				changeCustomizerPreviewPage(customizerUrl.replace(/%(25)?s/, $(this).val()));
			});

			$('li[data-id="vrmenucustomfields"]').on('click', () => {
				$('select[name="fields_layout_style"]').trigger('change');
			});
		});
	})(jQuery);
</script>