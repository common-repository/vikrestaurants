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
	
<!-- draw rectangle shape -->
<rect
	x="<?php echo (int) $table->x; ?>"
	y="<?php echo (int) $table->y; ?>"
	width="<?php echo (int) $table->width; ?>"
	height="<?php echo (int) $table->height; ?>"
	rx="<?php echo (int) $table->rx; ?>"
	ry="<?php echo (int) $table->ry; ?>"
	stroke="<?php echo $table->stroke; ?>"
	stroke-width="<?php echo (int) $table->strokeWidth; ?>"
	class="table-shape shape-rect shape-selection-target"
></rect>
