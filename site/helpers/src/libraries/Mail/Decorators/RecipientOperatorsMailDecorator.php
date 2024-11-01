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
 * Sets the e-mail address of all the operators that should receive notifications as recipient.
 *
 * @since 1.9
 */
final class RecipientOperatorsMailDecorator implements MailTemplateDecorator
{
	/** @var int */
	private $group;

	/** @var \VREOrderWrapper */
	private $order;

	/**
	 * Class constructor.
	 * 
	 * @param  int    $group  The order group (1: restaurant, 2: take-away).
	 * @param  mixed  $order  The order details.
	 */
	public function __construct(int $group, \VREOrderWrapper $order = null)
	{
		$this->group = $group;
		$this->order = $order;
	}

	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		// iterate each operator e-mail that should be notified
		foreach (\VikRestaurants::getNotificationOperatorsMails($this->group, $this->order) as $recipient)
		{
			// set operator as recipient
			$mail->addRecipient($recipient);
		}
	}
}
