<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.system
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

// fix [tmpl component] and [admin.php] issues on wordpress
JHtml::fetch('behavior.component');

$html  = !empty($displayData['html'])  ? $displayData['html'] : '';
$class = !empty($displayData['class']) ? ' ' . $displayData['class'] : '';

?>

<div class="wrap plugin-container<?php echo $class; ?>">

	<?php VikRestaurantsLayoutHelper::renderToolbar(); ?>

	<?php
	/**
	 * System messages are now fetched when processing the body
	 * in order to avoid keeping the same message within the
	 * session, as certain themes might obtain the buffer when
	 * the headers have been already sent.
	 */
	// VikRestaurantsLayoutHelper::renderSystemMessages();
	?>

	<?php echo $html; ?>

</div>
