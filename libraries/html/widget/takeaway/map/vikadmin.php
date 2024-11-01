<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.widget
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

extract($displayData);

?>

<div class="vre-tkmap-locations-wrapper">

	<div class="vre-tkmap-locations-actions">
		<button type="button" class="add-location button" id="<?php echo $id; ?>-add">
			<?php echo JText::translate('VRTKMAPADDLOCATION'); ?>
		</button>
	</div>

	<div class="vre-tkmap-locations-container" id="<?php echo $id; ?>-container">
		
	</div>

	<div class="vre-tkmap-location-record-tmp" style="display: none;">
		<div class="vre-tkmap-location-record">

			<div class="vre-tkmap-location-summary">

				<span class="location-name"></span>

				<span class="location-actions">
					<a href="javascript: void(0);" class="edit-location" onclick="vreMapEditLocation(this);">
						<i class="dashicons dashicons-edit"></i>
					</a>

					<a href="javascript: void(0);" class="delete-location" onclick="vreMapDeleteLocation(this, '<?php echo $name; ?>');">
						<i class="dashicons dashicons-trash"></i>
					</a>
				</span>

			</div>

			<div class="vre-tkmap-location-form" style="display: none;">

				<!-- NAME -->

				<p>
					<label for="<?php echo $id; ?>-title-N"><?php echo JText::translate('VRTKMAPTITLE') . '*'; ?>:</label>

					<input type="text" data-name="title" id="<?php echo $id; ?>-title-N" class="widefat" />
				</p>

				<!-- LATITUDE -->

				<p>
					<label for="<?php echo $id; ?>-lat-N"><?php echo JText::translate('VRTKMAPLAT') . '*'; ?>:</label>

					<input type="number" data-name="lat" id="<?php echo $id; ?>-lat-N" class="widefat" step="any" />
				</p>

				<!-- LONGITUDE -->

				<p>
					<label for="<?php echo $id; ?>-lng-N"><?php echo JText::translate('VRTKMAPLNG') . '*'; ?>:</label>

					<input type="number" data-name="lng" id="<?php echo $id; ?>-lng-N" class="widefat" step="any" />
				</p>

				<!-- DESCRIPTION -->

				<p>
					<label for="<?php echo $id; ?>-desc-N"><?php echo JText::translate('VRTKMAPDESC') . '*'; ?>:</label>

					<textarea data-name="desc" id="<?php echo $id; ?>-desc-N" class="widefat"></textarea>
				</p>

				<!-- SAVE BUTTON -->

				<p>
					<button type="button" class="button save-location" onclick="vreMapSaveLocation(this, '<?php echo $name; ?>');">
						<?php echo JText::translate('VRTKMAPSAVEBTN'); ?>
					</button>
				</p>

			</div>

		</div>
	</div>

	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $this->escape(json_encode($value)); ?>" />

</div>

<script>

	jQuery(document).ready(function() {

		var blocks_counter = 0;
		var locations = <?php echo json_encode($value); ?>;

		for (var i = 0; i < locations.length; i++)
		{
			vreMapInsertLocation(
				'#<?php echo $id; ?>-add',
				++blocks_counter,
				locations[i]
			);
		}

		// register click event to add new locations
		jQuery('#<?php echo $id; ?>-add').on('click', function() {
			// insert record within the locations container
			vreMapInsertLocation(this, ++blocks_counter)
			
			// open location form
			jQuery('#<?php echo $id; ?>-container')
				.find('.vre-tkmap-location-record').last()
					.find('.edit-location')
						.trigger('click');
		});

	});

</script>
