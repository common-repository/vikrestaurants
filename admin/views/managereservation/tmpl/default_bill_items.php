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

<div class="order-items-table" id="order-items-table">
	<?php echo $this->loadTemplate('bill_items_table'); ?>
</div>

<?php
JText::script('VRMANAGETKRES16');
JText::script('VRMANAGETKRES35');
?>

<script>
	(function($, w) {
		'use strict';

		let SELECTED_ITEM = null;
		let TMP_COSTS     = {};

		w.vreOpenRestaurantItemCard = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRMANAGETKRES16');
				SELECTED_ITEM = null;
			} else {
				title = Joomla.JText._('VRMANAGETKRES35');
				SELECTED_ITEM = 'reservation-item-row-' + index;
			}
			
			// open inspector
			vreOpenInspector('reservation-item-inspector', {title: title});
		}

		w.getInputTotals = () => {
			const data = {};

			[
				'total_net',
				'total_tax',
				'bill_value',
				'payment_charge',
				'payment_tax',
			].forEach((name) => {
				// update input hidden
				data[name] = parseFloat($('input[name="' + name + '"]').val());
			});

			return data;
		}

		w.updateInputTotals = (data) => {
			const totals   = $('.order-totals');
			const currency = Currency.getInstance();

			for (let k in data) {
				if (data.hasOwnProperty(k)) {
					let num = parseFloat(data[k]);
					$('input[name="' + k + '"]').val(num.toFixed(2));

					// get price columns
					let column = totals.find('[data-column="' + k + '"]');
					
					column.find('b').html(currency.format(data[k]));

					if (num == 0 && k != 'bill_value') {
						column.hide();
					} else {
						column.show();
					}
				}
			}
		}

		w.vreRefreshTotals = (data) => {
			// get totals
			let totals = getInputTotals();

			// get currency helper
			const currency = Currency.getInstance();

			// add new total net
			totals.total_net = currency.sum(totals.total_net, data.net);
			// subtract previous one
			totals.total_net = currency.diff(totals.total_net, TMP_COSTS.net);

			// add new total tax
			totals.total_tax = currency.sum(totals.total_tax, data.tax);
			// subtract previous one
			totals.total_tax = currency.diff(totals.total_tax, TMP_COSTS.tax);

			// add new total gross
			totals.bill_value = currency.sum(totals.bill_value, data.gross);
			// subtract previous one
			totals.bill_value = currency.diff(totals.bill_value, TMP_COSTS.gross);

			// refresh total due by subtracting the total amount paid to the bill value
			let totPaid = parseFloat($('.order-totals [data-column="total_due"]').data('paid'));
			totals.total_due = Math.max(0, currency.diff(totals.bill_value, totPaid));

			// commit changes
			updateInputTotals(totals);
		}

		$(function() {
			$('#add-item-btn').on('click', () => {
				vreOpenRestaurantItemCard();
			});

			$('#reservation-item-inspector').on('inspector.observer.init', (event) => {
				// register a reference to the observer used by the inspector
				w.itemInspectorObserver = event.observer;
			});

			// fill the form before showing the inspector
			$('#reservation-item-inspector').on('inspector.show', () => {
				let data = [];

				// fetch JSON data
				if (SELECTED_ITEM) {
					const fieldset = $('#' + SELECTED_ITEM);

					data = fieldset.find('input[name="item_json[]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				$('#reservation-item-inspector button[data-role="back"]').hide();

				if (data.id === undefined) {
					// creating new record, hide delete button
					$('#reservation-item-inspector button[data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#reservation-item-inspector button[data-role="delete"]').show();
				}

				// save current costs of the item
				TMP_COSTS = {
					net:   data.net   ? parseFloat(data.net)   : 0,
					tax:   data.tax   ? parseFloat(data.tax)   : 0,
					gross: data.gross ? parseFloat(data.gross) : 0,
				};

				// fill the form with the retrieved data
				fillReservationItemForm(data);
			});

			// handle the saving process
			$('#reservation-item-inspector').on('inspector.save', function() {
				// validate form
				if (!reservationItemValidator.validate()) {
					return false;
				}

				// disable button
				$('#reservation-item-inspector button[data-role="save"]').prop('disabled', true);

				// get saved record
				getReservationItemData().then((data) => {
					let fieldset;

					if (SELECTED_ITEM) {
						fieldset = $('#' + SELECTED_ITEM);
					} else {
						fieldset = vreAddReservationItemCard(data);
					}

					if (fieldset.length == 0) {
						// an error occurred, abort
						return false;
					}

					// save JSON data
					fieldset.find('input[name="item_json[]"]').val(JSON.stringify(data));

					// refresh details shown in card
					vreRefreshReservationItemCard(fieldset, data);

					// refresh item totals
					vreRefreshTotals(data);

					// auto-close on save
					$(this).inspector('dismiss');
				}).catch((err) => {
					// the callback performs an AJAX request to retrieve
					// all the costs of the option to save/add and, since
					// it may fail, we need to catch the exception thrown
					// and display the error message
					alert(err);
				}).finally(() => {
					// re-enable button
					$('#reservation-item-inspector button[data-role="save"]').prop('disabled', false);
				});
			});

			// handle the deleting process
			$('#reservation-item-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_ITEM);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="item_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="item_deleted[]" value="' + json.id + '" />');
				}

				// save current costs of the option
				TMP_COSTS = {
					net:   json.net   ? parseFloat(json.net)   : 0,
					tax:   json.tax   ? parseFloat(json.tax)   : 0,
					gross: json.gross ? parseFloat(json.gross) : 0,
				};

				// refresh total prices
				vreRefreshTotals({net: 0, tax: 0, gross: 0});

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});

			// go to the previous page when clicking the back button
			$('#reservation-item-inspector').on('inspector.back', () => {
				if (w.itemInspectorObserver.isChanged()) {
					// something has changed, warn the user about the
					// possibility of losing any changes
					if (!confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'))) {
						return false;
					}
				}

				$('#reservation-item-inspector button[data-role="back"]').hide();
				fillReservationItemForm({});

				// freeze the form to discard any pending changes
				w.itemInspectorObserver.freeze();
			});
		});
	})(jQuery, window);
</script>