<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

$view = $displayData['view'];

$lookup = array(
	'acl' => array(
		'title' => JText::translate('VREACLMENUTITLE'),
		'desc'  => __('Define the permissions for each user role to allow or deny the access to certain pages and the actions they can perform.', 'vikrestaurants'),
	),

	'customers' => array(
		'title' => JText::translate('VRMAINTITLEVIEWCUSTOMERS'),
		'desc'  => __('Here you can manage all of your customers information, their delivery locations and send specific SMS notifications.', 'vikrestaurants'),
	),

	'invoices' => array(
		'title' => JText::translate('VRMAINTITLEVIEWINVOICES'),
		'desc'  => __('Manage all the invoices generated for the various restaurant bills or take-away orders.', 'vikrestaurants'),
	),

	'managemap' => array(
		'title' => JText::translate('VRMAINTITLEEDITMAP'),
		'desc'  => __('Design here the layout of your rooms and create/manage the tables with a smart and intuitive tool.', 'vikrestaurants'),
	),

	'operators' => array(
		'title' => JText::translate('VRMAINTITLEVIEWOPERATORS'),
		'desc'  => __('Let your operators manage the reservations and orders from a smart private area in the front-end.', 'vikrestaurants'),
	),

	'reviews' => array(
		'title' => JText::translate('VRMAINTITLEVIEWREVIEWS'),
		'desc'  => __('Start collecting your own customers reviews for your food and service.', 'vikrestaurants'),
	),

	'tkdeals' => array(
		'title' => JText::translate('VRMAINTITLEVIEWTKDEALS'),
		'desc'  => __('Set up custom offers to highlight particular dates and to target different guests.', 'vikrestaurants'),
	),
);

if (!isset($lookup[$view]))
{
	return;
}

// set up toolbar title
JToolbarHelper::title($lookup[$view]['title']);

if (empty($lookup[$view]['image']))
{
	// use the default logo image
	$lookup[$view]['image'] = 'vikwp-lite-logo.png';
}

?>

<div class="vre-free-nonavail-wrap">

	<div class="vre-free-nonavail-inner">

		<div class="vre-free-nonavail-logo">
			<img src="<?php echo VIKRESTAURANTS_CORE_MEDIA_URI . 'images/' . $lookup[$view]['image']; ?>">
		</div>

		<div class="vre-free-nonavail-expl">
			<h3><?php echo preg_replace("/VikRestaurants - /i", '', $lookup[$view]['title']); ?></h3>

			<p class="vre-free-nonavail-descr"><?php echo $lookup[$view]['desc']; ?></p>

			<p class="vre-free-nonavail-footer-descr">
				<a href="admin.php?page=vikrestaurants&amp;view=gotopro" class="btn vre-free-nonavail-gopro">
					<i class="fas fa-rocket"></i>
					<span><?php echo JText::translate('VREGOTOPROBTN'); ?></span>
				</a>
			</p>
		</div>

	</div>

</div>
