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
 * Adds support to the following template data (tags):
 * 
 * - {stocks_content}  An intro for the mail notification.
 * - {stocks_help}     A walkthrough for the administrator explaining how to refill the stocks.
 *
 * @since 1.9
 */
final class TakeawayStockMailDecorator implements MailTemplateDecorator
{
	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		// register  template data
		$template->addTemplateData([
			'stocks_content' => \JText::translate('VRTKADMINLOWSTOCKCONTENT'),
			'stocks_help'    => \JText::translate('VRTKADMINLOWSTOCKHELP'),
		]);
	}
}
