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
 * Action used to include the provided attachments to the mail.
 *
 * @since 1.9
 */
class AttachmentsAction extends ConditionalTextActionAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * The file manager to choose the files to include as attachment.
			 * 
			 * @var string[]
			 */
			'files' => [
				'type'  => 'media',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_ATTACHMENTS_FILES'),
				'value' => $this->options->get('files', []),
				'multiple' => true,
				'attributes' => [
					'path'     => $this->options->get('path', VRE_MAIL_ATTACHMENTS),
					'filter'   => false,
					'preview'  => false,
					'icon'     => 'fas fa-upload',
				],
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-paperclip';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		/** @see CountableItemsSummaryTrait */
		return $this->createSummary($this->options->get('files', []));
	}

	/**
	 * @inheritDoc
	 */
	public function apply(Mail $mail)
	{
		// get files from configuration
		$files = (array) $this->options->get('files', []);

		// obtain the path of the folder where the files are located
		$path = $this->options->get('path', VRE_MAIL_ATTACHMENTS);

		// iterate all files
		foreach ($files as $file)
		{
			// prepend path to file name and register attachment
			$mail->addAttachment(\JPath::clean($path . '/' . $file));
		}
	}
}
