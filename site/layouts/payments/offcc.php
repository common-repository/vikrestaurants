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
 * Layout variables
 * -----------------
 * @var  array  $data    An associative array containing the transaction details.
 * @var  array  $params  An associative array with the payment configuration.
 */
extract($displayData);

/**
 * Use the correct credit card brands URL according to the current platform.
 * 
 * @since 1.9.1
 */
if (VersionListener::isJoomla())
{
	$ccBrandUrl = VREADMIN_URI;
}
else
{
	$ccBrandUrl = VRE_BASE_URI . 'libraries/';
}

?>

<form action="<?php echo $data['notify_url']; ?>" method="post" name="offlineccpaymform" id="offlineccpaymform">

	<div class="offcc-payment-wrapper">

		<div class="offcc-payment-box">

			<!-- ACCEPTED BRANDS -->
			<div class="offcc-payment-field">

				<div class="offcc-payment-field-wrapper">
					<?php foreach ($params['brands'] as $brand): ?>
						<img src="<?php echo $ccBrandUrl . 'payments/off-cc/resources/icons/' . $brand . '.png'; ?>" title="<?php echo $this->escape($brand); ?>" alt="<?php echo $this->escape($brand); ?>" /> 
					<?php endforeach; ?>
				</div>

			</div>

			<!-- CARDHOLDER NAME -->
			<div class="offcc-payment-field">

				<div class="offcc-payment-field-wrapper">
					<span class="offcc-payment-icon">
						<i class="fas fa-user"></i>
					</span>
					
					<input type="text" name="cardholder" value="<?php echo $this->escape($data['details']['purchaser_nominative']); ?>" placeholder="<?php echo $this->escape(JText::translate('VRCCNAME')); ?>" />
				</div>

			</div>

			<!-- CREDIT CARD -->
			<div class="offcc-payment-field">

				<div class="offcc-payment-field-wrapper">
					<span class="offcc-payment-icon">
						<i class="fas fa-credit-card"></i>
					</span>
				
					<input type="text" name="cardnumber" value="" placeholder="<?php echo $this->escape(JText::translate('VRCCNUMBER')); ?>" maxlength="16" autocomplete="off" />
				
					<span class="offcc-payment-cctype-icon" id="credit-card-brand"></span>
				</div>

			</div>

			<!-- EXPIRY DATE AND CVC -->
			<div class="offcc-payment-field">

				<!-- EXP DATE -->
				<div class="offcc-payment-field-wrapper inline">
					<span class="offcc-payment-icon">
						<i class="fas fa-calendar-alt"></i>
					</span>
					
					<input type="text" name="expdate" value="" placeholder="<?php echo $this->escape(JText::translate('VREXPIRINGDATEFMT')); ?>" class="offcc-small" maxlength="7" />
				</div>

				<!-- CVC -->
				<div class="offcc-payment-field-wrapper inline">
					<span class="offcc-payment-icon">
						<i class="fas fa-lock"></i>
					</span>
					
					<input type="text" name="cvc" value="" placeholder="<?php echo $this->escape(JText::translate('VRCVV')); ?>" class="offcc-small" maxlength="4" autocomplete="off" />
				</div>

			</div>

			<!-- SUBMIT -->
			<div class="offcc-payment-field">

				<div class="offcc-payment-field-wrapper inline">
					<button type="submit" onclick="return validateCreditCardForm();" class="vre-btn primary big"><?php echo JText::translate('VREORDERFOOD_PAY_NOW'); ?></button>
				</div>

			</div>

		</div>

	</div>

	<?php echo JHtml::fetch('form.token'); ?>

</form>
