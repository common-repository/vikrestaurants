<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Actions;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextActionAware;
use E4J\VikRestaurants\Mail\ConditionalText\Helpers\CountableItemsSummaryTrait;

/**
 * Action used to register additional recipients that will receive
 * the e-mail notification.
 *
 * @since 1.9
 */
class RecipientAction extends ConditionalTextActionAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * A textarea containing all the e-mail addresses to include as recipient.
			 * 
			 * @var string
			 */
			'recipients' => [
				'type'  => 'textarea',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_RECIPIENT_ADDRESSES'),
				'description' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_RECIPIENT_ADDRESSES_DESC'),
				'value' => $this->options->get('recipients', ''),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-at';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		/** @see CountableItemsSummaryTrait */
		return $this->createSummary($this->getRecipients());
	}

	/**
	 * @inheritDoc
	 */
	public function apply(Mail $mail)
	{
		// iterate all addresses
		foreach ($this->getRecipients() as $recipient)
		{
			// register new recipient
			$mail->addRecipient($recipient);
		}
	}

	/**
	 * Returns a list of provided recipients.
	 * 
	 * @return  string[]
	 */
	protected function getRecipients()
	{
		// obtain the list of all the addresses to include
		$recipients = $this->options->get('recipients', []);

		if (is_string($recipients))
		{
			// convert comma-separated list into an array of e-mail addresses
			$recipients = array_values(array_filter(preg_split("/\s*,\s*/", $recipients)));
		}

		return $recipients;
	}
}
