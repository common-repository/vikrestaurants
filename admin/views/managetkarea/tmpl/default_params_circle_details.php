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

$content = $this->area->content;

?>

<!-- LATITUDE - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('content[circle][center][latitude]')
	->value($content->center->latitude ?? '')
	->label(JText::translate('VRMANAGETKAREA6'))
	->placeholder(JText::translate('VRMANAGETKAREA7'))
	->required($this->area->type === 'circle')
	->class('maybe-required circle-map-repaint')
	->control(['required' => true]);
?>

<!-- LONGITUDE - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('content[circle][center][longitude]')
	->value($content->center->longitude ?? '')
	->hiddenLabel(true)
	->description(JText::translate('VRTKAREA_CIRCLE_LATLNG_HELP'))
	->placeholder(JText::translate('VRMANAGETKAREA8'))
	->required($this->area->type === 'circle')
	->class('maybe-required circle-map-repaint')
	->control(['required' => true]);
?>

<!-- RADIUS - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('content[circle][radius]')
	->value($content->radius ?? 1)
	->label(JText::translate('VRMANAGETKAREA9'))
	->required($this->area->type === 'circle')
	->class('maybe-required circle-map-repaint')
	->min(0)
	->step('any')
	->control(['required' => true])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('km'));
?>
