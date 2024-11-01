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

$display = isset($displayData['display']) ? (bool) $displayData['display'] : true;
$active  = !empty($displayData['active']) ? $displayData['active']         : 1;
$args    = !empty($displayData['args'])   ? (array) $displayData['args']   : [];
$itemid  = !empty($displayData['Itemid']) ? $displayData['Itemid']         : null;

if (!$display)
{
	// do not proceed in case the step bar shouldn't be displayed
	return;
}

if (is_null($itemid))
{
	$itemid = JFactory::getApplication()->input->get('Itemid', 0, 'uint');
}

$queryString = '';

if ($args)
{
	$queryString .= '&' . http_build_query($args);
}

if ($itemid)
{
	$queryString .= '&Itemid=' . $itemid;
}

$resreq = VREFactory::getConfig()->getUint('reservationreq');

?>

<!-- step one -->

<div class="vrstepbardiv">

	<!-- STEP ONE -->

	<?php
	if ($active == 1)
	{
		// first step active
		?>
		<div class="vrstep vrstepactive step-current">
			<div class="vrstep-inner">
				<span class="vrsteptitle"><?php echo JText::translate('VRSTEPONETITLE'); ?></span>
				<span class="vrstepsubtitle"><?php echo JText::translate('VRSTEPONESUBTITLE'); ?></span>
			</div>
		</div>
		<?php
	}
	else
	{
		// first step already completed
		?>
		<div class="vrstep vrstepactive">
			<div class="vrstep-inner">
				<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=restaurants' . $queryString); ?>">
					<span class="vrsteptitle"><i class="fas fa-check"></i></span>
					<span class="vrstepsubtitle"><?php echo JText::translate('VRSTEPONESUBTITLE'); ?></span>
				</a>
			</div>
		</div>
		<?php
	}
	?>

	<!-- STEP TWO -->

	<?php
	if ($resreq == 0)
	{
		$step_2_title = JText::translate('VRSTEPTWOSUBTITLEZERO');
	}
	else if ($resreq == 1)
	{
		$step_2_title = JText::translate('VRSTEPTWOSUBTITLEONE');
	}
	else
	{
		$step_2_title = JText::translate('VRSTEPTWOSUBTITLETWO');
	}
	
	if ($active == 2)
	{
		// second step active
		?>
		<div class="vrstep vrstepactive step-current">
			<div class="vrstep-inner">
				<span class="vrsteptitle"><?php echo JText::translate('VRSTEPTWOTITLE'); ?></span>
				<span class="vrstepsubtitle"><?php echo $step_2_title; ?></span>
			</div>
		</div>
		<?php
	}
	else if ($active > 2)
	{
		// second step already completed
		?>
		<div class="vrstep vrstepactive">
			<div class="vrstep-inner">
				<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=search&back=1' . $queryString); ?>">
					<span class="vrsteptitle"><i class="fas fa-check"></i></span>
					<span class="vrstepsubtitle"><?php echo $step_2_title; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
	else
	{
		// second step not yet reached
		?>
		<div class="vrstep">
			<div class="vrstep-inner">
				<span class="vrsteptitle"><?php echo JText::translate('VRSTEPTWOTITLE'); ?></span>
				<span class="vrstepsubtitle"><?php echo $step_2_title; ?></span>
			</div>
		</div>
		<?php
	}
	?>

	<!-- STEP THREE -->

	<?php
	if ($active == 3)
	{
		// third step active
		?>
		<div class="vrstep vrstepactive step-current">
			<div class="vrstep-inner">
				<span class="vrsteptitle"><?php echo JText::translate('VRSTEPTHREETITLE'); ?></span>
				<span class="vrstepsubtitle"><?php echo JText::translate('VRSTEPTHREESUBTITLE'); ?></span>
			</div>
		</div>
		<?php
	}
	else
	{
		// third step not yet reached
		?>
		<div class="vrstep">
			<div class="vrstep-inner">
				<span class="vrsteptitle"><?php echo JText::translate('VRSTEPTHREETITLE'); ?></span>
				<span class="vrstepsubtitle"><?php echo JText::translate('VRSTEPTHREESUBTITLE'); ?></span>
			</div>
		</div>
		<?php
	}
	?>

</div>