<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Decorators;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplate;
use E4J\VikRestaurants\Mail\MailTemplateDecorator;

/**
 * Adds support to the following template data (tags):
 *
 * {review_product_menu}   The title of the menu to which the item belongs.
 * {review_product_name}   The name of the reviewed item.
 * {review_product_desc}   The description of the reviewed item.
 * {review_product_image}  The image URI (if any) of the reviewed item.
 *
 * @since 1.9
 */
final class ProductReviewMailDecorator implements MailTemplateDecorator
{
	/** @var \stdClass */
	private $review;

	/**
	 * Class constructor.
	 * 
	 * @param  object  $review  The review details.
	 */
	public function __construct($review)
	{
		$this->review = $review;
	}

	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		// register  template data
		$template->addTemplateData([
			'review_product_menu'  => $this->review->menuTitle,
			'review_product_name'  => $this->review->productName,
			'review_product_desc'  => $this->review->productDescription,
			'review_product_image' => $this->review->productImage ? VREMEDIA_SMALL_URI . $this->review->productImage : '',
		]);
	}
}
