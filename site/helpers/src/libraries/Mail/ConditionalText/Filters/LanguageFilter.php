<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextFilterAware;

/**
 * Filters the conditional text by language.
 *
 * @since 1.9
 */
class LanguageFilter extends ConditionalTextFilterAware
{
	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * A list of supported languages
			 * 
			 * @var string[]
			 */
			'langtag' => [
				'type'  => 'select',
				'label' => \JText::translate('VRMANAGELANG4'),
				'value' => $this->options->get('langtag', ''),
				'options' => \JHtml::fetch('contentlanguage.existing', $all = false, $translate = true),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-language';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		return $this->options->get('langtag', '');
	}

	/**
	 * @inheritDoc
	 */
	public function isEligible(string $templateId, array $data)
	{
		// the last element of the $data array should always be a configuration array
		$options = end($data);

		// make sure we have a valid configuration and the language is set
		if (is_array($options) && !empty($options['lang']))
		{
			// use provided language
			$language = $options['lang'];
		}
		else
		{
			// use the current language
			$language = \JFactory::getLanguage()->getTag();
		}

		// fetch configured language
		$tag = $this->options->get('langtag', '');

		// make sure the language is supported
		return $tag === $language;
	}
}
