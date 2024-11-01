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
 * Template file used to display a summary of
 * the selected check-in details. 
 *
 * @since 1.8
 */

$data = [
	/**
	 * The check-in formatted date.
	 *
	 * @var string
	 */
	'date' => date(VREFactory::getConfig()->get('dateformat'), $this->checkinTime->ts),

	/**
	 * The check-in formatted time.
	 *
	 * @var string
	 */
	'time' => $this->checkinTime->format,

	/**
	 * The selected number of participants.
	 *
	 * @var int
	 */
	'people' => $this->args['people'],

	/**
	 * An optional class suffix.
	 *
	 * @var string
	 */
	'suffix' => ' search',
];

/**
 * The step bar is displayed from the layout below:
 * /components/com_vikrestaurants/layouts/blocks/summary.php (joomla)
 * /wp-content/plugins/vikrestaurants/site/layouts/blocks/summary.php (wordpress)
 *
 * @since 1.8
 */
echo JLayoutHelper::render('blocks.summary', $data);
