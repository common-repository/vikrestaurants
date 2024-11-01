<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Types;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\Field;

/**
 * VikRestaurants custom field separator handler.
 *
 * @since 1.9
 */
class SeparatorField extends Field
{
	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return \JText::translate('VRCUSTOMFTYPEOPTION6');
	}

	/**
	 * @inheritDoc
	 */
	final public function isCollectable()
	{
		// separator fields are not used to collect data
		return false;
	}

	/**
	 * @inheritDoc
	 */
	final protected function extract()
	{
		return '';
	}

	/**
	 * @inheritDoc
	 */
	protected function getControl(array $data, string $input = '')
	{
		// do not wrap field within a control
		return !$input ? $this->getInput($data) : $input;
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData(array $data)
	{
		// init display data
		$data = parent::getDisplayData($data);

		if ($sfx = $this->get('choose'))
		{
			// inject class suffix
			$data['class'] = trim($data['class'] . ' ' . $sfx);
		}

		return $data;
	}
}
