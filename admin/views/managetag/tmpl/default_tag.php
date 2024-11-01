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

$tag = $this->tag;

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
    ->type('text')
    ->name('name')
    ->value($tag->name)
    ->class('input-xxlarge input-large-text')
    ->required(true)
    ->label(JText::translate('VRMANAGELANG2'));
?>
