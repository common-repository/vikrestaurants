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

$controller = isset($displayData['controller']) ? $displayData['controller'] : 'confirmres';
$args       = isset($displayData['args'])       ? $displayData['args']       : [];
$itemid     = isset($displayData['itemid'])     ? $displayData['itemid']     : null;
$form       = isset($displayData['form'])       ? $displayData['form']       : null;

// build action URL
$url = JRoute::rewrite('index.php?option=com_vikrestaurants&task=' . $controller . '.redeemcoupon' . ($itemid ? '&Itemid=' . $itemid : ''));

// open form only if not specified
if (!$form)
{
	?>
	<form action="<?php echo $url; ?>" name="vrcouponform" method="post">
	<?php
}
?>

<div class="vrcouponcodediv">

	<h3 class="vrheading3"><?php echo JText::translate('VRENTERYOURCOUPON'); ?></h3>

	<input type="text" name="couponkey" class="vrcouponcodetext" />

	<button type="submit" class="vre-btn primary small" onclick="return onBeforeSubmitCouponCode();"><?php echo JText::translate('VRAPPLYCOUPON'); ?></button>

</div>
	
<?php
// preserve search arguments, if any
foreach ($args as $k => $v)
{
	?>
	<input type="hidden" name="<?php echo $this->escape($k); ?>" value="<?php echo $this->escape($v); ?>" />
	<?php
}

// close form only if not specified
if (!$form)
{
	// use token to prevent brute force attacks
	echo JHtml::fetch('form.token');
	?>
		<input type="hidden" name="option" value="com_vikrestaurants" />
		<input type="hidden" name="task" value="<?php echo $controller; ?>.redeemcoupon" />
	</form>
	<?php
}
?>

<script>
	(function($, w) {
		'use strict';

		w.onBeforeSubmitCouponCode = () => {
			<?php if ($form): ?>
				const form = $('form[name="<?php echo $form; ?>"]');

				// check if we have a task field within our form
				let taskField = form.find('input[name="task"]');

				if (!taskField.length) {
					// nope, create it and append it at the end of the form
					taskField = $('<input type="hidden" name="task" value="" />');
					form.append(taskField);
				}

				// manually update task
				taskField.val('<?php echo $controller; ?>.redeemcoupon');
			<?php endif; ?>

			return true;
		}
	})(jQuery, window);
</script>