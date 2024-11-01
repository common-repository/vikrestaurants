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

$card  = $this->creditCard;
$order = $this->order;

$vik = VREApplication::getInstance();

$delete_href = sprintf(
	'index.php?option=com_vikrestaurants&view=ccdetails&tmpl=component&id=%d&group=%d&rmhash=%s',
	$this->id,
	$this->group,
	$this->rmHash
);

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

<div style="padding: 10px;">

	<div class="btn-toolbar" style="height:32px;">

		<div class="btn-group pull-left vr-toolbar-setfont">
			<strong>
				<?php
				echo JText::sprintf(
					'VRCREDITCARDAUTODELMSG',
					JHtml::fetch('date', $this->expDate, JText::translate('DATE_FORMAT_LC3') . ' ' . VREFactory::getConfig()->get('timeformat'), date_default_timezone_get())
				);
				?>
			</strong>
		</div>

		<div class="btn-group pull-right">
			<a href="<?php echo VREFactory::getPlatform()->getUri()->addCSRF($delete_href, true); ?>" class="btn btn-danger" onclick="return confirmCreditCardDelete(event);">
				<?php echo JText::translate('VRDELETE'); ?>
			</a>
		</div>

	</div>

	<?php echo $vik->openCard(); ?>

		<div class="span6">
			<?php echo $vik->openEmptyFieldset(); ?>

				<?php
				foreach ($card as $k => $v)
				{
					$field = $this->formFactory->createField()
						->type('text')
						->id('cc_field_' . strtolower($k))
						->value($v->value)
						->label($v->label)
						->readonly(true);

					if ($k == 'cardNumber')
					{
						echo $field->render(function($data, $input) use ($card, $ccBrandUrl) {
							?>
							<div style="position: relative; width: fit-content;">
								<?php echo $input; ?>
								<img
									src="<?php echo $ccBrandUrl . 'payments/off-cc/resources/icons/' . $card->brand->alias . '.png'; ?>"
									title="<?php echo $this->escape($card->brand->value); ?>"
									alt="<?php echo $this->escape($card->brand->value); ?>"
									style="position: absolute; top: 50%; right: 6px; transform: translateY(-50%);"
								/>
							</div>
							<?php
						});
					}
					else
					{
						echo $field;
					}
				}
				?>

			<?php echo $vik->closeEmptyFieldset(); ?>
		</div>

	<?php echo $vik->closeCard(); ?>

</div>

<?php
JText::script('VRSYSTEMCONFIRMATIONMSG');
?>

<script>
	(function($, w) {
		'use strict';

		w.confirmCreditCardDelete = (event) => {
			// turn off any previously attached events
			$(event.target).off('click');
				
			// make sure the user confirmed the prompt
			if (confirm(Joomla.JText._('VRSYSTEMCONFIRMATIONMSG')))
			{
				<?php if (VersionListener::getPlatform() == 'joomla'): ?>
					// just return TRUE on Joomla to hit the URL HREF
					return true;
				<?php else: ?>
					// stop propagating the event
					event.stopPropagation();
					event.preventDefault();

					// get ID of the current modal
					let id = $('div.modal.fade.in').first().find('.modal-body-wrapper').attr('id');
					// retrieve contents via AJAX by reaching the link HREF
					wpAppendModalContent(id, $(event.target).attr('href'));
					// go ahead to always return false in WordPress
				<?php endif; ?>
			}

			// return false, the customer didn't confirm the prompt
			return false;
		}
	})(jQuery, window);
</script>