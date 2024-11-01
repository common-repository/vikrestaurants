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

JHtml::fetch('vrehtml.assets.intltel', '[name="purchaser_phone"]');

$reservation = $this->reservation;

?>

<!-- USER - Select -->

<?php
// construct users field
$hiddenCustomers = $this->formFactory->createField()
	->type('hidden')
	->name('id_user')
	->id('vr-users-select')
	->value($reservation->id_user > 0 ? $reservation->id_user : '');

$addCustomerButton = $this->formFactory->createField()
	->type('button')
	->id('add-customer-btn')
	->text('<i class="fas fa-user-plus"></i> ' . JText::translate('VRE_ADD_CUSTOMER'))
	->style($reservation->id_user > 0 ? 'display: none' : '')
	->hidden(true);

$editCustomerButton = $this->formFactory->createField()
	->type('button')
	->id('edit-customer-btn')
	->text('<i class="fas fa-user-edit"></i> ' . JText::translate('VRE_EDIT_CUSTOMER'))
	->style($reservation->id_user > 0 ? '' : 'display: none')
	->hidden(true);

// wrap hidden field into a control
echo $this->formFactory->createField()
	->label(JText::translate('VRMANAGEREVIEW10'))
	->render(function($data, $input) use ($hiddenCustomers, $addCustomerButton, $editCustomerButton) {
		?>
		<div><?php echo $hiddenCustomers; ?></div>

		<div class="manage-customer-actions" style="margin-top: 5px;">
			<?php echo $addCustomerButton; ?>
			<?php echo $editCustomerButton; ?>
		</div>
		<?php
	});
?>

<!-- NOMINATIVE - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('purchaser_nominative')
	->value($reservation->purchaser_nominative)
	->label(JText::translate('VRMANAGERESERVATION18'));
?>

<!-- MAIL - Email -->

<?php
echo $this->formFactory->createField()
	->type('email')
	->name('purchaser_mail')
	->value($reservation->purchaser_mail)
	->label(JText::translate('VRMANAGERESERVATION6'));
?>

<!-- PHONE - Tel -->

<?php
echo $this->formFactory->createField()
	->type('tel')
	->name('purchaser_phone')
	->value($reservation->purchaser_phone)
	->label(JText::translate('VRMANAGERESERVATION16'));
?>

<?php
JText::script('VRMAINTITLENEWCUSTOMER');
JText::script('VRMAINTITLEEDITCUSTOMER');
?>

<script>
	(function($, w) {
		'use strict';

		const BILLING_USERS_POOL = {};

		const openModal = (id, url, jqmodal) => {
			<?php echo VREApplication::getInstance()->bootOpenModalJS(); ?>
		}

		$(function() {
			$('#vr-users-select').select2({
				placeholder: '--',
				allowClear: true,
				width: '90%',
				minimumInputLength: 2,
				ajax: {
					url: '<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=customer.users'); ?>',
					dataType: 'json',
					type: 'POST',
					quietMillis: 50,
					data: (term) => {
						return {
							term: term
						};
					},
					results: (data) => {
						return {
							results: $.map(data, (item) => {
								if (!BILLING_USERS_POOL.hasOwnProperty(item.id))
								{
									BILLING_USERS_POOL[item.id] = item;
								}

								return {
									text: item.text || item.billing_name,
									id: item.id,
								};
							}),
						};
					},
				},
				initSelection: (element, callback) => {
					// the input tag has a value attribute preloaded that points to a preselected repository's id
					// this function resolves that id attribute to an object that select2 can render
					// using its formatResult renderer - that way the repository name is shown preselected
					if ($(element).val().length) {
						callback({text: '<?php echo (empty($reservation->purchaser_nominative) ? '' : addslashes($reservation->purchaser_nominative)); ?>'});
					}
				},
				formatSelection: (data) => {
					if ($.isEmptyObject(data.billing_name)) {
						// display data returned from ajax parsing
						return data.text;
					}
					// display pre-selected value
					return data.billing_name;
				},
			});

			$('#vr-users-select').on('change customer.refresh', function() {
				let id = $(this).val();

				if (!id) {
					$('#edit-customer-btn').hide();
					$('#add-customer-btn').show();
				} else {
					$('#add-customer-btn').hide();
					$('#edit-customer-btn').show();
				}

				if (!BILLING_USERS_POOL.hasOwnProperty(id)) {
					return;
				}
				
				if (BILLING_USERS_POOL[id].hasOwnProperty('billing_name')) {
					$('input[name="purchaser_nominative"]').val(BILLING_USERS_POOL[id].billing_name);
				}

				if (BILLING_USERS_POOL[id].hasOwnProperty('billing_mail')) {
					$('input[name="purchaser_mail"]').val(BILLING_USERS_POOL[id].billing_mail);
				}

				if (BILLING_USERS_POOL[id].hasOwnProperty('billing_phone')) {
					$('input[name="purchaser_phone"]').intlTelInput('setNumber', BILLING_USERS_POOL[id].billing_phone).trigger('change');
				}

				if (BILLING_USERS_POOL[id].hasOwnProperty('fields')) {
					compileCustomFields(BILLING_USERS_POOL[id].fields);
				}
			});

			// save "country code" and "dial code" every time the phone number changes
			$('input[name="purchaser_phone"]').on('change countrychange', function() {
				var country = $(this).intlTelInput('getSelectedCountryData');

				if (!country) {
					return false;
				}

				if (country.iso2) {
					$('input[name="purchaser_country"]').val(country.iso2.toUpperCase());
				}

				if (country.dialCode) {
					var dial = '+' + country.dialCode.toString().replace(/^\+/);

					if (country.areaCodes) {
						dial += ' ' + country.areaCodes[0];
					}

					$('input[name="purchaser_prefix"]').val(dial);
				}
			});

			$('#add-customer-btn').on('click', () => {
				$('#jmodal-managecustomer .customer-title').text(Joomla.JText._('VRMAINTITLENEWCUSTOMER'));

				// add customer URL
				let url = 'index.php?option=com_vikrestaurants&tmpl=component&task=customer.add';

				// open modal
				openModal('managecustomer', url, true);
			});

			$('#edit-customer-btn').on('click', () => {
				$('#jmodal-managecustomer .customer-title').text(Joomla.JText._('VRMAINTITLEEDITCUSTOMER'));

				// add customer URL
				let url = 'index.php?option=com_vikrestaurants&tmpl=component&task=customer.edit&cid[]=' + $('#vr-users-select').val();

				// open modal
				openModal('managecustomer', url, true);
			});

			$('button[data-role="customer.save"]').on('click', function() {
				// trigger click of save button contained in managecustomer view
				w.modalCustomerSaveButton.click();
			});

			$('#jmodal-managecustomer').on('hidden', function() {
				// restore default submit function, which might have been
				// replaced by the callback used in manage customer view
				Joomla.submitbutton = w.ManageReservationSubmitButtonCallback;
				
				// check if the customer was saved
				if (w.modalSavedCustomerData) {
					let data = w.modalSavedCustomerData;

					// register billing details (or update them if already exist)
					BILLING_USERS_POOL[data.id] = data;

					// insert/update customer
					$('#vr-users-select').select2('data', data).trigger('customer.refresh');
				}
			});
		});
	})(jQuery, window);
</script>