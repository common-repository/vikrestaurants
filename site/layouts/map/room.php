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
$id 	 = isset($displayData['id'])	  ? $displayData['id'] 	    : 'vre-svg-canvas';
$room 	 = isset($displayData['room']) 	  ? $displayData['room']	: null;
$tables  = isset($displayData['tables'])  ? $displayData['tables']  : array();
$admin   = isset($displayData['admin'])   ? $displayData['admin']   : false;
$options = isset($displayData['options']) ? $displayData['options'] : new stdClass;

if (!$room)
{
	return;
}

// ignore max-width rule for SVG canvas
$room->addStyle('max-width', 'none', true);

?>

<div style="display:flex;width:100%;max-height:calc(100vh - 220px);">

	<!-- make it scrollable within the page bounds -->
	<div class="svg-scrollable" style="overflow:scroll;border:1px solid #ddd;">

		<?php
		/**
		 * Learn about this code on MDN.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/SVG/Element/g
		 */
		?>
		<svg 
			width="<?php echo (int) $room->width; ?>"
			height="<?php echo (int) $room->height; ?>"
			id="<?php echo $this->escape($id); ?>"
			class="room-svg"
			style="<?php echo $this->escape($room->style); ?>"
			xmlns="http://www.w3.org/2000/svg"
		>

			<?php
			// iterate all room tables
			foreach ($tables as $table)
			{
				// build layout design data
				$data = array(
					'table'   => $table,
					'layout'  => @$displayData['shapeLayout'][$table->getData('class', 'none')],
					'admin'   => $admin,
					'options' => $options,
				);

				// get table layout
				$layout = @$displayData['tableLayout'];

				// make sure the layout is callable
				if (is_callable(array($layout, 'render')))
				{
					// render table layout
					echo $layout->render($data);
				}
			}
			?>

		</svg>

	</div>

	<?php
	$inspector = $options->inspector ?? true;

	if ($admin && $inspector && $inspector !== 'false')
	{
		// get inspector layout
		$layout = @$displayData['inspector'];

		// make sure the layout is callable
		if (is_callable(array($layout, 'render')))
		{
			// render inspector using the same layout data
			echo $layout->render($displayData);
		}
	}
	?>

</div>

<?php
if (!$admin)
{
	?>
	<script>

		jQuery(document).ready(function() {

			jQuery('g.table-graphic').on('click', function() {

				if (this.classList.contains('table-selected')) {
					// table already selected, do nothing
					return;
				}

				var tableId   = jQuery(this).data('id');
				var tableName = jQuery(this).find('.table-name-text').text();
				var available = jQuery(this).data('available');

				if (available) {
					// remove selection from any tables
					jQuery('g.table-graphic').each(function() {

						this.classList.remove('table-selected');

						jQuery(this).find('.shape-selection-target')
							.attr('stroke', 'none')
							.attr('stroke-width', 0);

						// hide selection badge
						jQuery(this).find('.table-selected-badge').hide();
					});

					// select table
					this.classList.add('table-selected')

					jQuery(this).find('.shape-selection-target')
						.attr('stroke', '#009900')
						.attr('stroke-width', 2);

					// show selection badge
					jQuery(this).find('.table-selected-badge').show();
				}

				<?php
				if (!empty($options->callback) && is_string($options->callback))
				{
					echo rtrim($options->callback, ';'); ?>(tableId, tableName, available, this);<?php
				}
				?>

			});

		});

	</script>

	<style>
		g.table-graphic[data-available="0"] {
			cursor: default;
			opacity: 0.4;
		}
	</style>
	<?php
}
else if (!empty($options->callback) && is_string($options->callback))
{
	?>
	<script>
		(function($, w) {
			'use strict';

			if (typeof w.mapRoomCallbackRegistered !== "undefined") {
				// do not register callback more than once
				return;
			}

			// security flag to prevent duplicate script registrations
			w.mapRoomCallbackRegistered = true;

			$(document).on('click', 'g.table-graphic', function() {
				const tableId   = $(this).data('id');
				const tableName = $(this).find('.table-name-text').text();
				const available = $(this).data('available');

				<?php echo rtrim($options->callback, ';'); ?>(tableId, tableName, available, this);
			});
		})(jQuery, window);
	</script>
	<?php
}
