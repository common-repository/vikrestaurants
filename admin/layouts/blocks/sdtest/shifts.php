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
 * @param 	array    list    An array of supported special days.
 * @param 	boolean  closed  True in case of closure, false otherwise.
 * @param 	array    args    An associative array containing the searched arguments.
 * @param 	array 	 shifts  A list of supported shifts.
 */
extract($displayData);

?>

<table class="git-table">

	<thead>
		<tr>
			<th><b><?php echo JText::translate('VRMANAGESHIFT1'); ?></b></th>
			<th><b><?php echo JText::translate('VRMANAGESHIFT2'); ?></b></th>
			<th><b><?php echo JText::translate('VRMANAGESHIFT3'); ?></b></th>
		</tr>
	</thead>

	<tbody>

		<?php
		foreach ($shifts as $shift)
		{
			?>
			<tr>
				<td><?php echo $shift->name; ?></td>
				<td><?php echo $shift->fromtime; ?></td>
				<td><?php echo $shift->totime; ?></td>
			</tr>
			<?php
		}
		?>

	</tbody>

</table>
