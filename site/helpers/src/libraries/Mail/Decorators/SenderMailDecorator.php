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
 * Configures the sender of the mail instance with the sender address specified
 * from the global configuration of VikRestaurants and the name of the restaurant.
 *
 * @since 1.9
 */
final class SenderMailDecorator implements MailTemplateDecorator
{
	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		// set up e-mail sender
		$mail->setSender(
			// get global sender e-mail address
			\VikRestaurants::getSenderMail(),
			// get restaurant name from configuration
			\VREFactory::getConfig()->getString('restname')
		);
	}
}
