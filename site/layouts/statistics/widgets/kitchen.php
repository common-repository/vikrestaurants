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

/**
 * Layout variables
 * -----------------
 * @var  VREStatisticsWidget  $widget  The instance of the widget to be displayed.
 */
extract($displayData);

/**
 * Preload status codes popup for take-away orders.
 *
 * @since 1.9.1
 */
JHtml::fetch('vrehtml.statuscodes.popup', 2);

/**
 * Preload status codes popup for items.
 *
 * @since 1.8.3
 */
JHtml::fetch('vrehtml.statuscodes.popup', 3);

JText::script('VRSYSTEMCONNECTIONERR');

?>

<div class="canvas-align-top">
	
	<!-- widget contents go here -->

</div>

<!-- no script needed, default callback to set pure HTML will be used -->