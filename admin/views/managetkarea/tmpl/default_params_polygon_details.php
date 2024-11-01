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

<!-- MANAGE POINTS - Button -->

<?php
echo $this->formFactory->createField()
	->type('button')
	->id('manage-points-btn')
	->text(JText::translate('VRTKAREA_POLYGON_MANAGE_BTN'))
	->hiddenLabel(true)
	->description(
		JText::sprintf(
			'VRTKAREA_POLYGON_LEGEND',
			'<i class="fas fa-location-arrow"></i>'
		)
	);
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#manage-points-btn').on('click', () => {
				vreOpenInspector('tkarea-polygon-inspector');
			});

			$('#tkarea-polygon-inspector').on('inspector.dismiss', function() {
				$(this).inspector('dismiss');
			});
		});
	})(jQuery);
</script>