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

$payments   = isset($displayData['payments'])   ? $displayData['payments']   : [];
$showdesc   = isset($displayData['showdesc'])   ? $displayData['showdesc']   : true;
$id_payment = isset($displayData['id_payment']) ? $displayData['id_payment'] : null;

$payCount = count($payments);

?>

<h3 class="vre-confirm-h3"><?php echo JText::translate('VRMETHODOFPAYMENT'); ?></h3>

<div class="vr-payments-container">
	<?php
	foreach ($payments as $i => $p)
	{	
		$cost_str = '';

		/**
		 * It is no more possible to offer discounts depending on the selected payment method.
		 * In order to preserve this functionality, it is preferred to create apposite deals
		 * that triggers according to the payment ID set in request.
		 * 
		 * The back-end still provides the possibility of offering discounts through the payments.
		 * 
		 * @since 1.9
		 */
		if ($p->charge > 0)
		{
			if ($p->percentot == 1)
			{
				// percentage fee
				$cost_str = VREFactory::getCurrency()->format($p->charge, [
					'symbol'     => '%',
					'position'   => 1,
					'space'      => false,
					'no_decimal' => true,
				]);
			}
			else
			{
				// fixed fee
				$cost_str = VREFactory::getCurrency()->format($p->charge);
			}
		}

		if ($id_payment)
		{
			// select the specified payment gateway only
			$selected = $p->id == $id_payment;
		}
		else
		{
			// auto-select the first one available
			$selected = $i == 0;
		}
		?>

		<div class="vr-payment-wrapper vr-payment-block">

			<div class="vr-payment-title">

				<?php if ($payCount > 1): ?>
					<input
						type="radio"
						name="id_payment"
						value="<?php echo (int) $p->id; ?>"
						id="vrpayradio<?php echo (int) $p->id; ?>"
						<?php echo $selected ? 'checked="checked"' : '' ?>
					/>
				<?php else: ?>
					<input type="hidden" name="id_payment" value="<?php echo (int) $p->id; ?>" />
				<?php endif; ?>

				<label for="vrpayradio<?php echo (int) $p->id; ?>" class="vr-payment-title-label">

					<?php if ($p->icontype == 1): ?>
						<i class="<?php echo $p->icon; ?>"></i>&nbsp;
					<?php elseif ($p->icontype == 2): ?>
						<img src="<?php echo JUri::root() . $p->icon; ?>" alt="<?php echo $this->escape($p->name); ?>" />&nbsp;
					<?php endif; ?>

					<span><?php echo $p->name . (strlen($cost_str) ? ' (' . $cost_str . ')' : ''); ?></span>

				</label>

			</div>

			<?php if (strlen($p->prenote) && $showdesc): ?>
				<div class="vr-payment-description" id="vr-payment-description<?php echo $p->id; ?>" style="<?php echo (!$selected ? 'display: none;' : ''); ?>">
					<?php
					// assign notes to temporary variable
					$content = $p->prenote;

					/**
					 * Render HTML description to interpret attached plugins.
					 * 
					 * @since 1.8
					 */
					VREApplication::getInstance()->onContentPrepare($content, $full = false);

					echo $content->text;
					?>
				</div>
			<?php endif; ?>

		</div>

		<?php
	}
	?>
</div>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('.vr-payment-wrapper input[name="id_payment"]').on('change', function() {
				$('.vr-payment-title-label').removeClass('vrrequired');

				// get input parent
				const block = $(this).closest('.vr-payment-block');
				// get description block
				const desc = $(block).find('.vr-payment-description');
				// check if a description was visible
				let was = $('.vr-payment-description:visible').length > 0;

				if (desc.length == 0) {
					// hide previous description with animation
					// only if the selected payment doesn't
					// have a description to display
					$('.vr-payment-description').slideUp();
				} else {
					// otherwise hide as quick as possible
					$('.vr-payment-description').hide();
				}

				if (was) {
					// in case a description was already visible,
					// show new description without animation
					desc.show();
				} else {
					// animate in case there was no active payment
					desc.slideDown();
				}
			});
		});
	})(jQuery);
</script>