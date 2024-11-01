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

$media = $this->media;

$properties = VikRestaurants::getMediaProperties();

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($media['name_no_ext'])
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGEMEDIA1'))
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer($media['file_ext']));
?>

<!-- ACTION - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('action')
	->id('vr-media-action')
	->label(JText::translate('VRMANAGEMEDIA5'))
	->options([
		JHtml::fetch('select.option', '',                         ''),
		JHtml::fetch('select.option',  1, JText::translate('VRMEDIAACTION1')),
		JHtml::fetch('select.option',  2, JText::translate('VRMEDIAACTION2')),
		JHtml::fetch('select.option',  3, JText::translate('VRMEDIAACTION3')),
	]);
?>

<!-- MEDIA - File -->

<?php
echo $this->formFactory->createField()
	->type('file')
	->name('file')
	->class('vr-action-child-field')
	->label(JText::translate('VRMANAGEMEDIA4'))
	->control([
		'class'    => 'vr-action-child',
		'style'    => 'display: none;',
		'required' => true,
	]);
?>

<!-- RESIZE - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('resize')
	->checked($properties['resize'])
	->label(JText::translate('VRMANAGEMEDIA6'))
	->onchange('resizeStatusValueChanged(this.checked)')
	->control([
		'class' => 'vr-replace-child',
		'style' => 'display: none;',
	]);
?>

<!-- ORIGINAL SIZE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('resize_value')
	->value($properties['resize_value'])
	->label(JText::translate('VRMANAGEMEDIA7'))
	->readonly(!$properties['resize'])
	->min(64)
	->step(1)
	->control([
		'class' => 'vr-replace-child',
		'style' => 'display: none;',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('px'));
?>

<!-- THUMBNAIL SIZE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('thumb_value')
	->value($properties['thumb_value'])
	->label(JText::translate('VRMANAGEMEDIA8'))
	->min(16)
	->max(1024)
	->step(1)
	->control([
		'class' => 'vr-replace-child',
		'style' => 'display: none;',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('px'));
?>

<!-- SEPARATOR -->

<?php
echo $this->formFactory->createField()
	->type('separator')
	->control([
		'class' => 'vr-replace-child',
		'style' => 'display: none;',
	]);
?>

<!-- ALTERNATIVE TEXT - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('alt')
	->value($media['alt'])
	->label(JText::translate('VRMANAGEMEDIA14'))
	->placeholder(JText::translate('VRTKCONFIGITEMOPT0'));
?>

<!-- TITLE - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('title')
	->value($media['title'])
	->label(JText::translate('VRMANAGEMEDIA15'));
?>

<!-- CAPTION - Textarea -->

<?php
echo $this->formFactory->createField()
	->type('textarea')
	->name('caption')
	->value($media['caption'])
	->label(JText::translate('VRMANAGEMEDIA16'))
	->height(120)
	->style('resize: vertical;');
?>

<?php
JText::script('VRMEDIAACTION0');
?>

<script>
	(function($, w) {
		'use strict';

		w.resizeStatusValueChanged = (checked) => {
			$('input[name="resize_value"]').prop('readonly', checked ? false : true);
		}

		$(function() {
			$('#vr-media-action').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRMEDIAACTION0'),
				allowClear: true,
				width: 300,
			});

			$('#vr-media-action').on('change', function() {
				let val = $(this).val();

				if (val.length) {
					$('.vr-action-child').show();
					$('.vr-action-child-field').addClass('required');
					w.validator.registerFields('.vr-action-child-field');

					if (val == '3') {
						$('.vr-replace-child').show();
					} else {
						$('.vr-replace-child').hide();
					}
				} else {
					$('.vr-action-child, .vr-replace-child').hide();
					w.validator.unregisterFields('.vr-action-child-field');
				}
			});
		});
	})(jQuery, window);
</script>