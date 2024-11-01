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
 * @var  object       $reservation  The object holding the reservation details.
 * @var  string       $table        The tabel secret key.
 * @var  bool         $error        Whether the user failed the PIN code.
 * @var  int|null     $itemid       The current menu item ID.
 */
extract($displayData);

// calculate the remaining attempts
$remaining = max(0, 3 - $reservation->pinattempts);

?>

<style>
	.pin-prompt-wrapper {
		margin: 0 auto;
		border: 1px solid #ddd;
		border-radius: 6px;
		max-width: 400px;
		text-align: center;
	}
	.pin-prompt-line {
		padding: 10px 10px 0 10px;
	}
	.pin-prompt-line.label {
		background-color: #ddd;
		font-size: 1.2em;
		padding-bottom: 10px;
	}
	.pin-prompt-line:last-child {
		padding-bottom: 10px;
	}
	.pin-prompt-line.input {
		display: flex;
		justify-content: center;
	}
	.pin-prompt-line.input > input {
		width: 48px !important;
		height: 48px !important;
		margin: 10px;
		font-size: 32px;
		text-align: center;
		caret-color: transparent;
		outline: none;
		cursor: default;
	}
	.pin-prompt-line.input > input[disabled] {
		opacity: 0.5;
		background-color: #f1f2f8;
	}
	.pin-prompt-line.input > input:focus {
		border: 3px solid #0e70a7;
	}
	/* Chrome, Safari, Edge, Opera */
	.pin-prompt-line.input > input::-webkit-outer-spin-button,
	.pin-prompt-line.input > input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	/* Firefox */
	.pin-prompt-line.input > input[type=number] {
		-moz-appearance: textfield;
	}
	.pin-prompt-line.submit > button {
		width: 100%;
	}
	.pin-prompt-line.error {
		color: #900;
	}
</style>

<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&task=orderdish.start' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" id="pin-prompt-form">

	<div class="pin-prompt-wrapper">

		<div class="pin-prompt-line label">
			<label for="pin-prompt-1"><?php echo JText::translate('VRE_QR_RES_PIN_LABEL'); ?></label>
		</div>

		<div class="pin-prompt-line input">
			<input type="number" class="pin-prompt" id="pin-prompt-1" step="1" pattern="[0-9]*" <?php echo $remaining == 0 ? 'disabled' : ''; ?> />
			<input type="number" class="pin-prompt" id="pin-prompt-2" step="1" pattern="[0-9]*" <?php echo $remaining == 0 ? 'disabled' : ''; ?> />
			<input type="number" class="pin-prompt" id="pin-prompt-3" step="1" pattern="[0-9]*" <?php echo $remaining == 0 ? 'disabled' : ''; ?> />
			<input type="number" class="pin-prompt" id="pin-prompt-4" step="1" pattern="[0-9]*" <?php echo $remaining == 0 ? 'disabled' : ''; ?> />
		</div>

		<?php if ($error): ?>
			<div class="pin-prompt-line error">
				<b><?php echo JText::translate('VRE_QR_RES_PIN_WRONG_PIN'); ?></b>
			</div>	
		<?php endif; ?>

		<?php if ($reservation->pinattempts > 0): ?>
			<div class="pin-prompt-line attempts">
				<?php echo JText::plural('VRE_QR_RES_PIN_REMAINING_N_ATTEMPTS', $remaining); ?>
			</div>	
		<?php endif; ?>

		<div class="pin-prompt-line submit">
			<button type="submit" class="vre-btn primary" disabled><?php echo JText::translate('VRSUBMIT'); ?></button>
		</div>

		<div class="pin-prompt-line help">
			<small><?php echo JText::translate('VRE_QR_RES_PIN_FIND_HELP'); ?></small>
		</div>
	</div>
	
	<input type="hidden" name="table" value="<?php echo $this->escape($table); ?>" />
	<input type="hidden" name="pin" value="" />

	<?php echo JHtml::fetch('form.token'); ?>
</form>

<script>
	(function($) {
		'use strict';

		const checkPinCode = () => {
			let pin = [];

			$('.pin-prompt-wrapper input.pin-prompt').each(function() {
				let val = $(this).val();

				if (val.length == 0) {
					return;
				}

				val = parseInt(val);

				if (!isNaN(val)) {
					pin.push(val);
				}
			});

			if (pin.length != 4) {
				$('.pin-prompt-wrapper .submit button').prop('disabled', true);
				$('.pin-prompt-wrapper input[name="pin"]').val('');
				return false;
			}

			$('.pin-prompt-wrapper .submit button').prop('disabled', false);
			$('#pin-prompt-form input[name="pin"]').val(pin.join(''));
			return true;
		}

		$(function() {
			$('#pin-prompt-1').focus();

			$('.pin-prompt-wrapper input.pin-prompt').on('keydown', function(event) {
				// look for backspace (8) or enter (13)
				if (event.key == 'Backspace') {
					// in case the input has no text, delete the value of the previous input
					if ($(this).val().length == 0) {
						$(this).prev().focus();
					} else {
						// clear the whole value
						$(this).val('');
					}
				} else if (event.key == 'Enter') {
					if (checkPinCode()) {
						$('#pin-prompt-form').submit();
					}
				} else if ($(this).val().length || isNaN(parseInt(event.key))) {
					// we don't have a digit, reject the input
					event.preventDefault();
					event.stopPropagation();
					return false;
				}
			});

			$('.pin-prompt-wrapper input.pin-prompt').on('keyup', function(event) {
				// always re-validate the pin code integrity when releasing a key
				checkPinCode();

				if (!isNaN(parseInt(event.key))) {
					// digit typed, find the next empty inpuy
					let siblings = $(this).nextAll().filter(function() {
						return $(this).val().length == 0;
					});

					// focus the next element
					$(siblings).first().focus();
				}
			});
		});
	})(jQuery);
</script>