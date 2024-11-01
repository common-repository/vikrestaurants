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
 * Template file used to display the custom fields.
 *
 * @since 1.8
 */

$vik = VREApplication::getInstance();

/**
 * Load from the configuration the style to use for the custom fields.
 * 
 * @since 1.9
 */
$fieldsLayoutStyle = VREFactory::getConfig()->get('fields_layout_style', 'default');

?>

<!-- TITLE -->

<h3 class="vr-confirm-h3"><?php echo JText::translate('VRCOMPLETEORDHEADTITLE'); ?></h3>

<!-- ERROR BOX -->

<div id="vrordererrordiv" class="vrordererrordiv" style="display: none;">
	
</div>

<!-- FIELDS -->

<div class="vrcustomfields custom-fields-<?php echo $this->escape($fieldsLayoutStyle); ?>">
	<?php
	// create custom fields renderer for the take-away group
	$renderer = new E4J\VikRestaurants\CustomFields\FieldsRenderer($this->customFields);

	// wrap the field within the chosen control style
	$renderer->setControl('form.control.' . $fieldsLayoutStyle, [
		'validator' => 'vrCustomFieldsValidator',
	]);

	// fetch custom fields already saved for this user
	$userFields = $this->user ? $this->user->fields->takeaway : [];

	/**
	 * Extract name components from registered user name
	 * and fill the related custom fields.
	 *
	 * @since 1.8
	 */
	VikRestaurants::extractNameFields($this->customFields, $userFields);

	/**
	 * Render the custom fields form by using the apposite helper.
	 *
	 * Looking for a way to override the custom fields? Take a look
	 * at "/layouts/form/fields/" folder, which should contain all
	 * the supported types of custom fields.
	 *
	 * @since 1.9
	 */
	echo $renderer->display($userFields);

	/**
	 * Trigger event to retrieve an optional field that could be used to confirm the subscription to a mailing list.
	 * 
	 * NOTE: rather use the following hooks according to your needs.
	 * 
	 * If you want to implement a new custom field type (e.g. a radio button):
	 * - onLoadCustomFieldsTypes
	 * 
	 * If you want to implement a new custom field rule (e.g. a mailing list subscription):
	 * - onLoadCustomFieldsRules
	 * - onDispatchCustomFieldRule
	 * - onRenderCustomFieldRule
	 * 
	 * If you want to manually register a custom field at runtime without having to create it from the back-end:
	 * - onBeforeRegisterCustomFields
	 *
	 * @param 	array 	$user     The user details.
	 * @param 	array 	$options  An array of options.
	 *
	 * @return  string  The HTML to display.
	 *
	 * @since 	1.8
	 * @deprecated 1.10  New custom fields should be introduced only through the events provided by this framework.
	 */
	$html = VREFactory::getEventDispatcher()->triggerOnce('onDisplayMailingSubscriptionInput', [(array) $this->user]);
	
	// display field if provided
	if ($html)
	{
		?>
		<div class="control-mailing">
			<?php echo $html; ?>
		</div>
		<?php
	}

	/**
	 * Only in case of guest users, try to display the ReCAPTCHA validation form.
	 * 
	 * NOTE: `$this->user` refers to the customer record created by VikRestaurants.
	 * This means that a user will have to solve the CAPTCHA for the first reservation/order
	 * made, even if it is currently logged-in. Starting from the second reservation/order,
	 * `$this->user` won't be NULL and the condition below won't be verified.
	 *
	 * @since 1.8.2
	 */
	$is_captcha = !$this->user && $vik->isGlobalCaptcha();

	if ($is_captcha)
	{
		?>
		<div class="control-captcha">
			<?php echo $vik->reCaptcha(); ?>
		</div>
		<?php
	}
	?>
</div>

