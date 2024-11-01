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
 * Wrapper used to handle mail notifications for the
 * customers that book a take-away order.
 *
 * @since 1.9
 */
class CustomerMailTemplate extends MailTemplateAware
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
	 * @param  array   $options  An array of options.
	 */
	public function __construct($order, array $options = [])
	{
		if (empty($options['lang']))
		{
			$options['lang'] = null;
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

		if (!$options['lang'])
		{
			// use order lang tag in case it was not specified
			$options['lang'] = $this->order->get('langtag', null);

			if (!$options['lang'])
			{
				// the order is not assigned to any lang tag, use the current one
				$options['lang'] = \JFactory::getLanguage()->getTag();
			}
		}

		// register options
		$this->options = $options;

		// load given language to translate template contents
		\VikRestaurants::loadLanguage($this->options['lang']);

		// format order items prices
		$currency = \VREFactory::getCurrency();

		foreach ($this->order->items as $item)
		{
			// format price
			$item->formattedPrice = $currency->format($item->price * $item->quantity);
		}

		// use global sender
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\SenderMailDecorator);

		// inject generic company information, such as the restaurant name and the image logo
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CompanyMailDecorator);

		// inject generic order information, such as order ID and payment details
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\OrderMailDecorator($this->order));

		// inject take-away order information
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\TakeawayOrderMailDecorator($this->order, $this->options['lang']));
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
			$file = \VREFactory::getConfig()->get('tkmailtmpl');

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
		// check if the customer is allowed to self-confirm its order
		if (\VikRestaurants::canUserApproveOrder($this->order))
		{
			// self-confirmation is allowed, force the e-mail sending
			// because the confirmation link can be found only within
			// the notification e-mail sent
			return true;
		}

		// get list of statuses for which the notification should be sent
		$list = \VREFactory::getConfig()->getArray('tkmailcustwhen');

		// make sure the order status is contained within the list
		return in_array($this->order->status, $list);
	}

	/**
	 * @inheritDoc
	 */
	final protected function createMail(array &$args)
	{
		// inject order details within the arguments of the events
		$args[] = $this->order;

		// get recipient from order detail
		$recipient = $this->order->purchaser_mail;

		if (!$recipient)
		{
			// check whether the recipient has been provided within the configuration array
			$recipient = $this->options['recipient'] ?? null;

			if (!$recipient)
			{
				// missing recipient
				throw new \RuntimeException('Missing recipient', 400);
			}
		}

		// get administrator e-mail
		$adminMail = \VikRestaurants::getAdminMail();

		// fetch subject
		$subject = \JText::sprintf('VRCUSTOMEREMAILTKSUBJECT', \VREFactory::getConfig()->getString('restname'));
			
		// fetch body
		$body = $this->getTemplate();

		// create mail instance
		return (new Mail)
			->addRecipient($recipient)
			->setReplyTo($adminMail)
			->setSubject($subject)
			->setBody($body);
	}
}
