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
 * Wrapper used to handle mail notifications for the administrators
 * when someone books a restaurant reservation.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\Mail\Templates\Restaurant\AdminMailTemplate instead.
 */
class VREMailTemplateRestaurantAdmin implements VREMailTemplate
{
	use VREMailTemplateadapter;

	/** @var E4J\VikRestaurants\Mail\Templates\Restaurant\AdminMailTemplate */
	protected $adaptee;

	/**
	 * Class constructor.
	 *
	 * @param  mixed   $res      Either the reservation ID or the reservation object.
	 * @param  string  $langtag  An optional language tag.
	 */
	public function __construct($res, $langtag = null)
	{
		$this->adaptee = new E4J\VikRestaurants\Mail\Templates\Restaurant\AdminMailTemplate($res, [
			'lang' => $langtag,
		]);
	}
}
