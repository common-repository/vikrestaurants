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
 * Wrapper used to handle mail notifications to be sent
 * to the administrators every time a customer makes
 * a take-away order cancellation.
 *
 * @since 1.9
 */
class CancellationMailTemplate extends MailTemplateAware
{
	/**
	 * The order object.
	 *
	 * @var \VREOrderTakeaway
	 */
	protected $order;

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
	 * @param  mixed   $order    Either the order ID or the order object.
	 * @param  array   $options  A configuration array.
	 */
	public function __construct($order, array $options = [])
	{
		if (empty($options['lang']))
		{
			// always use default language in case it is not specified
			$options['lang'] = \VikRestaurants::getDefaultLanguage();
		}

		if ($order instanceof \VREOrderTakeaway)
		{
			/**
			 * Directly use the specified order.
			 *
			 * @since 1.8.2
			 */
			$this->order = $order;
		}
		else
		{
			// recover order details for the given language
			$this->order = \VREOrderFactory::getOrder($order, $options['lang']);
		}

		// register options
		$this->options = $options;

		// load given language to translate template contents
		\VikRestaurants::loadLanguage($this->options['lang']);

		// inject cancellation reason within order object if provided
		if (isset($options['cancellation_reason']))
		{
			$this->order->cancellation_reason = $options['cancellation_reason'];
		}

		if ($this->order->statusRole !== 'CANCELLED')
		{
			// we are probably in test mode, update the status of the order with a correct one
			$this->order->status = \JHtml::fetch('vrehtml.status.cancelled', 'takeaway', 'code');
		}

		// format order items prices
		$currency = \VREFactory::getCurrency();

		foreach ($this->order->items as $item)
		{
			// format price
			$item->formattedPrice = $currency->format($item->price * $item->quantity);
		}

		// use global sender
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\SenderMailDecorator);

		// set all the administrators as recipient
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RecipientAdministratorsMailDecorator);

		// set all the operators (with notifications enabled) as recipient (2 stands for "take-away" group)
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RecipientOperatorsMailDecorator(2, $this->order));

		// inject generic company information, such as the restaurant name and the image logo
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CompanyMailDecorator);

		// inject generic order information, such as order ID and payment details
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\OrderMailDecorator($this->order));

		// inject take-away order information
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\TakeawayOrderMailDecorator($this->order, $this->options['lang']));

		// inject cancellation details (2 stands for "take-away" group)
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CancellationMailDecorator(2, $this->order));
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
		// copy order details in a local
		// variable for being used directly
		// within the template file
		$order = $this->order;

		if ($this->templateFile)
		{
			// use specified template file
			$file = $this->templateFile;
		}
		else
		{
			// get template file from configuration
			$file = \VREFactory::getConfig()->get('tkcancmailtmpl');

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
		unset($order);

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
		// inject order details within the arguments of the events
		$args[] = $this->order;

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
