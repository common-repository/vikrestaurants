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

<!-- PUBLISHED - Checkbox -->

<?php
echo $this->formFactory->createField()
    ->type('checkbox')
    ->name('published')
    ->checked($menu->published)
    ->label(JText::translate('VRMANAGETKMENU12'));
?>

<!-- START PUBLISHING - Calendar -->

<?php
echo $this->formFactory->createField()
    ->type('date')
    ->name('start_publishing')
    ->value($menu->start_publishing ? JFactory::getDate($menu->start_publishing) : null)
    ->label(JText::translate('VRMANAGETKMENU23'))
    ->attributes([
        'showTime' => true,
    ]);
?>

<!-- FINISH PUBLISHING - Calendar -->

<?php
echo $this->formFactory->createField()
    ->type('date')
    ->name('end_publishing')
    ->value($menu->end_publishing ? JFactory::getDate($menu->end_publishing) : null)
    ->label(JText::translate('VRMANAGETKMENU24'))
    ->attributes([
        'showTime' => true,
    ]);
?>
