<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Backup\Export;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Backup export rule abstraction.
 * 
 * @since 1.9
 */
abstract class Rule implements \JsonSerializable
{
	/**
	 * Returns the rule identifier.
	 * 
	 * @return  string
	 */
	abstract public function getRule();

	/**
	 * Returns the rules instructions.
	 * 
	 * @return  mixed
	 */
	abstract public function getData();

	/**
	 * Creates a standard object, containing all the supported properties,
	 * to be used when this class is passed to "json_encode()".
	 *
	 * @return  object
	 *
	 * @see     JsonSerializable
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		$rule = new \stdClass;
		$rule->role        = $this->getRule();
		$rule->data        = $this->getData();
		$rule->dateCreated = \JFactory::getDate()->toSql();

		return $rule;
	}
}
