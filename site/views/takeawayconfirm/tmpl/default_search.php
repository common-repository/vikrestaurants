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

/**
 * Template file used to to display the search bar.
 * It is possible to choose from here the check-in date
 * (if allowed), the check-in time and the type of service
 * (delivery or pick-up). 
 *
 * @since 1.8
 */

$config = VREFactory::getConfig();

$currency = VREFactory::getCurrency();

// calculate total cost before taxes
$total_cost = $this->cart->getTotalCost();

/**
 * Validate free delivery by using the apposite helper method.
 *
 * @since 1.8.3
 */
if (VikRestaurants::isTakeAwayFreeDeliveryService($this->cart))
{
	$is_free_delivery = true;
}
else
{
	$is_free_delivery = false;
}

?>

<div class="vrtk-service-dt-wrapper">
		
	<!-- DATE AND TIME -->

	<div class="vrtkdatetimediv">

		<div class="vrtkdeliverytitlediv">
			<?php
			if ($config->getBool('tkallowdate'))
			{
				// date selection is allowed
				echo JText::translate('VRTKDATETIMELEGEND');
			}
			else
			{
				// pre-orders for future days are disabled
				echo JText::translate('VRTKONLYTIMELEGEND');
			}
			?>
		</div>
		
		<?php
		if ($config->getBool('tkallowdate'))
		{
			// display datepicker only if date selection is allowed
			JHtml::fetch('vrehtml.sitescripts.datepicker', '#vrtkcalendar:input', 'takeaway');
			?>
			<div class="vrtkdatetimeinputdiv vrtk-date-box">
				
				<label class="vrtkdatetimeinputlabel" for="vrtkcalendar">
					<?php echo JText::translate('VRDATE'); ?>
				</label>
				
				<div class="vrtkdatetimeinput vre-calendar-wrapper">
					<input class="vrtksearchdate vre-calendar" type="text" value="<?php echo $this->escape($this->args['date']); ?>" id="vrtkcalendar" name="date" size="20" />
				</div>

			</div>
			<?php
		}
		
		if (count($this->times))
		{
			// display times dropdown only in case there is at least a time available
			?>
			<div class="vrtkdatetimeinputdiv vrtk-time-box">
				
				<label class="vrtkdatetimeinputlabel" for="vrtktime">
					<?php echo JText::translate('VRTIME'); ?>
				</label>
				
				<div class="vrtkdatetimeselect vre-select-wrapper">
					<?php
					$attrs = [
						'id'    => 'vrtktime',
						'class' => 'vre-select',
					];

					// display times dropdown
					echo JHtml::fetch('vrehtml.site.timeselect', 'hourmin', $this->args['hourmin'], $this->times, $attrs);
					?>
				</div>
			
			</div>
			<?php
		}
		else
		{
			// no available times, the restaurant is probably closed or it is out of orders
			?>
			<div class="vrtkdatetimeerrmessdiv">
				<div class="vrtkdatetimenoselectdiv"><?php echo JText::translate('VRTKNOTIMEAVERR'); ?></div>
			</div>
			<?php
		}
		?>
	</div>

	<!-- DELIVERY/PICK-UP SERVICE -->

	<div class="vrtkdeliveryservicediv">

		<div class="vrtkdeliverytitlediv">
			<?php echo JText::translate('VRTKSERVICELABEL'); ?>
		</div>

		<div class="vrtkdeliveryradiodiv">
			<?php
			// calculate delivery charge
			$delivery_charge = VikRestaurants::getTakeAwayDeliveryServiceAddPrice($total_cost);

			if ($delivery_charge > 0)
			{
				if ($is_free_delivery)
				{
					// didplay FREE label only in case of offer
					$label = '(' . JText::translate('VRTKDELIVERYFREE') . ')';
				}
				else
				{
					// display charge
					$label = '(+' . $currency->format($delivery_charge) . ')';
				}
			}
			else
			{
				$label = '';
			}

			if ($this->delivery)
			{
				?>
				<span class="vrtkdeliverysp">
					<input type="radio" name="service" value="delivery" id="vrtkdelivery1" onChange="vrServiceChanged(this.value);" <?php echo $this->args['service'] == 'delivery' ? 'checked="checked"' : ''; ?> />

					<label for="vrtkdelivery1"><?php echo trim(JText::sprintf('VRTKDELIVERYLABEL', $label)); ?></label>
				</span>
				<?php
			}

			// calculate pickup charge
			$pickup_charge = VikRestaurants::getTakeAwayPickupAddPrice($total_cost);

			if ($pickup_charge != 0)
			{
				$label = '(' . ($pickup_charge > 0 ? '+' : '') . $currency->format($pickup_charge) . ')';
			}
			else
			{
				$label = '';
			}

			if ($this->pickup)
			{
				?>
				<span class="vrtkpickupsp">
					<input type="radio" name="service" value="pickup" id="vrtkdelivery0" onChange="vrServiceChanged(this.value);" <?php echo $this->args['service'] == 'pickup' ? 'checked="checked"' : ''; ?> />
					
					<label for="vrtkdelivery0"><?php echo trim(JText::sprintf('VRTKPICKUPLABEL', $label)); ?></label>
				</span>
				<?php
			}
			?>
		</div>

	</div>

