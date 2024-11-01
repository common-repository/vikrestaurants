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

$itemid = JFactory::getApplication()->input->get('Itemid', null, 'uint');

?>

<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=order' . ($itemid ? '&Itemid=' . $itemid : '')); ?>" name="orderform" method="get">

	<div class="vrorderpagediv">

		<div class="vrordertitlediv"><?php echo JText::translate('VRORDERTITLE1'); ?></div>

		<div class="vrordercomponentsdiv">

			<div class="vrorderinputdiv">
				<label class="vrorderlabel" for="vrordnum" style="float: left;">
					<?php echo JText::translate('VRORDERNUMBER'); ?>:
				</label>
				
				<input class="vrorderinput" type="text" id="vrordnum" name="ordnum" size="16" />
			</div>
			
			<div class="vrorderinputdiv">
				<label class="vrorderlabel" for="vrordkey" style="float: left;">
					<?php echo JText::translate('VRORDERKEY'); ?>:
				</label>

				<input class="vrorderinput" type="text" id="vrordkey" name="ordkey" size="16" />
			</div>
			
			<div class="vrorderinputdiv">
				<button type="submit" class="vrordersubmit"><?php echo JText::translate('VRSUBMIT'); ?></button>
			</div>

		</div>

	</div>

	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="view" value="order" />

</form>
