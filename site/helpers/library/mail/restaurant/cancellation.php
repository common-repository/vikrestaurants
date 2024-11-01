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
 * Wrapper used to handle mail notifications to be sent
 * to the administrators every time a customer makes
 * a reservation cancellation.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\Mail\Templates\Restaurant\CancellationMailTemplate instead.
 */
class VREMailTemplateRestaurantCancellation implements VREMailTemplate
{
	use VREMailTemplateadapter;

	/** @var E4J\VikRestaurants\Mail\Templates\Restaurant\CancellationMailTemplate */
	protected $adaptee;

	/**
	 * Class constructor.
	 *
	 * @param  mixed   $res      Either the reservation ID or the reservation object.
	 * @param  string  $langtag  An optional language tag.
	 * @param  array   $options  A configuration array.
	 */
	public function __construct($res, $langtag = null, array $options = [])
	{
		$options['lang'] = $langtag;

		$this->adaptee = new E4J\VikRestaurants\Mail\Templates\Restaurant\CancellationMailTemplate($res, $options);
	}
}
