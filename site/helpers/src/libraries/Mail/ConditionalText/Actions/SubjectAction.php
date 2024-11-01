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
use E4J\VikRestaurants\Mail\ConditionalText\Helpers\LongTextSummaryTrait;

/**
 * Action used to manipulate the subject of the mail.
 *
 * @since 1.9
 */
class SubjectAction extends ConditionalTextActionAware
{
	use LongTextSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * The subject to apply to the e-mail.
			 * 
			 * @var string
			 */
			'subject' => [
				'type'  => 'textarea',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_TEXT'),
				'value' => $this->options->get('subject', ''),
			],

			/**
			 * How the subject should be applied.
			 * 
			 * @var string
			 */
			'mode' => [
				'type'  => 'select',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE'),
				'description' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_DESC'),
				'value' => $this->options->get('mode', 'replace'),
				'options' => [
					'replace' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_OPT_REPLACE'),
					'append'  => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_OPT_APPEND'),
					'prepend' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_SUBJECT_MODE_OPT_PREPEND'),
				],
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-heading';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		/** @see LongTextSummaryTrait */
		return $this->createSummary($this->options->get('subject', ''), [
			'length' => 64,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function apply(Mail $mail)
	{
		// get provided subject
		$subject = $this->options->get('subject', '');

		// check how the subject should be updated
		$mode = $this->options->get('mode', 'replace');

		if ($mode === 'append')
		{
			// append subject after the existing one
			$subject = $mail->getSubject() . ' ' . trim($subject);
		}
		else if ($mode === 'prepend')
		{
			// prepend subject before the existing one
			$subject = trim($subject) . ' ' . $mail->getSubject();
		}

		// replace mailing subject
		$mail->setSubject($subject);
	}
}
