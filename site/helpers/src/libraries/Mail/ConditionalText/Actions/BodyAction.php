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
 * Action used to register an additional text to the body of the mail.
 *
 * @since 1.9
 */
class BodyAction extends ConditionalTextActionAware
{
	use LongTextSummaryTrait;

	/**
	 * A lookup used to identify all the supported positions.
	 * 
	 * @var array
	 */
	protected static $supportedPositions = null;

	/**
	 * @inheritDoc
	 */
	public function __construct($options = [])
	{
		parent::__construct($options);
	}

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * A list containing all the supported custom positions where the
			 * text can be introduced.
			 * 
			 * @var string 
			 */
			'position' => [
				'type'  => 'select',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_BODY_POSITION'),
				'description' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_BODY_POSITION_DESC'),
				'value' => $this->options->get('position', ''),
				'options' => $this->getSupportedPositions(),
			],

			/**
			 * The editor used to insert the text to inject into the body.
			 * 
			 * @var string
			 */
			'text' => [
				'type' => 'editor',
				'label' => \JText::translate('VRE_CONDITIONAL_TEXT_ACTION_BODY_TEXT'),
				'value' => $this->options->get('text', ''),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-envelope';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		/** @see LongTextSummaryTrait */
		return $this->createSummary($this->options->get('text', ''), [
			'length' => 64,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function preflight(Mail $mail)
	{
		if (is_null(static::$supportedPositions))
		{
			// define default supported positions only once
			static::$supportedPositions = [];

			foreach ($this->getSupportedPositions() as $position)
			{
				static::$supportedPositions[$position] = [];
			}
		}

		// fetch the text that should be injected within the body
		$text = $this->options->get('text', '');

		if (!$text)
		{
			// nothing to append
			return;
		}

		// fetch the position where the text should be injected
		$position = $this->options->get('position', 'custom_position_middle');

		if ($position)
		{
			if (!isset(static::$supportedPositions[$position]))
			{
				// position not defined, create new slot
				static::$supportedPositions[$position] = [];
			}

			// append text into the provided position
			static::$supportedPositions[$position][] = $text;
		}
		else
		{
			// position not provided, directly append at the end
			$mail->setBody($mail->getBody() . "\n" . $text);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function apply(Mail $mail)
	{
		if (is_null(static::$supportedPositions))
		{
			// positions already fetched, do not need to proceed
			return;
		}

		$body = $mail->getBody();

		// scan all the registered positions
		foreach (static::$supportedPositions as $position => $texts)
		{
			// replace position tag with the related texts
			$body = str_replace('{' . $position . '}', implode("\n", $texts), $body);
		}

		// update mail body
		$mail->setBody($body);

		// clear supported positions to avoid executing useless code
		static::$supportedPositions = null;
	}

	/**
	 * Returns an array containing all the supported positions where the custom
	 * text can be introduced.
	 * 
	 * @return  string[]
	 */
	public function getSupportedPositions()
	{
		/**
		 * @todo consider to fire an hook to allow the extendibility of the custom positions
		 */

		return [
			'custom_position_top',
			'custom_position_middle',
			'custom_position_bottom',
			'custom_position_footer',
		];
	}
}
