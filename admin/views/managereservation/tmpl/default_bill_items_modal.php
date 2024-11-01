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

<div class="inspector-form" id="inspector-reservation-item-form">

	<div id="item-fieldset-add">
		<?php echo $this->loadTemplate('bill_items_modal_add'); ?>
	</div>

	<div id="item-fieldset-edit" style="display: none;">
		
	</div>

	<div class="loading-overlay" style="display:none;">
		<div class="vr-loading-tmpl">
			<div class="spinner size2x dark">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
			</div>
		</div>
	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		w.fillReservationItemForm = (data) => {
			if (typeof data.id === 'undefined') {
				$('#inspector-reservation-item-form .loading-overlay').hide();
				$('#item-fieldset-edit').hide().html('');
				$('#item-fieldset-add').show();

				$('#reservation-item-inspector button[data-role="save"]').prop('disabled', true);
			} else {
				loadReservationItemForm(data);
			}
		}

		w.getReservationItemData = () => {
			const form = $('#inspector-reservation-item-form');

			let data = {};

			// fetch item ID
			data.id = parseInt(form.find('#item_id').val());

			// fetch product ID
			data.id_product = parseInt(form.find('#item_id_product').val());

			// fetch product variation ID
			data.id_product_option = parseInt(form.find('#item_id_product_option').val());
			data.id_product_option = isNaN(data.id_product_option) ? 0 : data.id_product_option;

			// fetch product name
			data.name = form.find('#item_name').val();

			if (data.id_product_option > 0) {
				// fetch variation name
				data.option_name = form.find('#item_id_product_option option:selected').text();
			}

			// fetch serving number
			data.servingnumber = parseInt(form.find('#item_serving_number').val());
			data.servingnumber = isNaN(data.servingnumber) ? 0 : data.servingnumber;

			// fetch product price
			data.price = parseFloat(form.find('#item_price').val());
			data.price = isNaN(data.price) ? 0 : data.price;

			// preserve product discount
			data.discount = parseFloat(form.find('#item_discount').val());
			data.discount = isNaN(data.discount) ? 0 : data.discount;

			// fetch product tax ID
			if (form.find('#item_id_tax').length) {
				data.id_tax = parseInt(form.find('#item_id_tax').val());
				data.id_tax = isNaN(data.id_tax) ? 0 : data.id_tax;
			}

			// fetch product units
			data.quantity = parseInt(form.find('#item_quantity').val());
			data.quantity = isNaN(data.quantity) || data.quantity < 1 ? 1 : data.quantity;

			// fetch product notes
			data.notes = form.find('#item_notes').val();

			const currency = Currency.getInstance();

			// calculate base cost by multiplying the price by the selected
			// quantity and subtracting the discount, if any
			let baseCost = currency.multiply(data.price, data.quantity);
			baseCost = currency.diff(baseCost, data.discount);

			// create promise to load the costs of the option (tax, net, gross...)
			return new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=tax.testajax'); ?>',
					{
						id_tax:  data.id_product,
						amount:  baseCost,
						id_user: $('#vr-users-select').val(),
						langtag: '<?php echo $this->reservation->langtag; ?>',
						subject: 'restaurant.menusproduct',
					},
					(bill) => {
						// assign fetched prices to item object
						Object.assign(data, bill);

						// rename breakdown property
						data.tax_breakdown = data.breakdown;
						delete data.breakdown;

						resolve(data);
					},
					(err) => {
						reject(err.responseText || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));
					}
				);
			});
		}

		w.loadReservationItemForm = (data) => {
			$('#item-fieldset-add').hide();
			$('#item-fieldset-edit').html('');
			$('#inspector-reservation-item-form .loading-overlay').show();

			if (typeof data.id === 'undefined') {
				// show back button only in case of insert
				$('#reservation-item-inspector button[data-role="back"]').show();
			}

			new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=reservation.itemformajax'); ?>',
					data,
					(resp) => {
						resolve(resp);
					},
					(err) => {
						reject(err);
					}
				);
			}).then((html) => {
				const form = $('#item-fieldset-edit').html(html).trigger('form.load');

				// set item ID
				if (data.id !== undefined) {
					form.find('#item_id').val(data.id);
				}
				
				// set product ID
				if (data.id_product !== undefined) {
					form.find('#item_id_product').val(data.id_product);
				}

				// set product variation ID
				if (data.id_product_option) {
					form.find('#item_id_product_option').select2('val', data.id_product_option).trigger('change');
				}

				// set serving number
				if (data.servingnumber !== undefined) {
					form.find('#item_serving_number').select2('val', data.servingnumber).trigger('change');
				}

				// set product name
				if (data.name) {
					form.find('#item_name').val(data.name);
				}

				// set product price
				if (data.price !== undefined) {
					form.find('#item_price').val(data.price);
				}

				// set product discount
				if (data.discount !== undefined) {
					form.find('#item_discount').val(data.discount);
				}

				// set product tax ID
				if (data.id_tax !== undefined) {
					form.find('#item_id_tax').val(data.id_tax);
				}

				// set product units
				if (data.quantity) {
					form.find('#item_quantity').val(data.quantity);
				}

				// set product notes
				if (data.notes) {
					form.find('#item_notes').val(data.notes);
				}

				// show fieldset
				$('#inspector-reservation-item-form .loading-overlay').hide();
				$('#item-fieldset-edit').show();

				// register mandatory fields for a correct validation
				reservationItemValidator.registerFields('#item-fieldset-edit .required');

				// freeze the inspector observer whenever the page loads new contents
				w.itemInspectorObserver.freeze();

				$('#reservation-item-inspector button[data-role="save"]').prop('disabled', false);
			}).catch((error) => {
				$('#inspector-reservation-item-form .loading-overlay').hide();
				
				alert(error.responseText || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));

				if (!data.id) {
					// automatically go back to the previous page
					$('#reservation-item-inspector button[data-role="back"]').trigger('click');
				} else {
					// automatically close the inspector
					$('#reservation-item-inspector').inspector('dismiss');
				}
			});
		}

		$(function() {
			w.reservationItemValidator = new VikFormValidator('#inspector-reservation-item-form');
		});
	})(jQuery, window);
</script>