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

<div class="area-params-fieldset" style="<?php echo $area->type ? 'display:none;' : ''; ?>">
	<?php echo VREApplication::getInstance()->alert(JText::translate('VRTKAREATYPEDESC0')); ?>
</div>

<!-- AREA PARAMETERS -->

<?php foreach ($this->types as $type => $label): ?>
	<div class="row-fluid area-params-fieldset" id="area-params-<?php echo $type; ?>" style="<?php echo $area->type == $type ? '' : 'display:none;'; ?>">
		
		<?php
		try
		{
			echo $this->loadTemplate('params_' . $type);
		}
		catch (Exception $e)
		{
			// configuration form not found, try to look under the response of the attached plugins

			/**
			 * Look for any additional fields to be pushed within this rule fieldset.
			 *
			 * NOTE: retrieved from "onDisplayViewTkarea" hook.
			 *
			 * @since 1.9
			 */
			if (isset($this->forms['params.' . $type]))
			{
				echo $this->forms['params.' . $type];

				// unset details form to avoid displaying it twice
				unset($this->forms['params.' . $type]);
			}
		}
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkarea","key":"params.<?php echo $type; ?>","type":"field"} -->

	</div>
<?php endforeach; ?>

<script>
	(function($, w) {
		'use strict';

		let shapesLoaded = false;

		w.MAP_SHAPES = [];

		w.fillMapShapes = (status, map) => {
			onInstanceReady(() => {
				return shapesLoaded;
			}).then(() => {
				MAP_SHAPES.forEach((shape) => {
					if (status) {
						// attach to this map
						shape.setMap(map);
					} else {
						// attach to the polygon map 
						shape.setMap(null);
					}
				});
			});
		}

		$(function() {
			$('#vr-type-sel').on('change', function() {
				let type = $(this).val();

				if (type.length == 0) {
					return false;
				}

				// unregister the required fields that depends on the selected type
				w.validator.unregisterFields('.maybe-required');

				// make the fields required for the selected type only
				w.validator.registerFields('#area-params-' + type + ' .maybe-required');

				// hide all deal rules
				$('.area-params-fieldset').hide();

				// show the selected one
				$('#area-params-' + type).show();
			});

			// create map shapes
			const shapes = <?php echo json_encode($this->shapes); ?>;

			shapes.forEach((shape) => {
				if (shape.id == <?php echo (int) $area->id; ?>) {
					// ignore the current delivery area
					return;
				}

				// trigger event to allow the correct handler to properly set up the shape within the map
				let event = $.Event('deliveryarea.shapes.setup.' + shape.type);
				event.shape = shape;
				$(w).trigger(event);
			});

			// all the shapes have been loaded
			shapesLoaded = true;
		});
	})(jQuery, window);
</script>