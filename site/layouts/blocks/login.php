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

$canRegister = isset($displayData['register']) ? $displayData['register']         : false;
$returnUrl   = isset($displayData['return'])   ? $displayData['return']           : '';
$remember    = isset($displayData['remember']) ? $displayData['remember']         : false;
$useCaptcha  = isset($displayData['captcha'])  ? $displayData['captcha']          : null;
$gdpr        = isset($displayData['gdpr'])     ? $displayData['gdpr']             : null;
$footerLinks = isset($displayData['footer'])   ? $displayData['footer']           : true;
$active      = isset($displayData['active'])   ? $displayData['active']           : 'login';
$style       = isset($displayData['style'])    ? $displayData['style']            : null;
$formId      = isset($displayData['form'])     ? ltrim($displayData['form'], '#') : null;

$app    = JFactory::getApplication();
$vik    = VREApplication::getInstance();
$config = VREFactory::getConfig();

if (is_null($useCaptcha))
{
	// check if 'recaptcha' is configured
	$useCaptcha = $vik->isCaptcha();
}

if (($tab = $app->input->get('tab')))
{
	// overwrite pre-selected tab with the one set in request
	$active = $tab;
}

if ((!$canRegister && $active == 'registration') || !in_array($active, ['login', 'registration']))
{
	// restore active tab to "login" as the registration is disabled
	$active = 'login';
}

if (is_null($gdpr))
{
	// gdpr setting not provided, get it from the global configuration
	$gdpr = $config->getBool('gdpr', false);
}

if ($footerLinks)
{
	// load com_users site language to display footer messages
	JFactory::getLanguage()->load('com_users', JPATH_SITE, JFactory::getLanguage()->getTag(), true);
}

if (!$style)
{
	/**
	 * Load from the configuration the style to use for the custom fields.
	 * 
	 * @since 1.9
	 */
	$style = $config->get('fields_layout_style', 'default');
}

