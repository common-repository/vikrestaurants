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
 * Interface used to handle mail templates entities.
 *
 * @since 1.9
 */
interface MailTemplate
{
	/**
	 * Forces the usage of a new template file.
	 *
	 * @param 	string  An optional template file to use.
	 * 					If not specified, the one set in
	 * 					configuration will be used.
	 *
	 * @return 	void
	 */
	public function setFile(string $file);

	/**
	 * Returns the code of the template before being parsed.
	 *
	 * @return 	string
	 */
	public function getTemplate();

	/**
	 * Checks whether the notification should be sent.
	 *
	 * @return 	boolean
	 */
	public function shouldSend();

	/**
	 * Returns the mail instance ready to be sent.
	 *
	 * @return 	Mail
	 */
	public function getMail();
}
