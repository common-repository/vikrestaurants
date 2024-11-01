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
 * - {review_content}     A default into-text.
 * - {review_title}       The review title submitted by the user.
 * - {review_comment}     An optional review comment submitted by the user.
 * - {review_rating}      The rating (as image) submitted by the user.
 * - {review_verified}    A badge to check whether the review was submitted by a verified user.
 * - {confirmation_link}  A quick link to approve the review.
 * - {user_name}          The name of the CMS user.
 * - {user_email}         The e-mail of the CMS user.
 * - {user_username}      The login username of the CMS user.
 *
 * @since 1.9
 */
final class ReviewMailDecorator implements MailTemplateDecorator
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
		// fetch review rating
		$star    = '<img src="' . VREASSETS_URI . 'css/images/rating-star.png" alt="" />';
		$no_star = '<img src="' . VREASSETS_URI . 'css/images/rating-star-no.png" alt="" />';

		// Repeat star image for the value of the rating.
		// Then display empty stars for the missed rating.
		$reviewRating = str_repeat($star, $this->review->rating)
			. str_repeat($no_star, 5 - $this->review->rating);

		// get user ID
		if ($this->review->jid > 0)
		{
			// get user details
			$user = \JFactory::getUser($this->review->jid);
		}
		else
		{
			$user = null;
		}

		/** @var E4J\VikRestaurants\Platform\Uri\UriInterface */
		$uri = \VREFactory::getPlatform()->getUri();

		// fetch confirmation link HREF
		$confirmationLinkHREF = $uri->route("index.php?option=com_vikrestaurants&task=approve_review&id={$this->review->id}&conf_key={$this->review->conf_key}");

		// register  template data
		$template->addTemplateData([
			'review_content'        => \JText::sprintf('VRREVIEWCONTENT', $this->review->email, $this->review->name),
			'review_title'          => $this->review->title,
			'review_comment'        => $this->review->comment,
			'review_rating'         => $reviewRating,
			'review_verified'       => $this->review->verified ? \JText::translate('VRREVIEWVERIFIED') : '',
			'confirmation_link'     => $confirmationLinkHREF,
			'user_name'             => $user ? $user->name : '',
			'user_username'         => $user ? $user->username : '',
			'user_email'            => $user ? $user->email : '',
		]);
	}
}
