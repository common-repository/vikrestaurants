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

$properties = $this->properties;

?>

<!-- RESIZE - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('resize')
	->checked($properties['resize'])
	->label(JText::translate('VRMANAGEMEDIA6'))
	->onchange('resizeStatusValueChanged(this.checked)');
?>

<!-- SEPARATOR -->

<?php
echo $this->formFactory->createField()
	->type('separator')
	->control([
		'class' => 'original-size',
		'style' => $properties['resize'] ? '' : 'display: none;',
	]);
?>

<!-- ORIGINAL SIZE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('resize_value')
	->value($properties['resize_value'])
	->label(JText::translate('VRMANAGEMEDIA7'))
	->min(64)
	->step(1)
	->control([
		'class' => 'original-size',
		'style' => $properties['resize'] ? '' : 'display: none;',
	])->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('px'));
?>

<!-- SEPARATOR -->

<?php echo $this->formFactory->createField()->type('separator'); ?>

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
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('px'));
?>

<script>
	(function($, w) {
		'use strict';

		w.resizeStatusValueChanged = (checked) => {
			$('input[name="resize_value"]').prop('readonly', checked ? false : true);

			if (checked) {
				$('.original-size').show();
			} else {
				$('.original-size').hide();
			}
		}
	})(jQuery, window);
</script>