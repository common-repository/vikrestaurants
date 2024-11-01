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
 * Mail template adapter used for backward compatibility.
 * 
 * @since 1.9
 * @deprecated 1.10  Without replacement.
 */
trait VREMailTemplateadapter
{
	/**
	 * @see VREMailTemplate::setFile()
	 */
	public function setFile($file)
	{
		$this->adaptee->setFile($file);
	}

	/**
	 * @see VREMailTemplate::getTemplate()
	 */
	public function getTemplate()
	{
		return $this->adaptee->getTemplate();
	}

	/**
	 * @see VREMailTemplate::getSubject()
	 */
	public function getSubject()
	{
		return (string) $this->getMail()->getSubject();
	}

	/**
	 * @see VREMailTemplate::getHtml()
	 */
	public function getHtml()
	{
		return (string) $this->getMail()->getBody();
	}

	/**
	 * @see VREMailTemplate::send()
	 */
	public function send()
	{
		return (new E4J\VikRestaurants\Mail\MailDeliverer([
			'silent' => true,
			'admin'  => JFactory::getApplication()->isClient('administrator'),
		]))->send($this->getMail());
	}

	/**
	 * @see VREMailTemplate::shouldSend()
	 */
	public function shouldSend()
	{
		return $this->adaptee->shouldSend();
	}

	/**
	 * Caches the mailing information.
	 * 
	 * @return  E4J\VikRestaurants\Mail\Mail
	 */
	protected function getMail()
	{
		static $mail = null;

		if (!$mail)
		{
			$mail = $this->adaptee->getMail();
		}

		return $mail;
	}
}
