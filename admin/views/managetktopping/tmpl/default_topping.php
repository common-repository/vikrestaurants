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

$topping = $this->topping;

?>
				
<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($topping->name)
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGELANG2'));
?>

<!-- SEPARATOR - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('id_separator')
	->value($topping->id_separator ?: '')
	->label(JText::translate('VRMANAGETKTOPPING5'))
	->description(JText::translate('VRMANAGETKTOPPING5_HELP'))
	->options(array_merge(
		[
			JHtml::fetch('select.option', '',                         ''),
			JHtml::fetch('select.option',  0, JText::translate('VRCREATENEWOPT')),
		],
		JHtml::fetch('vrehtml.admin.tktopseparators')
	));
?>

<!-- CREATE NEW SEPARATOR - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('separator_name')
	->placeholder(JText::translate('VRMANAGETKTOPPINGSEP1'))
	->control([
		'class' => 'create-separator-control',
		'style' => 'display: none;',
	]);
?>

<?php
JText::script('VRE_FILTER_SELECT_SEPARATOR');
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			$('select[name="id_separator"]').select2({
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_SEPARATOR'),
				allowClear: true,
				width: '90%',
			});

			$('select[name="id_separator"]').on('change', function() {
				const separator = $('input[name="separator_name"]');	

				if ($(this).val() == '0') {
					$('.create-separator-control').show();
					separator.focus();
					w.validator.registerFields(separator);
				} else {
					$('.create-separator-control').hide();
					w.validator.unregisterFields(separator);
					separator.val('');
				}
			});
		});
	})(jQuery, window);
</script>