<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Update rule abstract class.
 *
 * @since 1.9
 */
abstract class UpdateRule
{
	/** @var string */
	protected $version;

	/**
	 * Class constructor.
	 * 
	 * @param  string  $version  The update version.
	 */
	public function __construct(string $version)
	{
		$this->version = $version;
	}

	/**
	 * Returns the rule name.
	 * 
	 * @return  string
	 * 
	 * @throws  \RuntimeException
	 */
	public function getName()
	{
		$classname = get_class($this);

		// extract rule name from class
		if (!preg_match("/\\\\([a-zA-Z0-9_]+)$/", $classname, $match))
		{
			throw new \RuntimeException('Malformed class name: ' . $classname);
		}

		return strtolower($match[1]);
	}

	/**
	 * Method run during update process.
	 *
	 * @param   mixed  $parent  The parent that calls this method.
	 *
	 * @return  bool   True on success, otherwise false to stop the flow.
	 */
	final public function launch($parent)
	{
		// invoke run method declared by the implementors
		$result = $this->run($parent);

		if ($result !== false)
		{
			// auto-flag task as completed
			$this->complete();
		}

		return $result;
	}

	/**
	 * Method run during update process.
	 *
	 * @param   mixed  $parent  The parent that calls this method.
	 *
	 * @return  bool   True on success, otherwise false to stop the flow.
	 */
	abstract protected function run($parent);

	/**
	 * Children classes can override this method to avoid executing the
	 * same rule more than once.
	 *
	 * @return  bool  True to skip the rule execution.
	 */
	public function did()
	{
		// get pool of completed tasks
		$tasks = \VREFactory::getConfig()->getArray('updatetasks', []);

		if (!isset($tasks[$this->version]))
		{
			// missing version slot
			return false;
		}

		// check whether this rule is contained within the list
		return in_array($this->getName(), $tasks[$this->version]);
	}

	/**
	 * Marks the task as completed.
	 * Children classes can invoke this method at the end of the run
	 * in order to prevent a double execution.
	 *
	 * @return  void
	 */
	protected function complete()
	{
		$config = \VREFactory::getConfig();

		// get pool of completed tasks
		$tasks = $config->getArray('updatetasks', []);

		if (!isset($tasks[$this->version]))
		{
			// create version slot
			$tasks[$this->version] = [];
		}

		// register this task within the list to mark it as completed
		$tasks[$this->version][] = $this->getName();

		$config->set('updatetasks', $tasks);
	}
}
