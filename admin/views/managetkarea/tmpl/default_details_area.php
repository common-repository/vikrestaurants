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

$area = $this->area;

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($area->name)
	->required(true)
	->class('input-xxlarge input-large-text')
	->label(JText::translate('VRMANAGETKAREA1'));
?>

<!-- TYPE - Select -->

<?php
$options = [
	JHtml::fetch('select.option', '', ''),
];

foreach ($this->types as $value => $text)
{
	$options[] = JHtml::fetch('select.option', $value, $text);
}

echo $this->formFactory->createField()
	->type('select')
	->name('type')
	->value($area->type)
	->id('vr-type-sel')
	->required(true)
	->label(JText::translate('VRMANAGETKAREA2'))
	->description(JText::translate('VRMANAGETKAREA2_HELP'))
	->options($options);
?>

<!-- CHARGE - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('charge')
	->value($area->charge)
	->label(JText::translate('VRMANAGETKAREA4'))
	->description(JText::translate('VRMANAGETKAREA4_HELP'))
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- MIN COST - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('min_cost')
	->value($area->min_cost)
	->label(JText::translate('VRMANAGETKAREA18'))
	->description(JText::translate('VRMANAGETKAREA18_HELP'))
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- PUBLISHED - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('published')
	->checked($area->published)
	->label(JText::translate('VRMANAGETKAREA3'));
?>

<?php
JText::script('VRE_FILTER_SELECT_TYPE');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vr-type-sel').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_TYPE'),
				allowClear: false,
				width: 300,
			});
		});
	})(jQuery);
</script>