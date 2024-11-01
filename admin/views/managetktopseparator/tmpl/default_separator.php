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

$separator = $this->separator;

?>
			
<!-- TITLE - Text -->

<?php
echo $this->formFactory->createField()
    ->type('text')
    ->name('title')
    ->value($separator->title)
    ->class('input-xxlarge input-large-text')
    ->required(true)
    ->label(JText::translate('VRMANAGETKTOPPINGSEP1'));
?>
