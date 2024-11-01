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

$room = $this->room;

?>
			
<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
    ->type('text')
    ->name('name')
    ->value($room->name)
    ->class('input-xxlarge input-large-text')
    ->required(true)
    ->label(JText::translate('VRMANAGEROOM1'));
?>

<!-- IMAGE - Media -->

<?php
echo $this->formFactory->createField()
    ->type('media')
    ->name('image')
    ->value($room->image)
    ->label(JText::translate('VRMANAGEROOM4'));
?>
