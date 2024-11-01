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

// get global attributes
$table = isset($displayData['table']) ? $displayData['table'] : null;

if (!$table)
{
	return;
}

?>

<?php
/**
 * Since <image> is not able to apply the stroke, we need to 
 * display a rect before the image in order to display the border.
 */
?>

<!-- draw image shape -->
<rect
	x="<?php echo (int) $table->x; ?>"
	y="<?php echo (int) $table->y; ?>"
	width="<?php echo (int) $table->width; ?>"
	height="<?php echo (int) $table->height; ?>"
	stroke="<?php echo $table->stroke; ?>"
	stroke-width="<?php echo (int) $table->strokeWidth; ?>"
	fill="transparent"
	class="shape-selection-target"
></rect>

<image
	xlink:href="<?php echo $table->href; ?>"
	x="<?php echo (int) $table->x; ?>"
	y="<?php echo (int) $table->y; ?>"
	width="<?php echo (int) $table->width; ?>"
	height="<?php echo (int) $table->height; ?>"
	class="table-shape shape-image"
	preserveAspectRatio="none"
></image>
