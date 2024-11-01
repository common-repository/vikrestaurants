<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;

/**
 * Interface used to implement an action for an eligible conditional text.
 * Conditional text actions are commonly used to alter the mailing data
 * during the notification, such as the body, the subject and so on.
 *
 * @since 1.9
 */
interface ConditionalTextAction
{
	/**
	 * Fires before the apply method.
	 * 
	 * @param   Mail  $mail  The mail instance where the changes should
	 *                       be applied.
	 * 
	 * @return  void
	 */
	public function preflight(Mail $mail);

	/**
	 * Applies the changes defined by the conditional text to a mail
	 * instance before sending the notification.
	 * 
	 * @param   Mail  $mail  The mail instance where the changes should
	 *                       be applied.
	 * 
	 * @return  void
	 */
	public function apply(Mail $mail);
}
