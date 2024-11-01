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

$area = $this->area;

?>

<div class="inspector-form" id="inspector-polygon-points-form">

	<div class="inspector-fieldset">

		<div style="display:none;" id="polygon-point-repeat">

			<div class="inspector-repeatable-head">
				<span class="polygon-point-summary">
					<i class="fas fa-ellipsis-v big hndl" style="margin-right: 4px;"></i>

					<span class="badge badge-info point-coord"></span>
				</span>

				<span>
					<a href="javascript: void(0);" class="polygon-point-edit-prod no-underline">
						<i class="fas fa-pen-square big ok"></i>
					</a>

					<a href="javascript: void(0);" class="polygon-point-trash-prod no-underline">
						<i class="fas fa-minus-square big no"></i>
					</a>
				</span>
			</div>

			<div class="inspector-repeatable-body">

				<!-- LATITUDE - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->name('content[polygon][latitude][]')
					->label(JText::translate('VRMANAGETKAREA7'))
					->id(false);
				?>

				<!-- LONGITUDE - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->name('content[polygon][longitude][]')
					->label(JText::translate('VRMANAGETKAREA8'))
					->id(false);
				?>

			</div>

		</div>

		<div class="inspector-repeatable-container" id="polygon-point-pool">
			
		</div>

		<!-- ADD POINT - Button -->

		<?php
		echo $this->formFactory->createField()
			->type('button')
			->id('add-polygon-point-btn')
			->text('<i class="fas fa-plus-circle"></i> '. JText::translate('VRMANAGETKAREA11'))
			->hiddenLabel(true);
		?>

	</div>

</div>

<?php
JText::script('VRSYSTEMCONFIRMATIONMSG');
?>

<script>
	(function($, w) {
		'use strict';

		// tracks the selected marker to auto-focus the related fieldset
		let selectedPoint = null;

		// counts the total number of created points
		let POLYGON_POINTS_COUNT = 0;

		w.addPolygonPoint = (data) => {
			if (typeof data !== 'object') {
				data = {};
			}

			let form = $('#inspector-polygon-points-form');

			// get repeatable form of the inspector
			const repeatable = $(form).find('#polygon-point-repeat');
			// clone the form
			const clone = $('<div class="inspector-repeatable polygon-point"></div>')
				.append(repeatable.clone().html());

			// set unique identifier
			clone.attr('data-id', POLYGON_POINTS_COUNT);

			const latInput = clone.find('input[name="content[polygon][latitude][]"]');

			// set up point latitude
			if (typeof data.latitude !== 'undefined') {
				latInput.val(data.latitude);

				// auto-collapse existing blocks
				clone.addClass('collapsed');
			}

			const lngInput = clone.find('input[name="content[polygon][longitude][]"]');

			// set up point latitude
			if (typeof data.longitude !== 'undefined') {
				lngInput.val(data.longitude);
			}

			// refresh head every time something changes
			$(latInput).add(lngInput).on('change', () => {
				refreshSummaryPolygonPoint(clone);
			});

			// handle delete button
			clone.find('.polygon-point-trash-prod').on('click', () => {
				if (confirm(Joomla.JText._('VRSYSTEMCONFIRMATIONMSG'))) {
					deletePolygonPoint(clone);
				}
			});

			// handle edit button
			clone.find('.polygon-point-edit-prod').on('click', () => {
				clone.toggleClass('collapsed');
			});

			// append the clone to the document
			$('#polygon-point-pool').append(clone);

			if (data.latitude === undefined) {
				// start by focusing "latitude" input
				latInput.focus();
			}

			// set up summary head and refresh map
			refreshSummaryPolygonPoint(clone);

			POLYGON_POINTS_COUNT++;
		}

		w.updatePolygonPoint = (index, data) => {
			const block = $('#inspector-polygon-points-form .polygon-point[data-id="' + index + '"]');

			block.find('input[name="content[polygon][latitude][]"]').val(data.latitude);
			block.find('input[name="content[polygon][longitude][]"]').val(data.longitude).trigger('change');
		}

		w.deletePolygonPoint = (block) => {
			let id = block.attr('data-id');
			$(block).remove();
			removePolygonMarker(id);
		}

		w.getPolygonPoints = () => {
			let points = [];

			$('#inspector-polygon-points-form .polygon-point').each(function() {
				let data = {
					lat: parseFloat($(this).find('input[name="content[polygon][latitude][]"]').val()),
					lng: parseFloat($(this).find('input[name="content[polygon][longitude][]"]').val()),
				}

				if (!isNaN(data.lat) && !isNaN(data.lng)) {
					points.push(data);
				}
			});

			return points;
		}

		w.selectPolygonPoint = (index) => {
			selectedPoint = index;
			vreOpenInspector('tkarea-polygon-inspector');
		}

		const refreshSummaryPolygonPoint = (block) => {
			// extract latitude and longitude from block
			let coords = [
				parseFloat(block.find('input[name="content[polygon][latitude][]"]').val()),
				parseFloat(block.find('input[name="content[polygon][longitude][]"]').val()),
			];

			// get rid of invalid coordinates
			coords = coords.filter(c => c && !isNaN(c));

			// set badge within block head
			block.find('.polygon-point-summary').find('.point-coord').text(coords.join(' - '));

			if (coords.length === 2) {
				updatePolygonMarker(block.attr('data-id'), coords[0], coords[1]);
			}
		}

		$(function() {
			$('#add-polygon-point-btn').on('click', () => {
				addPolygonPoint();
			});

			$('#polygon-point-pool').sortable({
				items:  '.inspector-repeatable',
				revert: false,
				axis:   'y',
				handle: '.hndl',
				cursor: 'move',
				stop: () => {
					// refresh polygon
					refreshPolygonShape();
				},
			});

			$('#tkarea-polygon-inspector').on('inspector.aftershow', () => {
				if (selectedPoint !== null) {
					// collapse all the open points
					$('#inspector-polygon-points-form .polygon-point').addClass('collapsed');
					
					// expand only the selected on
					$('#inspector-polygon-points-form .polygon-point[data-id="' + selectedPoint + '"]')
						.removeClass('collapsed')
						.find('input[name="content[polygon][latitude][]"]')
							.select()
							.focus();

					// reset selected point
					selectedPoint = null;
				}
			});

			<?php
			// fill polygon markers
			if ($area->type === 'polygon' && $area->content)
			{
				foreach ($area->content as $coord)
				{
					echo "addPolygonPoint(" . json_encode($coord) . ");\n";
				}
			}
			?>
		});
	})(jQuery, window);
</script>