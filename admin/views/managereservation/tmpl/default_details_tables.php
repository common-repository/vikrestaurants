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
 * Loads dedicated CSS file.
 *
 * @since 1.7.4
 */
VREApplication::getInstance()->addStyleSheet(VREASSETS_URI . 'css/oversight.css');

// obtain the ID of all the selected tables
$tables = array_map(function($t)
{
	return (int) $t->id;
}, $this->reservation->tables);

if (!$tables && $this->reservation->id_table)
{
	// we are creating a new reservation and the selected table is not listed within the array
	$tables = [$this->reservation->id_table];
}

// prepare map configuration
$mapOptions = [
	// an array containing the ID of all the selected tables
	'selectedTables' => $tables,
	// the javascript callback to invoke whenever a table gets clicked
	'callback' => 'onSelectTable',
	// whether the map layout should display the inspector
	'inspector' => false,
	// whether the map should display the selection badge when clicking on a table
	'showBadge' => true,
	// in case of closure, ignore the tables availability
	'ignore_availability' => (bool) $this->reservation->closure,
];

?>

<!-- ROOM - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->id('vr-room-sel')
	->value($this->selectedRoom)
	->hiddenLabel(true)
	->options(array_values(array_map(function($room)
	{
		return JHtml::fetch('select.option', $room->id, $room->name);
	}, $this->mapModel->getRooms())));
?>

<input type="hidden" name="id_table" value="<?php echo $this->escape(json_encode($tables)); ?>" />

<div id="rooms-maps-wrapper">
	<?php foreach ($this->mapModel->getRooms() as $room): ?>
		<div class="vre-map-svg-wrapper" id="vre-map-svg-wrapper<?php echo (int) $room->id; ?>" style="display: none;">
			<?php
			/** @var VREMapFactory */
			$renderer = $this->mapModel->createMapRenderer($room->id, $this->filters, $mapOptions);
			echo $renderer->admin()->build();
			?>
		</div>
	<?php endforeach; ?>

	<div class="loading-overlay" style="display:none;">
		<div class="vr-loading-tmpl">
			<div class="spinner size2x dark">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
			</div>
		</div>
	</div>
</div>

<?php
JText::script('VRMANAGERESERVATION_MISSING_TABLE_ERR');
JText::script('VRMANAGERESERVATION_CAPACITY_TABLE_WARN');
?>

<script>
	(function($, w) {
		'use strict';

		w.MAP_OPTIONS = <?php echo json_encode($mapOptions); ?>;

		let selectedRoom;

		// Make sure the selected tables can host the number of selected participants.
		// Do not apply this validation if we are updating an existing reservation, just
		// to avoid spamming the administrator with an alert.
		let skipTablesValidation = <?php echo $this->reservation->id ? 'true' : 'false'; ?>;
		// flag used to check whether the table validation is enabled or not
		w.GLOBAL_TABLES_VALIDATION = true;

		const updateSelectedTables = () => {
			w.MAP_OPTIONS.selectedTables = [];

			// fetch all the selected tables (for the current room only)
			$('#vre-map-svg-wrapper' + selectedRoom).find('g.table-graphic.table-selected').each(function() {
				w.MAP_OPTIONS.selectedTables.push($(this).data('id'));
			});

			// commit changes
			$('#adminForm input[name="id_table"]').val(JSON.stringify(w.MAP_OPTIONS.selectedTables));
		}

		w.onSelectTable = (tableId, tableName, available, node) => {
			if (node.classList.contains('table-selected') == false) {
				// select table
				node.classList.add('table-selected')

				// show selection badge
				$(node).find('.table-selected-badge').show();
			} else {
				// select table
				node.classList.remove('table-selected')

				// hide selection badge
				$(node).find('.table-selected-badge').hide();
			}

			// do not skip the tables validation
			skipTablesValidation = false;

			// commit changes
			updateSelectedTables();
		}

		w.enableTableSelection = (enable) => {
			if (enable) {
				$('#rooms-maps-wrapper .loading-overlay').hide();
			} else {
				$('#rooms-maps-wrapper .loading-overlay').show();
			}

			// Something has (probably) changed on the search fields.
			// Do not skip the tables validation.
			skipTablesValidation = false;
		}

		$(function() {
			$('#vr-room-sel').select2({
				allowClear: false,
				width: '100%',
			});

			$('#vr-room-sel').on('change', function() {
				selectedRoom = parseInt($(this).val());

				// reset changes
				updateSelectedTables();

				$('.vre-map-svg-wrapper').hide();
				$('#vre-map-svg-wrapper' + selectedRoom).show();
			}).trigger('change');

			// Make sure the selected tables can host the number of selected participants.
			// Do not apply this validation if we are updating an existing reservation, just
			// to avoid spamming the administrator with an alert.
			onInstanceReady(() => {
				return w.reservationValidator;
			}).then((validator) => {
				validator.addCallback(() => {
					if (w.MAP_OPTIONS.selectedTables.length === 0) {
						// no table selected
						alert(Joomla.JText._('VRMANAGERESERVATION_MISSING_TABLE_ERR'));
						return false;
					}

					const isClosure = <?php echo $this->reservation->closure ? 'true' : 'false'; ?>;

					if (skipTablesValidation || isClosure) {
						// ignore validation
						return true;
					}

					let capacity = 0;
					let people   = parseInt($('#vr-people-sel').val());

					w.MAP_OPTIONS.selectedTables.forEach((tableId) => {
						capacity += parseInt($('g#table-' + tableId).data('max'));
					});

					if (capacity < people) {
						// not enough tables
						let answer = confirm(Joomla.JText._('VRMANAGERESERVATION_CAPACITY_TABLE_WARN'));
						
						if (!answer) {
							// the user refused the submit
							return false;
						}
					}

					return true;
				});
			});
		});
	})(jQuery, window);
</script>