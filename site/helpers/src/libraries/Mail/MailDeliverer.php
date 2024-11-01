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
 * Helper class used to send e-mail messages through the CMS mailing functions.
 * 
 * @since 1.9
 */
class MailDeliverer
{
	/** @var \JRegistry */
	protected $options;

	/**
	 * Class constructor.
	 * 
	 * @param  array  $options  A configuration array.
	 *                          - silent  bool  True to ignore any thrown exception and go ahead
	 *                                          without breaking the process.
	 *                          - admin   bool  True to enqueue a system message in case of errors.
	 *                                          Applies only in case of `silent` delivery.
	 */
	public function __construct(array $options = [])
	{
		// wrap configuration in a registry for a better ease of use
		$this->options = new \JRegistry($options);
	}

	/**
	 * Delivers the provided e-mail.
	 * 
	 * @param   Mail  $mail
	 * 
	 * @return  bool  True on success, false otherwise.
	 */
	public function send(Mail $mail)
	{
		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		try
		{
			// create mail transporter
			$transporter = $this->createTransporter($mail);

			/**
			 * Fires before delivering an e-mail.
			 * NOTE: it is possible to throw an exception to abort the mail delivery.
			 * In this case, you might want to avoid breaking the process, things that
			 * you can accomplish by using the code below before throwing the error:
			 * `$options->set('silent', true);`
			 * 
			 * @param   Mail        $mail         The e-mail information.
			 * @param   \JMail      $transporter  The e-mail transporter.
			 * @param   \JRegistry  $options      The configuration registry.
			 * 
			 * @return  void
			 * 
			 * @since   1.9
			 */
			$dispatcher->trigger('onBeforeSendMail', [$mail, $transporter, $this->options]);

			// attempt to send the message
			$result = (bool) $transporter->Send();
		}
		catch (\Exception $e)
		{
			// register error
			$result = $e;
		}

		/**
		 * Fires after delivering an e-mail.
		 * 
		 * @param   \Exception|bool  $status       The delivery status or an exception.
		 * @param   Mail             $mail         The e-mail information.
		 * @param   \JMail           $transporter  The e-mail transporter.
		 * @param   \JRegistry       $options      The configuration registry.
		 * 
		 * @return  void
		 * 
		 * @since   1.9
		 */
		$dispatcher->trigger('onAfterSendMail', [$result, $mail, $transporter, $this->options]);

		if ($result instanceof \Exception)
		{
			if (!$this->options->get('silent', false))
			{
				// propagate exception
				throw $e;
			}

			// silent delivery, do not stop the process
			$result = false;

			if ($this->options->get('admin', false))
			{
				// we have an administrator, enqueue the error message
				\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}

		return $result;
	}

	/**
	 * Creates the e-mail transporter and binds it with the provided mail information.
	 * 
	 * @param   Mail    $mail  The e-mail information to bind.
	 * 
	 * @return  \JMail  The e-mail transporter.
	 */
	protected function createTransporter(Mail $mail)
	{
		/** @var \JMail */
		$transporter = \JFactory::getMailer();

		// SENDER

		if ($senderMail = $mail->getSenderMail())
		{
			if ($senderName = $mail->getSenderName())
			{
				// both address and name provided
				$transporter->setSender([$senderMail, $senderName]);
			}
			else
			{
				// provided only the address
				$transporter->setSender($senderMail);
			}
		}

		// RECIPIENT(s)
		
		foreach ($mail->getRecipients() as $recipient)
		{
			// register recipients one by one
			$transporter->addRecipient($recipient->mail, $recipient->name ?: '');
		}

		// REPLY-TO

		if ($replyTo = $mail->getReplyTo())
		{
			// register reply-to address
			$transporter->addReplyTo($replyTo);
		}

		// SUBJECT

		if ($subject = $mail->getSubject())
		{
			// encode subject only on Joomla 3.x
			if (\VersionListener::isJoomla3x())
			{
				/**
				 * @todo is this still actually needed?
				 */
				$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
			}

			// register e-mail subject
			$transporter->setSubject($subject);
		}

		// BODY

		if ($body = $mail->getBody())
		{
			// in case we have an HTML message, make sure the body is properly wrapped within a HTML tag
			if ($mail->isHtml() && !preg_match("/<\/html>\s*$/", $body))
			{
				// nope, wrap it now
				$body = "<html>\n<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"></head>\n<body>$body</body>\n</html>";
			}

			// register e-mail body
			$transporter->setBody($body);
		}

		// ATTACHMENTS

		foreach ($mail->getAttachments() as $attachment)
		{
			// register attachments one by one
			$transporter->addAttachment($attachment);
		}

		// complete configuration
		$transporter->isHTML($mail->isHtml());
		$transporter->Encoding = 'base64';

		return $transporter;
	}
}