</div>

<?php
JText::script('VRTKDELIVERYADDRNOTFULL');
JText::script('VRTKDELIVERYADDRNOTFOUND');
JText::script('VRTKDELIVERYMINCOST');
JText::script('VRTKDELIVERYSURCHARGE');
JText::script('VRTKADDITEMERR2');
?>

<script>
	(function($, w) {
		'use strict';

		// flag used to check whether the Google API Key was badly configured
		let GOOGLE_AUTH_ERROR = <?php echo $config->getBool('googleapikey') ? 'false' : 'true'; ?>;

		// tracks all the address components fetched during the last validation
		let LAST_COMPONENTS_FOUND = {};

		// flag used to check whether the provided address can be accepted or not
		let DELIVERY_ADDRESS_STATUS = <?php echo (int) $this->args['service'] !== 'delivery'; ?>;

		// globally declare some variables holding a few information about the cost of the services
		let TK_FREE_DELIVERY = <?php echo $is_free_delivery ? 1 : 0; ?>;

		// register here the ID of the area the specified address belongs to
		w.TK_DELIVERY_AREA = 0;

		const evaluateCoordinatesFromAddress = (address) => {
			return new Promise((resolve, reject) => {
				// do not go ahead in case Google Maps failed the authentication
				if (VikGMapsUtils.hasError()) {
					reject('google.error');
					return;
				}

				// make sure google JS object is available
				if (typeof google === 'undefined') {
					reject('google.undefined');
					return;
				}

				// make sure google.maps JS object is available
				if (typeof google.maps === 'undefined') {
					reject('google.maps.undefined');
					return;
				}

				// make sure Geocoder API are supported before proceeding with the validation
				if (typeof google.maps.Geocoder === 'undefined') {
					reject('google.maps.geocoder.undefined');
					return;
				}

				const geocoder = new google.maps.Geocoder();

				geocoder.geocode({'address': address}, (results, status) => {
					if (status == 'OK') {
						// extract components from place
						const components = VikGeo.extractDataFromPlace(results[0]);

						// include full address
						components.fullAddress = results[0].formatted_address;

						resolve(components);
					} else {
						reject({
							code: 'google.maps.geocoder.failed',
							status: status,
							results: results,
						});
					}
				});
			});
		}

		const getLocationDeliveryInfo = (query) => {
			return new Promise((resolve, reject) => {
				// Filter the query to obtain only the values set.
				// In case of no values, save a request and avoid to display an error message.
				if (Object.values(query).filter(v => v).length === 0) {
					reject(null);
					return;
				}

				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=takeawayconfirm.getlocationinfo' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>',
					{
						query: query,
					},
					(data) => {
						resolve(data);
					},
					(error) => {
						if (!error.responseText || error.responseText.length > 1024) {
							// use default generic error
							error.responseText = Joomla.JText._('VRTKADDITEMERR2');
						}

						reject(error.responseText);
					}
				);
			});
		}

		const fetchAddressComponents = () => {
			const customFields = $('.vrcustomfields');

			// reset components
			LAST_COMPONENTS_FOUND = {};

			// get ZIP value
			const zipField = customFields.find('.field-zip');

			if (zipField.length) {
				if (zipField.is('select')) {
					// the field is a select, take the zip code from the text of the selected option
					LAST_COMPONENTS_FOUND.zip = zipField.find('option:selected').text();
				} else {
					LAST_COMPONENTS_FOUND.zip = zipField.val();
				}
			}

			// get CITY value
			const cityField = customFields.find('.field-city');

			if (cityField.length) {
				if (cityField.is('select')) {
					// the field is a select, take the city from the text of the selected option
					LAST_COMPONENTS_FOUND.city = cityField.find('option:selected').text();
				} else {
					LAST_COMPONENTS_FOUND.city = cityField.val();
				}
			}

			// get ADDRESS value
			const addressField = customFields.find('.field-address');

			if (addressField.length) {
				LAST_COMPONENTS_FOUND.address = addressField.val();
			}
		}

		w.vrGetSelectedService = () => {
			return $('form#vrtkconfirmform input[name="service"]:checked').val();
		}

		w.vrIsDelivery = () => {
			// check whether the customer selected the delivery service
			return vrGetSelectedService() == 'delivery';
		}

		w.vrIsAddressAccepted = (components) => {
			DELIVERY_ADDRESS_STATUS = 0;

			LAST_COMPONENTS_FOUND = components;

			// unset address error
			w.vrSetAddressResponse();
				
			if (!vrIsDelivery()) {
				// don't need to validate the address
				DELIVERY_ADDRESS_STATUS = 1;

				// still refresh the grand total on complete (takeaway service)
				vrRefreshGrandTotal();
				return;
			}

			// reset delivery area ID
			TK_DELIVERY_AREA = 0;

			getLocationDeliveryInfo(components).then((info) => {
				if (!info.area) {
					// Delivery area not found, probably the locations haven't been configured.
					// Create an empty placeholder to preserve the same behavior.
					info.area = {
						id: 0,
						min_cost: 0,
						charge: 0,
					};
				}

				/**
				 * Compare minimum cost with total cost before taxes.
				 *
				 * @since 1.8
				 */
				if (info.area.min_cost > w.TK_BASE_TOTAL) {
					// set min delivery cost error
					vrSetAddressResponse(Joomla.JText._('VRTKDELIVERYMINCOST').replace('%s', info.texts.minCost), true);
				} else {
					if (!TK_FREE_DELIVERY) {
						TK_DELIVERY_AREA = info.area.id;

						if (info.area.charge != 0) {
							// set delivery surcharge notice
							vrSetAddressResponse(Joomla.JText._('VRTKDELIVERYSURCHARGE').replace('%s', info.texts.charge));
						}
					}

					DELIVERY_ADDRESS_STATUS = 1;
				}
			}).catch((error) => {
				// set address error
				vrSetAddressResponse(error, true);
			}).finally(() => {
				// refresh grand total on complete (delivery service)
				vrRefreshGrandTotal();
			});
		}

		w.vrValidateAddress = (address) => {
			if (address.length === 0) {
				// always reset the last components found after clearing all the inputs
				LAST_COMPONENTS_FOUND = {};
			}

			if ($('.vrcustomfields').find('.field-address').length == 0 || GOOGLE_AUTH_ERROR) {
				// address field not available or Google error, we should validate
				// the deliverability with the information we already have
				vrIsAddressAccepted(LAST_COMPONENTS_FOUND);
				return;
			}

			// unset address error
			vrSetAddressResponse();

			DELIVERY_ADDRESS_STATUS = 0;
			
			if (vrIsDeliveryMap() && VRTK_ADDR_MARKER !== null) {
				// unset marker from map module
				VRTK_ADDR_MARKER.setMap(null);
			}

			if (address.length == 0) {
				// properly apply the delivery charge
				vrRefreshGrandTotal();
				return false;
			}

			evaluateCoordinatesFromAddress(address).then((components) => {
				if (!components.street.name && !components.street.number) {
					// set address error
					vrSetAddressResponse(Joomla.JText._('VRTKDELIVERYADDRNOTFULL'), true);
					return false;
				}

				if (vrIsDeliveryMap()) {
					if (VRTK_ADDR_MARKER) {
						// update position of existing marker
						VRTK_ADDR_MARKER.setPosition({
							lat: components.lat,
							lng: components.lng,
						});
					} else {
						// create marker from scratch
						VRTK_ADDR_MARKER = new google.maps.Marker({
							position: {
								lat: components.lat,
								lng: components.lng,
							},
						});
					}

					VRTK_ADDR_MARKER.setAnimation(google.maps.Animation.DROP);
					VRTK_ADDR_MARKER.setMap(VRTK_MAP);

					VRTK_MAP.setCenter(VRTK_ADDR_MARKER.position);
				}

				// VALIDATION
				vrIsAddressAccepted(components);
			}).catch((error) => {
				if (typeof error === 'object') {
					<?php
					/**
					 * Raise an error message as it wasn't possible
					 * to find the specified address.
					 *
					 * @since 1.7.4
					 */
					?>
					vrSetAddressResponse(Joomla.JText._('VRTKDELIVERYADDRNOTFOUND'), true);
				}
			});
		}

		w.vrServiceChanged = (service) => {
			<?php if (!$this->refreshServiceNeeded): ?>
				if (typeof service === 'undefined') {
					// get selected service
					service = $('form#vrtkconfirmform input[name="service"]:checked').val();
				}

				// revalidate the last components found
				// w.vrIsAddressAccepted(LAST_COMPONENTS_FOUND);
				w.vrValidateAddress(w.vrGetAddressString());

				// toggle custom fields according to the selected service
				w.vrToggleServiceRequiredFields(service);
			<?php endif; ?>
		}

		$(function() {
			// recover specified address components
			fetchAddressComponents();

			// refresh page in case the check-in date changes
			$('#vrtkcalendar').on('change', () => {
				$('#vrtkconfirmform').submit();
			});

			<?php if ($this->refreshTimeNeeded): ?>
				// refresh page after changing the time because the
				// system might support different services/menus
				$('#vrtktime').on('change', () => {
					$('#vrtkconfirmform').submit();
				});
			<?php endif; ?>

			<?php if ($this->refreshServiceNeeded): ?>
				// refresh page after changing the service because the
				// system might support different deals
				$('input[name="service"]').on('change', () => {
					$('#vrtkconfirmform').submit();
				});

				// revalidate the last components found
				w.vrValidateAddress(w.vrGetAddressString());

				// toggle custom fields according to the selected service
				w.vrToggleServiceRequiredFields(w.vrGetSelectedService());
			<?php endif; ?>

			// toggle service change to update delivery/pickup charge
			vrServiceChanged();

			// wait until the validator is ready
			onInstanceReady(() => {
				return w.vrCustomFieldsValidator;
			}).then((validator) => {
				// add address validation callback to form validator
				validator.addCallback(() => {
					// ignore address validation in case the service is not "delivery"
					if (vrIsDelivery() === false) {
						return true;
					}

					// get address and ZIP code fields
					const fields = $('.vrcustomfields').find('.field-address, .field-zip, .field-city');

					if (!DELIVERY_ADDRESS_STATUS) {
						// set fields as invalid
						validator.setInvalid(fields);
					} else {
						// unset fields as invalid
						validator.unsetInvalid(fields);
					}

					// valid in case the specified address is accepted or
					// in case there are no fields to use for the validation
					return DELIVERY_ADDRESS_STATUS || fields.length == 0;
				});
			});

			const customFields = $('.vrcustomfields');

			// fetch all address fields
			const addressFields = customFields.find('.field-address')
				.prop('autocomplete', 'off')
				.on('change', () => {
					w.vrValidateAddress(w.vrGetAddressString());
				});

			// fetch all zip fields
			const zipFields = customFields.find('.field-zip')
				.on('change', function() {
					let zip = $(this).val();

					if ($(this).is('select')) {
						// the field is a select, take the ZIP code from the text of the selected option
						zip = $(this).find('option:selected').text();
					}

					// re-validate address only in case the post code changed
					if (LAST_COMPONENTS_FOUND.zip == zip) {
						// nothing has changed
						return true;
					}

					// overwrite post code
					LAST_COMPONENTS_FOUND.zip = zip;

					// revalidate address
					w.vrValidateAddress(w.vrGetAddressString());
				});

			// fetch all the city fields
			const cityFields = customFields.find('.field-city')
				.on('change', function() {
					let city = $(this).val();

					if ($(this).is('select')) {
						// the field is a select, take the city from the text of the selected option
						city = $(this).find('option:selected').text();
					}

					// re-validate address only in case the city changed
					if (LAST_COMPONENTS_FOUND.city == city) {
						// nothing has changed
						return true;
					}

					// overwrite city
					LAST_COMPONENTS_FOUND.city = city;

					// revalidate address
					w.vrValidateAddress(w.vrGetAddressString());
				});
		});

		$(window).on('google.autherror', () => {
			// google hasn't been properly configured
			GOOGLE_AUTH_ERROR = true;

			// fetch address components according to the value of the custom fields
			fetchAddressComponents();

			// make sure at least a field has been configured
			if (Object.values(LAST_COMPONENTS_FOUND).filter(v => v).length === 0) {
				// validate address according to the specified components
				vrIsAddressAccepted(LAST_COMPONENTS_FOUND);
			}
		});
	})(jQuery, window);
</script>