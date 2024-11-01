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
			
<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
    ->type('text')
    ->name('name')
    ->value($menu->name)
    ->class('input-xxlarge input-large-text')
    ->required(true)
    ->label(JText::translate('VRMANAGEMENU1'));
?>

<!-- ALIAS - Text -->

<?php
echo $this->formFactory->createField()
    ->type('text')
    ->name('alias')
    ->value($menu->alias) 
    ->label(JText::translate('JFIELD_ALIAS_LABEL'));
?>
			
<!-- IMAGE - Media -->

<?php
echo $this->formFactory->createField()
    ->type('media')
    ->name('image')
    ->value($menu->image) 
    ->label(JText::translate('VRMANAGEMENU18'));
?>
