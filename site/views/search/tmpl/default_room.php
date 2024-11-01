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
 * Template file used to display the section from which it
 * is possible to select/change room.
 *
 * @since 1.8
 */

$vik = VREApplication::getInstance();

$config = VREFactory::getConfig();

// create map to easily access rooms descriptions
$desc_lookup = [];

// create map to easily access rooms available tables
$tables_lookup = [];

foreach ($this->avail as $table)
{
	if (!isset($tables_lookup[$table->id_room]))
	{
		// set only the first available table
		$tables_lookup[$table->id_room] = (int) $table->id;
	}
}

?>

<!-- ROOM SELECTION -->

<div id="vrchooseroomouterdiv">
	
	<span id="vrchooseroomsp"><?php echo JText::translate('VRCHOOSEROOM'); ?></span>

	<div id="vrchooseroomdiv" class="vre-select-wrapper">
		<select class="vre-select" id="vrroomselect" name="room" onChange="roomSelectionChanged(this);">
			<?php
			/**
			 * The rooms in the dropdown are now displayed using
			 * the correct ordering.
			 *
			 * It is not possible to use always the ordering set for the rooms
			 * as the system needs to show first the rooms that offer non-shared tables.
			 * So, we should follow the ordering used by $this->availableRooms array.
			 *
			 * @since 1.8
			 */
			foreach ($this->availableRooms as $id_room)
			{
				// find room
				$room = array_filter($this->rooms, function($room) use ($id_room)
				{
					return $room->id == $id_room;
				});

				if ($room)
				{
					// take only first value
					$room = array_shift($room);

					// copy description into a tmp variable
					$description = $room->description;

					/**
					 * Properly render the contents also for the description
					 * of the other rooms in the list.
					 *
					 * @since 1.8.5
					 */
					$vik->onContentPrepare($description);
					$desc_lookup[$room->id] = $description->text;

					?>
					<option value="<?php echo $room->id; ?>" <?php echo ($room->id == $this->selectedRoom->id ? 'selected="selected"' : ''); ?>>
						<?php echo $room->name; ?>
					</option>
					<?php
				}
			}
			?>
		</select>
	</div>

</div>

<!-- ROOM DESCRIPTION -->

<div id="vrroomdescriptionactiondiv" style="<?php echo ($this->selectedRoom->description ? '' : 'display:none;'); ?>">
	<button type="button" class="vre-btn primary small" id="vrroomdescriptionactionlink" onClick="changeRoomDescriptionDisplay(this);">
		<?php echo JText::translate('VRSHOWDESCRIPTION'); ?>
	</button>
</div>

<?php
foreach ($desc_lookup as $room_id => $description)
{
	if ($description)
	{
		?>
		<div class="vrroomdescriptiondiv" id="vrroomdescriptiondiv<?php echo (int) $room_id; ?>" style="display: none;">
			<?php echo $description; ?>
		</div>
		<?php
	}
}

JText::script('VRSHOWDESCRIPTION');
JText::script('VRHIDEDESCRIPTION');
?>

<script>
	(function($, w) {
		'use strict';

		const ROOMS_TABLES_LOOKUP = <?php echo json_encode($tables_lookup); ?>;
	
		w.roomSelectionChanged = (select) => {
			<?php if ($config->getUint('reservationreq') == 0): ?>
				// reload page to display new map
				$(select).closest('form').submit();
			<?php else: ?>
				// switch room description and auto-select
				// first available table of the picked room

				// get selected room ID
				let id_room = parseInt($(select).val());

				// update selected table with first one available
				SELECTED_TABLE = ROOMS_TABLES_LOOKUP.hasOwnProperty(id_room) ? ROOMS_TABLES_LOOKUP[id_room] : null;

				// get room description
				let desc = $('#vrroomdescriptiondiv' + id_room);

				if (desc.length) {
					// show description details button
					$('#vrroomdescriptionactiondiv').show();
				} else {
					// hide description details button
					$('#vrroomdescriptionactiondiv').hide();
				}

				if ($('.vrroomdescriptiondiv').is(':visible')) {
					$('#vrroomdescriptionactionlink').trigger('click');
				}

				$('.vrroomdescriptiondiv').not(desc).hide();
			<?php endif; ?>
		}

		w.changeRoomDescriptionDisplay = (link) => {
			// get selected room ID
			let id_room = parseInt($('#vrroomselect').val());
			// get room description
			let desc = $('#vrroomdescriptiondiv' + id_room);

			if (desc.is(':visible') || desc.length == 0) {
				// hide description if visible
				$(link).text(Joomla.JText._('VRSHOWDESCRIPTION'));
				desc.hide();
			} else {
				// otherwise show description
				$(link).text(Joomla.JText._('VRHIDEDESCRIPTION'));
				desc.show();
			}
		}

		$(function() {
			<?php if ($config->getUint('reservationreq') == 1): ?>
				// trigger room selection to auto-select the first available table for the chosen room
				roomSelectionChanged(document.getElementById('vrroomselect'));
			<?php endif; ?>
		});
	})(jQuery, window);
</script>