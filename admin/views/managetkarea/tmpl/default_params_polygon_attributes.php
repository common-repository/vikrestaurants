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

$attributes = $this->area->attributes;

?>

<!-- COLOR - Color -->

<?php
echo $this->formFactory->createField()
	->type('color')
	->name('attributes[polygon][color]')
	->value($attributes->color ?? 'FF0000')
	->label(JText::translate('VRMANAGETKAREA10'))
	->description(JText::translate('VRMANAGETKAREA10_DESC'))
	->class('polygon-shape-repaint');
?>

<!-- STROKE COLOR - Color -->

<?php
echo $this->formFactory->createField()
	->type('color')
	->name('attributes[polygon][strokecolor]')
	->value($attributes->strokecolor ?? 'FF0000')
	->label(JText::translate('VRMANAGETKAREA14'))
	->description(JText::translate('VRMANAGETKAREA14_DESC'))
	->class('polygon-shape-repaint');
?>

<!-- STROKE WEIGHT - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('attributes[polygon][strokeweight]')
	->value($attributes->strokeweight ?? '2')
	->label(JText::translate('VRMANAGETKAREA15'))
	->description(JText::translate('VRMANAGETKAREA15_DESC'))
	->class('polygon-shape-repaint')
	->min(0)
	->max(10)
	->step(1)
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('px'));
?>

<!-- DISPLAY SHAPES - Checkbox -->

<?php
// display all shapes only in case there are 2 or more existing delivery areas
// or in case we are creating a new area and there is another existing record
if ($this->shapes->count() - ($this->area->id ? 1 : 0) >= 1)
{
	echo $this->formFactory->createField()
		->type('checkbox')
		->name('attributes[polygon][display_shapes]')
		->checked($attributes->display_shapes ?? false)
		->label(JText::translate('VRMANAGETKAREA12'))
		->description(JText::translate('VRMANAGETKAREA12_HELP'))
		->onchange('fillPolygonMapShapes(this.checked)');
}
?>
