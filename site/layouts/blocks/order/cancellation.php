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
 * @var  VREOrderWrapper  $order   The reservation/order we want to cancel.
 * @var  string           $task    The task to reach to request the cancellation.
 * @var  int              $reason  Whether the cancellation reason is enabled or not
 *                                 (0: disabled, 1: optional, 2: mandatory).
 * @var  int              $itemid  An optional Item ID to use for URL rewriting.
 */
$order   = isset($displayData['order'])  ? $displayData['order']         : null;
$task    = isset($displayData['task'])   ? (string) $displayData['task'] : '';
$reason  = isset($displayData['reason']) ? (int) $displayData['reason']  : 0;
$itemid  = isset($displayData['itemid']) ? (int) $displayData['itemid']  : 0;

if (!$order instanceof VREOrderWrapper)
{
    $type = is_object($order) ? get_class($order) : gettype($order);
    throw new InvalidArgumentException('Invalid order instance: VREOrderWrapper expected, ' . $type . ' given', 400);
}

// auto-load CSS file to display the confirmation dialog
VREApplication::getInstance()->addStyleSheet(VREASSETS_URI . 'css/confirmdialog.css');
?>

<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&task=' . $task . ($itemid ? '&Itemid=' . $itemid : '')); ?>" method="post" name="vrcancform" id="vrcancform">

    <div class="vrordercancdiv vrcancallbox">
        <button type="button" class="vre-btn danger" onClick="vrCancelButtonPressed();">
            <?php echo JText::translate('VRCANCELORDERTITLE'); ?>
        </button>
    </div>

    <input type="hidden" name="oid" value="<?php echo $this->escape($order->id); ?>" />
    <input type="hidden" name="sid" value="<?php echo $this->escape($order->sid); ?>" />
    <input type="hidden" name="reason" value="" />

    <input type="hidden" name="option" value="com_vikrestaurants" />
    <input type="hidden" name="task" value="<?php echo $this->escape($task); ?>" />

</form>

<div id="dialog-cancel" style="display: none;">
    
    <h4><?php echo JText::translate('VRCANCELORDERTITLE');?></h4>

    <p><?php echo JText::translate('VRCANCELORDERMESSAGE'); ?></p>

    <?php if ($reason > 0): ?>
        <div>
            <div class="vr-cancreason-err" style="display: none;">
                <?php echo JText::translate('VRCANCREASONERR'); ?>
            </div>

            <textarea
                id="vrcancreason"
                placeholder="<?php echo $this->escape(JText::translate('VRCANCREASONPLACEHOLDER' . $reason)); ?>"
                style="width: 100%; height: 120px; max-height: 50vh; resize: vertical;"
            ></textarea>
        </div>
    <?php endif; ?>
    
</div>

<?php
JText::script('VRCANCELORDEROK');
JText::script('VRCANCELORDERCANC');
?>

<script>
    (function($) {
        'use strict';

        const CANC_REASON = <?php echo (int) $reason; ?>;

        // create cancellation dialog
        let cancDialog;

        window.vrCancelButtonPressed = () => {
            const args = {
                textarea: CANC_REASON > 0 ? $('#vrcancreason') : null,
            };

            // Show dialog by passing some arguments.
            // Prevent submit when ENTER is pressed.
            cancDialog.show(args, {submit: false});
        }

        $(function() {
            cancDialog = new VikConfirmDialog('#dialog-cancel');

            // add confirm button
            cancDialog.addButton(Joomla.JText._('VRCANCELORDEROK'), (args, event) => {
                if (CANC_REASON) {
                    // get specified reason
                    let reason = $(args.textarea).val();

                    if ((reason.length > 0 && reason.length < 32)
                        || (reason.length == 0 && CANC_REASON == 2)) {
                        $('#vrcancreason').addClass('vrrequiredfield');
                        $('.vr-cancreason-err').show();
                        return false;
                    }

                    $('#vrcancreason').removeClass('vrrequiredfield');
                    $('.vr-cancreason-err').hide();

                    $('#vrcancform input[name="reason"]').val(reason);
                }

                // dispose dialog
                cancDialog.dispose();

                // submit form to complete cancellation
                document.vrcancform.submit();
            }, false);

            // add cancel button
            cancDialog.addButton(Joomla.JText._('VRCANCELORDERCANC'));

            // pre-build dialog
            cancDialog.build();

            if (window.location.hash === '#cancel') {
                vrCancelButtonPressed();
            }
        });
    })(jQuery);
</script>