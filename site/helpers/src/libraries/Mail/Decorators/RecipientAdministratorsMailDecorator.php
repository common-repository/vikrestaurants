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
 * Sets the e-mail address of all the administrators as recipient.
 * 
 * @since 1.9
 */
final class RecipientAdministratorsMailDecorator implements MailTemplateDecorator
{
	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		// obtain the list of all the administrator e-mail addresses
		foreach (\VikRestaurants::getAdminMailList() as $recipient)
		{
			// set administrator as recipient
			$mail->addRecipient($recipient);
		}
	}
}
