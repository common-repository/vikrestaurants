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

// create log details layout
$layout = new JLayoutFile('blocks.operatorlog');

foreach ($this->payLog as $row)
{
	// set empty data attribute to avoid errors
	$row['content']        = '[]';
	$row['group']          = $this->filters['group'];
	$row['id_reservation'] = $this->filters['id'];
	
	// set current log for being used in sub-layout
	$data = [
		'log'         => $row,
		'operator'    => false, // hide operator badge
		'reservation' => false, // hide reservation badge
	];

	echo $layout->render($data);
}
