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
 * Interface used to implement new mail template decorators.
 * A decorator has the scope to fulfill the mail instance with
 * custom tags, recipients, senders and so on.
 *
 * @since 1.9
 */
interface MailTemplateDecorator
{
	/**
	 * Builds the mail instance.
	 * 
	 * @param   Mail          $mail      The mail to fulfill.
	 * @param   MailTemplate  $template  The template instance (@see addTemplateData).
	 *
	 * @return 	void
	 */
	public function build(Mail $mail, MailTemplate $template);
}
