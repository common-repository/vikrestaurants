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

<!-- PIN CODE - Custom -->

<?php
$failedAttempts = (int) $this->reservation->pinattempts;

// pin code locked
$pinCodeLockedError = $this->formFactory->createField()
	->type('alert')
	->style('error')
	->text(JText::translate('VRPINCODE_RESERVATION_LOCKED'))
	->hiddenLabel(true);

// pin code unlock button
$pinCodeUnlockButton = $this->formFactory->createField()
	->type('button')
	->text(JText::translate('VRPINCODE_RESERVATION_UNLOCK'))
	->hiddenLabel(true)
	->onclick('unlockPinCode(this)');

echo $this->formFactory->createField()
	->value($this->reservation->pin)
	->hiddenLabel(true)
	->description(JText::translate('VRPINCODE_RESERVATION_DESC'))
	->render(function($data) use ($failedAttempts, $pinCodeLockedError, $pinCodeUnlockButton) {
		?>
		<div class="reservation-pincode-wall" style="display: <?php echo $failedAttempts < 3 ? 'block' : 'none'; ?>;padding-bottom: 20px; text-align: center; font-size: 48px; font-weight: bold; color: #476799;">
			<?php echo $data->get('value'); ?>
		</div>
		<?php if ($failedAttempts >= 3): ?>
			<div class="reservation-pincode-locked">
				<?php
				// display error message
				echo $pinCodeLockedError;
				// display button to unlock the pin code
				echo $pinCodeUnlockButton;
				?>
			</div>
		<?php endif; ?>
		<?php
	});
?>

<script>
	(function($, w) {
		'use strict';

		w.unlockPinCode = (button) => {
			$(button).prop('disabled', true);

			new Promise((resolve, reject) => {
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=reservation.resetpinajax'); ?>',
					{
						id: <?php echo $this->reservation->id; ?>,
					},
					(pin) => {
						resolve(pin)
					},
					(error) => {
						reject(error.responseText || Joomla.JText._('VRE_AJAX_GENERIC_ERROR'));
					}
				);
			}).then((pin) => {
				// hide error message
				$('.reservation-pincode-locked').hide();
				// update the pin code and show it again
				$('.reservation-pincode-wall').text(pin).show();
			}).catch((error) => {
				$(button).prop('disabled', false);

				setTimeout(() => {
					alert(error);
				}, 128);
			});
		}
	})(jQuery, window);
</script>