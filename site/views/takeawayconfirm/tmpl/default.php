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

JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.googlemaps');
JHtml::fetch('vrehtml.assets.fontawesome');

$config = VREFactory::getConfig();

/**
 * Get login requirements:
 * [1] - Never
 * [2] - Optional
 * [3] - Required on confirmation page
 */
$login_req = $config->getUint('tkloginreq');

// If the login is mandatory/optional and the customer is not logged in, we need to show
// a form to allow the customers to login or at least to create a new account.
if ($login_req > 1 && JFactory::getUser()->guest)
{
	// display login/registration form
	echo $this->loadTemplate('login');
	
	// do not go ahead in case the login is mandatory
	if ($login_req > 2)
	{
		return;
	}
}

// display cart summary by using a sub-template
echo $this->loadTemplate('cart');
?>

<!-- Continue shopping button -->

<div class="vrtkaddmoreitemsdiv">
	<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeaway' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vre-btn primary small">
		<?php echo JText::translate('VRTKADDMOREITEMS'); ?>
	</a>
</div>

<!-- Search parameters form -->

<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayconfirm' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" name="vrtkconfirmform" id="vrtkconfirmform" method="post">

	<?php
	// checks whether the take-away section uses the coupon codes
	if ($this->anyCoupon)
	{
		// display form to redeem coupon with a sub-template
		echo $this->loadTemplate('coupon');
	}
	
	// display search bar (date, time, service)
	echo $this->loadTemplate('search');
	?>
	
	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="view" value="takeawayconfirm" />

	<?php echo JHtml::fetch('form.token'); ?>

</form>

<!-- Confirmation form -->

<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&task=takeawayconfirm.saveorder' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" name="vrpayform" id="vrpayform" method="post">

	<?php
	$step = 0;

	// make sure there are custom fields to collect
	if ($this->customFields)
	{
		// display custom fields using a sub-template
		echo $this->loadTemplate('fields');
	}
	else
	{
		$step = 1;
	}

	// make sure there is at least a payment available
	if (count($this->payments))
	{
		?>
		<div class="vr-payments-list" id="vrpaymentsdiv" style="<?php echo $step == 0 ? 'display: none;' : ''; ?>">
			<?php
			// display payments using a sub-template
			echo $this->loadTemplate('payments');
			?>
		</div>
		<?php
	}
	else
	{
		$step = 1;
	}
	?>

	<button type="button" id="vre-conf-continue-btn" class="vre-btn primary big" onClick="vrContinueButton(this);">
		<?php echo JText::translate($step == 0 ? 'VRCONTINUE' : 'VRTKCONFIRMORDER'); ?>
	</button>

	<input type="hidden" name="date" value="<?php echo $this->escape($this->args['date']); ?>" />
	<input type="hidden" name="hourmin" value="<?php echo $this->escape($this->args['hourmin']); ?>" />
	<input type="hidden" name="service" value="<?php echo $this->escape($this->args['service']); ?>" />
	<input type="hidden" name="gratuity" value="0" />

	<?php echo JHtml::fetch('form.token'); ?>

</form>

<?php
JText::script('VRCONFRESFILLERROR');
JText::script('VRTKCONFIRMORDER');
?>

<script>
	(function($, w) {
		'use strict';

		let CONFIRMATION_STEP = <?php echo (int) $step; ?>;

		w.vrContinueButton = (button) => {
			// validate custom fields
			if (!w.vrCustomFieldsValidator.validate()) {
				// display error message
				$('#vrordererrordiv').html(Joomla.JText._('VRCONFRESFILLERROR')).show();

				// get first invalid input
				let input = $('.vrcustomfields .vrinvalid').filter('input,textarea').first();

				if (input.length == 0) {
					// the label is displayed before the input, get it
					input = $('.vrcustomfields .vrinvalid').first();
				}

				// animate to element found
				if (input.length) {
					$('html,body').stop(true, true).animate({
						scrollTop: ($(input).offset().top - 100),
					}, {
						duration:'medium'
					}).promise().done(() => {
						// try to focus the input
						$(input).focus();
					});
				}

				// do not go ahead in case of error
				return;
			}

			// hide error message
			$('#vrordererrordiv').html('').hide();

			if (CONFIRMATION_STEP == 0) {
				// display payment gateways
				$('#vrpaymentsdiv').show();

				// change button text
				$(button).text(Joomla.JText._('VRTKCONFIRMORDER'));

				// increase step and do not go ahead
				CONFIRMATION_STEP++;
				return;
			}

			// do not validate payment gateways selection
			// because the first payment available, if any,
			// is now pre-selected by default

			<?php
			/**
			 * Disable book now button before submitting the
			 * form in order to prevent several clicks.
			 *
			 * @since 1.8
			 */
			?>
			$(button).prop('disabled', true);

			// copy search arguments within the form
			$('#vrpayform input[name="hourmin"]').val($('#vrtkconfirmform select[name="hourmin"]').val());
			$('#vrpayform input[name="service"]').val(vrGetSelectedService());
			$('#vrpayform input[name="gratuity"]').val(vrGetGratuity());

			<?php
			/**
			 * Check whether we should flag the order as ASAP.
			 * This should occur only in case the check-in date is equals
			 * to the current date.
			 * 
			 * @since 1.9
			 */
			if (VikRestaurants::createTimestamp($this->args['date']) == strtotime('today 00:00:00')): ?>
				// check whether the user selected the first available time
				if ($('#vrtktime option:selected').index() == 0) {
					// append an input to know that this order should be prepared as soon as possible
					$('#vrpayform').append('<input type="hidden" name="asap" value="1" />');
				}
			<?php endif; ?>

			$('#vrpayform').submit();
		}

		w.vrIsDeliveryMap = () => {
			return typeof VRTK_ADDR_MARKER !== 'undefined';
		}

		$(function() {
			w.vrCustomFieldsValidator = new VikFormValidator('#vrpayform', 'vrinvalid');
		});
	})(jQuery, window);
</script>