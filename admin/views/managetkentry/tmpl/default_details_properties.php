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

$entry = $this->entry;

$attributes = JHtml::fetch('vrehtml.admin.tkattributes');

$attr_icons = [];

foreach ($attributes as $attr)
{
	$attr_icons[$attr->value] = $attr->icon;
}

?>

<!-- ATTRIBUTES - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('attributes')
	->value($entry->attributes)
	->multiple(true)
	->label(JText::translate('VRMANAGETKMENU18'))
	->options(array_merge([JHtml::fetch('select.option', '', '')], $attributes));
?>

<!-- NO PREPARATION - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('ready')
	->checked($entry->ready)
	->label(JText::translate('VRMANAGETKMENU9'))
	->description(JText::translate('VRMANAGETKMENU9_HELP'));
?>

<?php
JText::script('VRTKNOATTR');
?>

<script>
	(function($) {
		'use strict';

		const ATTRIBUTES_LOOKUP = <?php echo json_encode($attr_icons); ?>;

		const formatAttributeOption = (attr) => {
			if (!attr.id) {
				// optgroup
				return attr.text;
			}

			if (!ATTRIBUTES_LOOKUP.hasOwnProperty(attr.id)) {
				// unsupported icon
				return attr.text;
			}

			return '<img class="vr-opt-tkattr" src="<?php echo VREMEDIA_URI; ?>' + ATTRIBUTES_LOOKUP[attr.id] + '" /> ' + attr.text;
		}

		$(function() {
			$('select[name="attributes[]"]').select2({
				placeholder: Joomla.JText._('VRTKNOATTR'),
				allowClear: true,
				width: '100%',
				formatResult: formatAttributeOption,
				formatSelection: formatAttributeOption,
				escapeMarkup: m => m,
			});
		});
	})(jQuery);
</script>