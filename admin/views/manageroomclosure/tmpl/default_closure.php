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

$closure = $this->closure;

?>
				
<!-- ROOM - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('id_room')
	->value($closure->id_room)
	->required(true)
	->label(JText::translate('VRMANAGEROOMCLOSURE1'))
	->options(JHtml::fetch('vikrestaurants.rooms'));
?>

<!-- START CLOSURE - Calendar -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('start_date')
	->id('vrstartdate')
	->value($closure->start_ts)
	->required(true)
	->label(JText::translate('VRMANAGEROOMCLOSURE2'))
	->attributes([
		'onChange' => 'vrStartDateChanged()',
		'showTime' => true,
	]);
?>

<!-- END CLOSURE - Calendar -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('end_date')
	->id('vrenddate')
	->value($closure->end_ts)
	->required(true)
	->label(JText::translate('VRMANAGEROOMCLOSURE3'))
	->attributes([
		'showTime' => true,
	]);
?>

<script>
	(function($, w) {
		'use strict';

		w.vrStartDateChanged = () => {
			if ($('#vrenddate').val().length == 0) {
				let date = $('#vrstartdate').val();

				$('#vrenddate').val(date).attr('data-alt-value', date);
			}
		}

		$(function() {
			$('select[name="id_room"]').select2({
				allowClear: false,
				width: 300,
			});
		});
	})(jQuery, window);
</script>
