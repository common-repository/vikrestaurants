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

JHtml::fetch('behavior.core');
JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.fontawesome');

$filters = $this->filters;

$vik = VREApplication::getInstance();

?>

<div id="orderhistory-modal" style="padding: 10px;">

	<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">
	
		<?php echo $vik->bootStartTabSet('orderhistory', array('active' => 'orderhistory_operator')); ?>

			<!-- OPERATOR -->
				
			<?php
			echo $vik->bootAddTab('orderhistory', 'orderhistory_operator', JText::translate('VRMANAGEOPLOG1'));
			echo $this->loadTemplate('operator');
			echo $vik->bootEndTab();
			?>

			<!-- PAYMENT LOGS -->

			<?php
			if ($this->payLog)
			{
				echo $vik->bootAddTab('orderhistory', 'orderhistory_payment', JText::translate('VRMANAGERESERVATION20'));
				echo $this->loadTemplate('payment');
				echo $vik->bootEndTab();
			}
			?>

		<?php echo $vik->bootEndTabSet(); ?>

	
		<input type="hidden" name="id" value="<?php echo (int) $filters['id']; ?>" />
		<input type="hidden" name="group" value="<?php echo (int) $filters['group']; ?>" />
		
		<input type="hidden" name="view" value="orderhistory" />

		<?php echo JHtml::fetch('form.token'); ?>
		<?php echo $this->navbut; ?>
	</form>

</div>
