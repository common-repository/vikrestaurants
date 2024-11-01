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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $html        The menu HTML.
 * @var   boolean  $compressed  True if the menu is compressed.
 */

?>

<a class="btn mobile-only" id="vre-menu-toggle-phone">
	<i class="fas fa-bars"></i>
	<?php echo JText::translate('VRE_MENU'); ?>
</a>

<div class="vre-leftboard-menu<?php echo $compressed ? ' compressed' : ''; ?>" id="vre-main-menu">
	<?php echo $html; ?>
</div>