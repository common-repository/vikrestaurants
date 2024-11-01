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
				
<!-- PUBLISHED - Checkbox -->

<?php
echo $this->formFactory->createField()
    ->type('checkbox')
    ->name('published')
    ->checked($topping->published)
    ->label(JText::translate('VRMANAGETKTOPPING3'));
?>