<script>
	(function($, w) {
		'use strict';

		<?php
		$pool = [];

		if ($this->user)
		{
			foreach ($this->user->locations as $addr)
			{
				$pool[$addr->id] = $addr;
			}
		}
		?>
		const USER_LOCATIONS_POOL = <?php echo json_encode($pool); ?>;

		w.vrToggleServiceRequiredFields = (service) => {
			// take all the fields that depend on a service
			const fields = $('.control-custom-field').filter('[class*="control-service-"]');

			// hide all the fields that do not match the selected service
			fields.not('.control-service-' + service).hide();
			// show only the fields that match the selected service
			fields.filter('.control-service-' + service).show();

			// do the same for the separators, which are treated in a different way
			const separators = $('.custom-field.separator').filter('[class*="service-"]');

			// hide all the separators that do not match the selected service
			separators.not('.service-' + service).hide();
			// show only the separators that match the selected service
			separators.filter('.service-' + service).show();

			<?php
			foreach ($this->customFields as $field)
			{
				if (!$field->get('required', false))
				{
					// skip field in case it is optional
					continue;
				}

				if (!$field->get('service', null))
				{
					// skip field in case it does not linked to a specific service
					continue;
				}

				// make field required/optional according to the selected service
				?>
				onInstanceReady(() => {
					return w.vrCustomFieldsValidator;
				}).then((validator) => {
					// obtain the service linked to the current field
					let fieldService = '<?php echo $field->get('service'); ?>';

					// get field
					let field = $('#<?php echo $field->getID(); ?>');

					if (fieldService === service) {
						// register field as required in case of same service
						validator.registerFields(field);
					} else {
						// register field as optional in case of different service
						validator.unregisterFields(field);
					}
				});
				<?php
			}
			?>
		}

		w.vrGetAddressString = () => {

			let parts = [];

			// extract address, ZIP code, city and state/province from custom fields
			$('.custom-field').filter('.field-address, .field-zip, .field-city, .field-state').each(function() {
				const val = $(this).val();

				if (val && val.length) {
					parts.push(val);
				}
			});

			return parts.join(', ');
		}

		w.vrSetAddressResponse = (text, error) => {
			// get address response box
			const addressResponse = $('.vrtk-address-response');

			if (error) {
				addressResponse.addClass('fail');
			} else {
				addressResponse.removeClass('fail');
			}

			if (text) {
				// set error
				addressResponse.html(text).show();
			} else {
				// clear error
				addressResponse.hide();
			}
		}

		$(function() {
			// handle global address field change
			$('#vrcfuser-address-sel').on('change', function() {
				// get selected location ID
				let id = parseInt($(this).val());

				const customFields = $('.vrcustomfields');

				const addr = customFields.find('.control-custom-field .field-address').first();
				const zip  = customFields.find('.control-custom-field .field-zip').first();
				const city = customFields.find('.control-custom-field .field-city').first();
				const note = customFields.find('.control-custom-field .field-deliverynotes').first();

				if (USER_LOCATIONS_POOL.hasOwnProperty(id)) {
					// get selected location
					let loc = USER_LOCATIONS_POOL[id];

					// fetch base address
					let addrStr = loc.address;

					if (loc.address_2) {
						// append extra notes
						addrStr += ' ' + loc.address_2;
					}

					if (city.length) {
						// set city within the related field
						city.val(loc.city || loc.state).trigger('blur');
					} else if (loc.city || loc.state) {
						// append city to address string
						addrStr += ', ' + (loc.city || loc.state);
					}

					if (zip.length) {
						// set ZIP code within the related field
						zip.val(loc.zip).trigger('blur');
					} else if (loc.zip) {
						// append ZIP Code to address string
						addrStr += ', ' + loc.zip;
					}

					if (note.length) {
						// set delivery notes within the related field
						note.val(loc.note).trigger('blur');
					}

					addr.val(addrStr).trigger('blur');
				} else {
					// location not found
					addr.val('').trigger('blur');
					zip.val('').trigger('blur');
					city.val('').trigger('blur');
					note.val('').trigger('blur');
				}

				if (addr.length) {
					// trigger address change to start the validation
					addr.trigger('change');
				} else {
					// fallback to ZIP code
					zip.trigger('change');
				}
			});

			<?php if ($is_captcha): ?>
				onInstanceReady(() => {
					return w.vrCustomFieldsValidator;
				}).then((validator) => {
					/**
					 * Add callback to validate whether the ReCAPTCHA quiz
					 * was completed or not.
					 *
					 * @return 	boolean  True if completed, false otherwise.
					 */
					validator.addCallback(() => {
						// get recaptcha elements
						const captcha = $('#vrpayform .g-recaptcha').first();
						const iframe  = captcha.find('iframe').first();

						// get widget ID
						let widget_id = captcha.data('recaptcha-widget-id');

						// check if recaptcha instance exists
						// and whether the recaptcha was completed
						if (typeof grecaptcha !== 'undefined'
							&& widget_id !== undefined
							&& !grecaptcha.getResponse(widget_id)) {
							// captcha not completed
							iframe.addClass('vrinvalid');
							return false;
						}

						// captcha completed
						iframe.removeClass('vrinvalid');
						return true;
					});
				});
			<?php endif; ?>
		});
	})(jQuery, window);
</script>