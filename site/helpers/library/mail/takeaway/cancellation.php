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
 * an order cancellation.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\Mail\Templates\Takeaway\CancellationMailTemplate instead.
 */
class VREMailTemplateTakeawayCancellation implements VREMailTemplate
{
	use VREMailTemplateadapter;

	/** @var E4J\VikRestaurants\Mail\Templates\Takeaway\CancellationMailTemplate */
	protected $adaptee;

	/**
	 * Class constructor.
	 *
	 * @param  mixed   $order    Either the order ID or the order object.
	 * @param  string  $langtag  An optional language tag.
	 * @param  array   $options  A configuration array.
	 */
	public function __construct($order, $langtag = null, array $options = [])
	{
		$options['lang'] = $langtag;
		
		$this->adaptee = new E4J\VikRestaurants\Mail\Templates\Takeaway\CancellationMailTemplate($order, $options);
	}
}
