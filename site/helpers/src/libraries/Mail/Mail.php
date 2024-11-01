<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Encapsulates the information of an electronic mail.
 * 
 * @since 1.9
 */
class Mail
{
	/**
	 * The e-mail address of the sender.
	 * 
	 * @var string|null
	 */
	protected $senderMail;

	/**
	 * The name of the sender.
	 * 
	 * @var string|null
	 */
	protected $senderName;

	/**
	 * A list of addresses that should receive the e-mail message.
	 * 
	 * @var object[]
	 */
	protected $recipients = [];

	/**
	 * The reply-to e-mail address.
	 * 
	 * @var string|null
	 */
	protected $replyTo;

	/**
	 * The e-mail subject.
	 * 
	 * @var string|null
	 */
	protected $subject;

	/**
	 * The e-mail body message.
	 * 
	 * @var string|null
	 */
	protected $body;

	/**
	 * Flag used to check whether the e-mail should be sent with TEXT/HTML 
	 * or TEXT/PLAIN format.
	 * 
	 * @var bool
	 */
	protected $isHtml = true;

	/**
	 * A list of attachments to include within the e-mail.
	 * The full path to the file should be included here.
	 * 
	 * @var string[]
	 */
	protected $attachments = [];

	/**
	 * Magic method used to access the internal properties of this object.
	 * 
	 * @param  string  $method  The invoked method.
	 * @param  array   $args    A list of specified arguments.
	 */
	public function __call(string $method, array $args)
	{
		if ($method === 'isHtml')
		{
			return $this->isHtml;
		}
		else if (substr($method, 0, 3) === 'get')
		{
			// make sure the property exists
			$propertyName = lcfirst(substr($method, 3));

			if (property_exists($this, $propertyName))
			{
				// obtain the property value
				$value = $this->{$propertyName};

				// make sure we have a value set
				return $value !== null && $value !== ''
					// return the value
					? $value
					// in case of default value, return it instead
					: ($args ? $args[0] : null);
			}
		}

		// unhandled method, throw error
		throw new \BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $method . '()');
	}

	/**
	 * Sets the sender information.
	 * 
	 * @param   string  $mail
	 * @param   string  $name
	 * 
	 * @return  self
	 */
	public function setSender(string $mail, string $name = null)
	{
		return $this->setSenderMail($mail)->setSenderName($name);
	}

	/**
	 * Sets the sender e-mail address.
	 * 
	 * @param   string  $mail
	 * 
	 * @return  self
	 */
	public function setSenderMail(string $mail)
	{
		$this->senderMail = $mail;

		return $this;
	}

	/**
	 * Sets an optional name for the sender.
	 * 
	 * @param   string  $name
	 * 
	 * @return  self
	 */
	public function setSenderName(string $name = null)
	{
		$this->senderName = $name;
		
		return $this;
	}

	/**
	 * Sets the e-mail recipient(s).
	 * 
	 * @param   array  $recipients  A list of recipients to attach.
	 * 
	 * @return  self
	 */
	public function setRecipients(array $recipients)
	{
		$this->recipients = [];

		foreach ($recipients as $recipient)
		{
			if (is_string($recipient))
			{
				// provided only the e-mail address
				$recipient = [
					'mail' => $recipient,
					'name' => '',
				];
			}

			// cast to array for a better ease of use
			$recipient = (object) $recipient;

			// add the recipient
			$this->addRecipient($recipient->mail ?? '', $recipient->name ?? null);
		}

		return $this;
	}

	/**
	 * Adds an e-mail recipient.
	 * 
	 * @param   string  $mail
	 * @param   string  $name
	 * 
	 * @return  self
	 */
	public function addRecipient(string $mail, string $name = null)
	{
		if ($mail)
		{
			// create recipient element
			$recipient = new \stdClass;
			$recipient->mail = $mail;
			$recipient->name = $name;

			// register the recipient
			$this->recipients[] = $recipient;
		}

		return $this;
	}

	/**
	 * Sets the reply-to e-mail address.
	 * 
	 * @param   string  $mail
	 * 
	 * @return  self
	 */
	public function setReplyTo(string $mail)
	{
		$this->replyTo = $mail;

		return $this;
	}

	/**
	 * Sets the e-mail subject.
	 * 
	 * @param   string  $subject
	 * 
	 * @return  self
	 */
	public function setSubject(string $subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Sets the e-mail body.
	 * 
	 * @param   string  $body
	 * 
	 * @return  self
	 */
	public function setBody(string $body)
	{
		$this->body = $body;

		return $this;
	}

	/**
	 * Applies the requested e-mail format (false: TEXT/PLAIN, true: TEXT/HTML).
	 * 
	 * @param   bool  $isHtml
	 * 
	 * @return  self
	 */
	public function setHtml(bool $isHtml)
	{
		$this->isHtml = $isHtml;

		return $this;
	}

	/**
	 * Sets the e-mail attachment(s).
	 * 
	 * @param   string|array  $attachments  Either an array or a file string.
	 * 
	 * @return  self
	 */
	public function setAttachments($attachments)
	{
		$this->attachments = [];

		return $this->addAttachment($attachments);
	}

	/**
	 * Adds the e-mail attachment(s).
	 * 
	 * @param   string|array  $attachments  Either an array or a file string.
	 * 
	 * @return  self
	 */
	public function addAttachment($attachments)
	{
		foreach ((array) $attachments as $attachment)
		{
			if (\JFile::exists($attachment))
			{
				// inject file only in case it actually exists
				$this->attachments[] = $attachment;
			}
		}

		return $this;
	}
}
