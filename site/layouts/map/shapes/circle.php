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
	
<!-- draw circle shape -->
<circle
	class="table-shape shape-circle shape-selection-target"
	cx="<?php echo (int) $table->cx; ?>"
	cy="<?php echo (int) $table->cy; ?>"
	r="<?php echo (int) $table->radius; ?>"
	stroke="<?php echo $table->stroke; ?>"
	stroke-width="<?php echo (int) $table->strokeWidth; ?>"
></circle>
