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
 * when someone leaves a review for a take-away product.
 *
 * @since 1.8
 * @deprecated 1.10  Use E4J\VikRestaurants\Mail\Templates\Takeaway\ReviewMailTemplate instead.
 */
class VREMailTemplateTakeawayReview implements VREMailTemplate
{
	use VREMailTemplateadapter;

	/** @var E4J\VikRestaurants\Mail\Templates\Takeaway\ReviewMailTemplate */
	protected $adaptee;

	/**
	 * Class constructor.
	 *
	 * @param  int     $reviewId  The review ID.
	 * @param  string  $langtag   An optional language tag.
	 */
	public function __construct($reviewId, $langtag = null)
	{
		$this->adaptee = new E4J\VikRestaurants\Mail\Templates\Takeaway\ReviewMailTemplate($reviewId, [
			'lang' => $langtag,
		]);
	}
}
