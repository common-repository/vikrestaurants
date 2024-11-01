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

<!-- DESCRIPTION - Textarea -->

<?php
echo $this->formFactory->createField()
    ->type('textarea')
    ->name('description')
    ->value($tag->description)
    ->height(140)
    ->style('resize: vertical;')
    ->hiddenLabel(true);
?>
