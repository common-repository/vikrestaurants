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
 * @param 	object   $data   The special day object.
 */
extract($displayData);

$currency = VREFactory::getCurrency();

?>

<table class="git-table">

	<thead>
		<tr>
			<th width="30%"><b><?php echo JText::translate('VRE_CONFIG_PARAM'); ?></b></th>
			<th width="70%"><b><?php echo JText::translate('VRE_CONFIG_SETTING'); ?></b></th>
		</tr>
	</thead>

	<tbody>

		<!-- Delivery -->

		<tr>
			<td><?php echo JText::translate('VRMANAGETKRES14'); ?></td>

			<td>
				<?php
				if ($data->delivery)
				{
					?><i class="fas fa-check-circle ok medium-big"></i><?php
				}
				else
				{
					?><i class="fas fa-dot-circle no medium-big"></i><?php
				}
				?>
			</td>
		</tr>

		<!-- Pickup -->

		<tr>
			<td><?php echo JText::translate('VRMANAGETKRES15'); ?></td>

			<td>
				<?php
				if ($data->pickup)
				{
					?><i class="fas fa-check-circle ok medium-big"></i><?php
				}
				else
				{
					?><i class="fas fa-dot-circle no medium-big"></i><?php
				}
				?>
			</td>
		</tr>

		<!-- Working Shifts -->

		<tr>
			<td><?php echo JText::translate('VRMANAGESPDAY4'); ?></td>

			<td>
				<?php
				foreach ($data->shifts ? $data->shifts : $shifts as $shift)
				{
					?>
					<span class="badge badge-info">
						<?php echo $shift->fromtime . ' - ' . $shift->totime; ?>
					</span>
					<?php
				}
				?>
			</td>
		</tr>

		<!-- Menus -->

		<tr>
			<td><?php echo JText::translate('VRMANAGESPDAY10'); ?></td>

			<td>
				<?php
				if ($data->menus)
				{
					foreach ($data->getMenus(true) as $menu)
					{
						?>
						<span class="badge badge-success">
							<?php echo $menu->title; ?>
						</span>
						<?php
					}
				}
				else
				{
					?>
					<span class="badge badge-important">
						<?php echo JText::translate('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</span>

					<i class="fas fa-exclamation-triangle no hasTooltip" title="<?php echo $this->escape(JText::translate('VRTESTSPECIALDAYSNOMENUS')); ?>"></i>
					<?php
				}
				?>
			</td>
		</tr>

		<?php
		// show delivery areas only when specified
		if ($data->deliveryAreas)
		{
			?>
			<!-- Delivery Areas -->

			<tr>
				<td><?php echo JText::translate('VRMENUTAKEAWAYDELIVERYAREAS'); ?></td>

				<td>
					<?php
					foreach ($data->getDeliveryAreas(true) as $area)
					{
						?>
						<span class="badge badge-warning">
							<?php echo $area->name; ?>
						</span>
						<?php
					}
					?>
				</td>
			</tr>
			<?php
		}
		?>

	</tbody>

</table>
