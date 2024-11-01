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

$menu = $this->menu;

?>

<!-- COST - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('cost')
	->value($menu->cost)
	->label(JText::translate('VRTKCARTOPTION3'))
	->description(JText::translate('VRE_MENU_COST_HELP'))
	->min(0)
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- CHOOSABLE - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('choosable')
	->checked($menu->choosable)
	->label(JText::translate('VRMANAGEMENU31'))
	->description(JText::translate('VRMANAGEMENU31_HELP'));
?>
