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

$specialday = $this->specialday;

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($specialday->name)
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGESPDAY1'))
?>

<!-- GROUP - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('group')
	->id('vr-group-sel')
	->value($specialday->group)
	->required(true)
	->label(JText::translate('VRMANAGESPDAY16'))
	->options(JHtml::fetch('vrehtml.admin.groups', [1, 2]));
?>

<!-- IMAGE - Media -->

<?php
echo $this->formFactory->createField()
	->type('media')
	->name('images')
	->multiple(true)
	->value($specialday->images)
	->label(JText::translate('VRMANAGESPDAY17'))
	->description(JText::translate('VRMANAGESPDAY17_HELP'))
	->control([
		'class' => 'restaurant-params',
		'style' => $specialday->group == 1 ? '' : 'display: none;',
	]);
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vr-group-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			$('#vr-group-sel').on('change', function() {
				if ($(this).val() == 1) {
					$('.takeaway-params').hide();
					$('.restaurant-params').show();

					$('#vr-restaurant-shifts').attr('name', 'working_shifts[]');
					$('#vr-takeaway-shifts').attr('name', '');

					$('#vr-restaurant-menus').attr('name', 'id_menu[]');
					$('#vr-takeaway-menus').attr('name', '');
				} else {
					$('.restaurant-params').hide();
					$('.takeaway-params').show();

					$('#vr-restaurant-shifts').attr('name', '');
					$('#vr-takeaway-shifts').attr('name', 'working_shifts[]');

					$('#vr-restaurant-menus').attr('name', '');
					$('#vr-takeaway-menus').attr('name', 'id_menu[]');
				}
			});
		});
	})(jQuery);
</script>