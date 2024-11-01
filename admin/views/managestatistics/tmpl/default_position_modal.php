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

?>

<div class="inspector-form" id="inspector-position-form">

	<div class="inspector-fieldset">

		<!-- TITLE - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->name('position_name')
			->required(true)
			->label(JText::translate('VRE_WIDGET_POSITION'))
			->description(JText::translate('VRE_WIDGET_POSITION_ADD_HELP'));
		?>

	</div>

</div>

<?php
JText::script('VRE_WIDGET_POSITION_EXISTS_ERR');
?>

<script>
	(function($, w) {
		'use strict';

		w.clearPositionForm = () => {
			$('#inspector-position-form input[name="position_name"]').val('');
		}

		w.getPositionData = () => {
			let data = {};

			// get specified position
			data.position = $('#inspector-position-form input[name="position_name"]').val();

			// strip any non supported character
			data.position = data.position.replace(/[^a-zA-Z0-9_-]/g, '');

			return data;
		}

		$(function() {
			w.positionValidator = new VikFormValidator('#inspector-position-form');
			
			w.positionValidator.addCallback(() => {
				// get position input
				const input = $('#inspector-position-form input[name="position_name"]');

				// get position value
				let data = getPositionData();

				// make sure the position is not empty
				if (!data.position) {
					positionValidator.setInvalid(input);

					return false;
				}
				// make sure the position doesn't already exist
				else if ($('.widgets-position-row[data-position="' + data.position + '"]').length) {
					positionValidator.setInvalid(input);

					// inform the user that the position already exists
					alert(Joomla.JText._('VRE_WIDGET_POSITION_EXISTS_ERR'));

					return false;
				}

				// position is ok
				positionValidator.unsetInvalid(input);

				return true;
			});
		});
	})(jQuery, window);
</script>