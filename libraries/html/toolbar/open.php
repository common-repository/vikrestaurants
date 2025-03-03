<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.toolbar
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

$bar = !empty($displayData['bar']) ? $displayData['bar'] : JToolbar::getInstance();

?>

<div class="jtoolbar">
	<h1 class="wp-heading-inline"><?php echo $bar->getTitle(); ?></h1>
