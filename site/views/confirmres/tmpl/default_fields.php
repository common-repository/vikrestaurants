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

<h3 class="vr-confirm-h3"><?php echo JText::translate('VRCOMPLETERESHEADTITLE'); ?></h3>

<!-- ERROR BOX -->

<div id="vrordererrordiv" class="vrordererrordiv" style="display: none;">
	
</div>

<!-- FIELDS -->

<div class="vrcustomfields custom-fields-<?php echo $this->escape($fieldsLayoutStyle); ?>">
	<?php
	// create custom fields renderer for the restaurant group
	$renderer = new E4J\VikRestaurants\CustomFields\FieldsRenderer($this->customFields);

	// wrap the field within the chosen control style
	$renderer->setControl('form.control.' . $fieldsLayoutStyle, [
		'validator' => 'vrCustomFieldsValidator',
	]);

	// fetch custom fields already saved for this user
	$userFields = $this->user ? $this->user->fields->restaurant : [];

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

		$(function() {
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