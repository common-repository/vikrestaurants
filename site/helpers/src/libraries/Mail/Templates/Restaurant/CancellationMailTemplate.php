<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Templates\Restaurant;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplateAware;

/**
 * Wrapper used to handle mail notifications to be sent
 * to the administrators every time a customer makes
 * a reservation cancellation.
 *
 * @since 1.9
 */
class CancellationMailTemplate extends MailTemplateAware
{
	/**
	 * The reservation object.
	 *
	 * @var \VREOrderRestaurant
	 */
	protected $reservation;

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
	 * @param  mixed  $res      Either the reservation ID or the reservation object.
	 * @param  array  $options  A configuration array.
	 */
	public function __construct($res, array $options = [])
	{
		if (empty($options['lang']))
		{
			// always use default language in case it is not specified
			$options['lang'] = \VikRestaurants::getDefaultLanguage();;
		}

		if ($res instanceof \VREOrderRestaurant)
		{
			/**
			 * Directly use the specified reservation.
			 *
			 * @since 1.8.2
			 */
			$this->reservation = $res;
		}
		else
		{
			// recover reservation details for the given language
			$this->reservation = \VREOrderFactory::getReservation($res, $options['lang']);
		}

		// register language tag
		$this->options = $options;

		// load given language to translate template contents
		\VikRestaurants::loadLanguage($this->options['lang']);

		// inject cancellation reason within reservation object if provided
		if (isset($options['cancellation_reason']))
		{
			$this->reservation->cancellation_reason = $options['cancellation_reason'];
		}

		if ($this->reservation->statusRole !== 'CANCELLED')
		{
			// we are probably in test mode, update the status of the reservation with a correct one
			$this->reservation->status = \JHtml::fetch('vrehtml.status.cancelled', 'restaurant', 'code');
		}

		// use global sender
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\SenderMailDecorator);

		// set all the administrators as recipient
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RecipientAdministratorsMailDecorator);

		// set all the operators (with notifications enabled) as recipient (1 stands for "restaurant" group)
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RecipientOperatorsMailDecorator(1, $this->reservation));

		// inject generic company information, such as the restaurant name and the image logo
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CompanyMailDecorator);

		// inject generic order information, such as order ID and payment details
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\OrderMailDecorator($this->reservation));

		// inject restaurant reservation information
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RestaurantReservationMailDecorator($this->reservation, $this->options['lang']));

		// inject cancellation details (1 stands for "restaurant" group)
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CancellationMailDecorator(1, $this->reservation));
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
			$file = VREHELPERS . '/mail_tmpls/' . $file;
		}

		$this->templateFile = \JPath::clean($file);
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplate()
	{
		// copy reservation details in a local
		// variable for being used directly
		// within the template file
		$reservation = $this->reservation;

		if ($this->templateFile)
		{
			// use specified template file
			$file = $this->templateFile;
		}
		else
		{
			// get template file from configuration
			$file = \VREFactory::getConfig()->get('cancmailtmpl');

			// build template path
			$file = \JPath::clean(VREHELPERS . '/mail_tmpls/' . $file);
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
		unset($reservation);

		return $content;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldSend()
	{
		// always send cancellation e-mails
		return true;
	}

	/**
	 * @inheritDoc
	 */
	final protected function createMail(array &$args)
	{
		// inject reservation details within the arguments of the events
		$args[] = $this->reservation;

		// fetch subject
		$subject = \JText::sprintf('VRORDERCANCELLEDSUBJECT', \VREFactory::getConfig()->getString('restname'));
			
		// fetch body
		$body = $this->getTemplate();

		// create mail instance
		return (new Mail)
			->setSubject($subject)
			->setBody($body);
	}
}
