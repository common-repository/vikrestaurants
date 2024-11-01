<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.managetos
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

JHtml::fetch('vrehtml.assets.toast', 'bottom-center');

$field = $displayData['field'];

$vik = VREApplication::getInstance();

// render inspector to manage ToS fields
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'tos-inspector-' . $field['id'],
	array(
		'title'       => JText::translate('VRMAINTITLEEDITCUSTOMF'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => '<button type="button" class="btn btn-success" id="tos-save-' . $field['id'] . '">' . JText::translate('JAPPLY') . '</button>',
		'width'       => 400,
	),
	JLayoutHelper::render('html.managetos.modal', $displayData)
);

JText::script('VRSYSTEMCONNECTIONERR');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			// get ToS table row
			var tr = $('input[name="cid[]"][value="<?php echo (int) $field['id']; ?>"]').closest('tr');

			// get column that contains the field name
			var nameTD = tr.children().eq(2);

			// wrap name within a span
			nameTD.html('<span> ' + nameTD.html() + ' </span>');

			// create edit button
			var editButton = $('<a href="javascript:void(0);"><i class="fas fa-pen-square big"></i></a>');

			// register click event
			editButton.on('click', function() {
				// open inspector
				vreOpenInspector('tos-inspector-<?php echo (int) $field['id']; ?>');
			});

			// float button to right side
			editButton.css('float', 'right');

			// append edit button
			nameTD.append(editButton);

			// register save event
			$('#tos-save-<?php echo (int) $field['id']; ?>').on('click', function() {
				// get form containing the field value
				var form = $('form#tos-form-<?php echo (int) $field['id']; ?>');

				// make save request
				UIAjax.do(
					// request end-point
					'admin-ajax.php?action=vikrestaurants&task=customf.savetosajax',
					// serialize form
					form.serialize(),
					// successful response
					function(resp) {
						// get saved data
						var data = JSON.parse(resp);

						// update name within table column
						nameTD.find('span').html(data.name);

						// auto-close inspector on successful save
						vreCloseInspector('tos-inspector-<?php echo (int) $field['id']; ?>');
					},
					// failure
					function(error) {
						if (!error.responseText) {
							// use default connection lost error
							error.responseText = Joomla.JText._('VRSYSTEMCONNECTIONERR');
						}

						// raise error
						ToastMessage.enqueue({
							text: error.responseText,
							status: 0,
						});
					}
				);
			});
		});
	})(jQuery);
</script>
