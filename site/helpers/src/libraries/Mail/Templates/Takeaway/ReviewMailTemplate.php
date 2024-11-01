<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Templates\Takeaway;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplateAware;

/**
 * Wrapper used to handle mail notifications for the administrators
 * when someone leaves a review for a take-away product.
 *
 * @since 1.9
 */
class ReviewMailTemplate extends MailTemplateAware
{
	/**
	 * The review object.
	 *
	 * @var \stdClass
	 */
	protected $review;

	/**
	 * An array of options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * An optional template file to use.
	 *
	 * @var string
	 */
	protected $templateFile;

	/**
	 * Class constructor.
	 *
	 * @param  int     $reviewId  The review ID.
	 * @param  array   $options   An array of options.
	 */
	public function __construct($reviewId, array $options = [])
	{
		if (empty($options['lang']))
		{
			// language not provided, use the current one
			$options['lang'] = \JFactory::getLanguage()->getTag();
		}

		// register options
		$this->options = $options;

		// load given language to translate template contents
		\VikRestaurants::loadLanguage($this->options['lang']);

		// load review helper
		\VRELoader::import('library.reviews.handler');

		// obtain the product review
		$this->review = (new \ReviewsHandler)->takeaway()->getReview((int) $reviewId);

		// use global sender
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\SenderMailDecorator);

		// set all the administrators as recipient
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RecipientAdministratorsMailDecorator);

		// inject generic company information, such as the restaurant name and the image logo
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CompanyMailDecorator);

		// inject generic review information
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\ReviewMailDecorator($this->review, $this->options['lang']));

		// inject product review information
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\ProductReviewMailDecorator($this->review, $this->options['lang']));
	}

	/**
	 * @inheritDoc
	 */
	public function setFile($file)
	{
		// check if a filename or a path was passed
		if ($file && !\JFile::exists($file))
		{
			// make sure we have a valid file path
			$file = VREHELPERS . '/tk_mail_tmpls/' . $file;
		}

		$this->templateFile = \JPath::clean($file);
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplate()
	{
		// copy review details in a local
		// variable for being used directly
		// within the template file
		$review = $this->review;

		if ($this->templateFile)
		{
			// use specified template file
			$file = $this->templateFile;
		}
		else
		{
			// get template file from configuration
			$file = \VREFactory::getConfig()->get('tkreviewmailtmpl');

			// build template path
			$file = \JPath::clean(VREHELPERS . '/tk_mail_tmpls/' . $file);
		}

		// make sure the file exists
		if (!is_file($file))
		{
			// missing file, return empty string
			return '';
		}

		// start output buffering 
		ob_start();
		// include file to catch its contents
		include $file;
		// write template contents within a variable
		$content = ob_get_contents();
		// clear output buffer
		ob_end_clean();

		// free space
		unset($review);

		return $content;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldSend()
	{
		// always send notification when a review is left
		return true;
	}

	/**
	 * @inheritDoc
	 */
	final protected function createMail(array &$args)
	{
		// inject review details within the arguments of the events
		$args[] = $this->review;

		// fetch subject
		$subject = \JText::sprintf('VRREVIEWSUBJECT', \VREFactory::getConfig()->getString('restname'));
			
		// fetch body
		$body = $this->getTemplate();

		// create mail instance
		return (new Mail)
			->setSubject($subject)
			->setBody($body);
	}
}