if ($canRegister)
{
	/**
	 * In case of failed registration, the user details are stored
	 * within the user state. We can fetch them to auto-populate 
	 * the registration form.
	 *
	 * @since 1.9
	 */
	$data = $app->getUserState('vre.cms.user.register', []);
	// immediately unset the specified user data to avoid displaying
	// them again in case the user decides to leave the page
	$app->setUserState('vre.cms.user.register', null);

	// create user registration fields
	$userRegisterFieldsProvider = new E4J\VikRestaurants\CustomFields\Providers\UserRegisterFieldsProvider([
		'gdpr'    => $gdpr,
		'captcha' => $useCaptcha,
	]);

	// setup registration fields collection
	$userRegisterFields = new E4J\VikRestaurants\CustomFields\FieldsCollection($userRegisterFieldsProvider);

	// create fields renderer
	$renderer = new E4J\VikRestaurants\CustomFields\FieldsRenderer($userRegisterFields);
	?>

	<!-- REGISTRATION -->
	
	<script>
		(function($, w) {
			'use strict';

			w.vreUserRegistrationValidator = null;

			w.vrLoginValueChanged = () => {
				if ($('input[name=loginradio]:checked').val() == 1) {
					$('.vrregisterblock').css('display', 'none');
					$('.vrloginblock').fadeIn();
				} else {
					$('.vrloginblock').css('display', 'none');
					$('.vrregisterblock').fadeIn();
				}
			}

			$(function() {
				// in case of a specified form, register only the fields contained within the registration wrapper
				const formSelector = '<?php echo $formId ? '#' . $formId . ' .vrregform' : '#vrregform'; ?>';

				// create validator once the document is ready, because certain themes
				// might load the resources after the body
				w.vreUserRegistrationValidator = new VikFormValidator(formSelector, 'vrinvalid');

				// register callback to make sure both the password fields are equals
				w.vreUserRegistrationValidator.addCallback((form) => {
					const pwd1 = $('#vrcfregister_password');
					const pwd2 = $('#vrcfregister_conf_password');

					if (!pwd1.val() || (pwd1.val() !== pwd2.val())) {
						// the specified password are not matching
						form.setInvalid($(pwd1).add(pwd2));
						return false;
					}

					// the specified password are equals
					form.unsetInvalid($(pwd1).add(pwd2));
					return true;
				});

				<?php if ($gdpr): ?>
					// in case of GDPR enabled, validate the disclaimer checkbox
					w.vreUserRegistrationValidator.addCallback((form) => {
						const field = $('#vrcfregister_gdpr');

						if (!field.is(':checked')) {
							// not checked
							form.setInvalid(field);
							return false;
						}

						// checked
						form.unsetInvalid(field);
						return true;
					});
				<?php endif; ?>

				<?php if ($useCaptcha): ?>
					// make sure the captcha has been validated
					w.vreUserRegistrationValidator.addCallback((form) => {
						// get recaptcha elements
						const captcha = $(form.form).find('.g-recaptcha').first();
						const iframe  = captcha.find('iframe').first();

						// get widget ID
						let widget_id = captcha.data('recaptcha-widget-id');

						// check if recaptcha instance exists
						// and whether the recaptcha was completed
						if (typeof grecaptcha !== 'undefined'
							&& widget_id !== undefined
							&& !grecaptcha.getResponse(widget_id)) {
							// captcha not completed
							iframe.addClass(form.clazz);
							return false;
						}

						// captcha completed
						iframe.removeClass(form.clazz);
						return true;
					});
				<?php endif; ?>

				$(formSelector).find('button[name="registerbutton"]').on('click', function(event) {
					if (!w.vreUserRegistrationValidator.validate()) {
						event.preventDefault();
						event.stopPropagation();
						return false;
					}

					// find parent form
					const formElement = $(this).closest('form');

					<?php if ($formId): ?>
						// check if we have an option field within our form
						let optionField = $(formElement).find('input[name="option"]');

						if (!optionField.length) {
							// nope, create it and append it at the end of the form
							optionField = $('<input type="hidden" name="option" value="" />');
							$(formElement).append(optionField);
						}

						// register the correct option value
						optionField.val('com_vikrestaurants');

						// check if we have a task field within our form
						let taskField = $(formElement).find('input[name="task"]');

						if (!taskField.length) {
							// nope, create it and append it at the end of the form
							taskField = $('<input type="hidden" name="task" value="" />');
							$(formElement).append(taskField);
						}

						// register the correct task value
						taskField.val('userprofile.register');

						// check if we have a return field within our form
						let returnField = $(formElement).find('input[name="return"]');

						if (!returnField.length) {
							// nope, create it and append it at the end of the form
							returnField = $('<input type="hidden" name="return" value="" />');
							$(formElement).append(returnField);
						}

						// register the correct return value
						returnField.val('<?php echo base64_encode($returnUrl); ?>');
					<?php endif; ?>

					return true;
				});
			});
		})(jQuery, window);
	</script>

	<div class="vrloginradiobox" id="vrloginradiobox">
		<span class="vrloginradiosp">
			<label for="logradio1"><?php echo JText::translate('VRLOGINRADIOCHOOSE1'); ?></label>
			<input type="radio" id="logradio1" name="loginradio" value="1" onChange="vrLoginValueChanged();" <?php echo $active == 'login' ? 'checked="checked"' : ''; ?> />
		</span>
		<span class="vrloginradiosp">
			<label for="logradio2"><?php echo JText::translate('VRLOGINRADIOCHOOSE2'); ?></label>
			<input type="radio" id="logradio2" name="loginradio" value="2" onChange="vrLoginValueChanged();" <?php echo $active != 'login' ? 'checked="checked"' : ''; ?> />
		</span>
	</div>

	<div class="vrregisterblock" style="<?php echo $active != 'login' ? '' : 'display: none;'; ?>">
		<?php
		if ($formId)
		{
			// the registration is already contained within a parent form
			?>
			<div class="vrregform">
			<?php
		}
		else
		{
			// wrap the registration fields within a form
			?>
			<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants'); ?>" method="post" name="vrregform" id="vrregform">
			<?php
		}
		?>

			<h3><?php echo JText::translate('VRREGISTRATIONTITLE'); ?></h3>
			
			<div class="vrloginfieldsdiv custom-fields-<?php echo $this->escape($style); ?>">

				<?php
				// wrap the field within the chosen control style
				$renderer->setControl('form.control.' . $style, [
					'validator' => 'vreUserRegistrationValidator',
				]);

				echo $renderer->display($data);
				?>

				<div class="vrloginfield field-button">
					<button type="submit" class="vre-btn primary large" name="registerbutton">
						<?php echo JText::translate('VRREGSIGNUPBTN'); ?>
					</button>
				</div>

			</div>
	
			<?php echo JHtml::fetch('form.token'); ?>

		<?php if ($formId): ?>
			</div>
		<?php else: ?>
				<input type="hidden" name="option" value="com_vikrestaurants" />
				<input type="hidden" name="task" value="userprofile.register" />
				<input type="hidden" name="return" value="<?php echo base64_encode($returnUrl); ?>" />
			</form>
		<?php endif; ?>
		</form>
	</div>

<?php } ?>

<!-- LOGIN -->

<div class="vrloginblock" style="<?php echo $active == 'login' ? '' : 'display: none;'; ?>">
	<?php
	/**
	 * The login form is displayed from the layout below:
	 * /components/com_vikrestaurants/layouts/blocks/login/joomla.php (joomla)
	 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/login/wordpress.php (wordpress)
	 *
	 * @since 1.8
	 */
	echo $this->sublayout(VersionListener::getPlatform(), $displayData);
	?>
</div>
