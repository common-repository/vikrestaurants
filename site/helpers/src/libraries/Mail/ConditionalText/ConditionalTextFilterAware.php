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
 * Conditional text filter adapter.
 *
 * @since 1.9
 */
abstract class ConditionalTextFilterAware extends ConditionalTextManageableAware implements ConditionalTextFilter
{
	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		$id = $this->getID();

		// define language key
		$langKey = 'VRE_CONDITIONAL_TEXT_FILTER_' . strtoupper($id);

		// try to translate the name
		$name = \JText::translate($langKey);

		if ($langKey === $name)
		{
			// missing translation, use default name
			$name = parent::getName();
		}

		return $name;
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		$id = $this->getID();

		// define language key
		$langKey = 'VRE_CONDITIONAL_TEXT_FILTER_' . strtoupper($id) . '_DESC';

		// try to translate the description
		$description = \JText::translate($langKey);

		if ($langKey === $description)
		{
			// missing translation
			$description = '';
		}

		return $description;
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		// use default icon
		return 'fas fa-sliders-h';
	}
}
