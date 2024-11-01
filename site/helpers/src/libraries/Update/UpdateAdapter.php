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
 * Update adapter base class.
 *
 * @since 1.9
 */
abstract class UpdateAdapter
{
	/**
	 * An array of rules to execute, grouped by method.
	 *
	 * @var UpdateRule[]
	 */
	protected $rules = [];

	/**
	 * Method run during update process.
	 *
	 * @param   mixed  $parent  The parent that calls this method.
	 *
	 * @return  bool   True on success, otherwise false to stop the flow.
	 */
	final public function update($parent)
	{
		// process "update" tasks pool
		return $this->executeRules('update', $parent);
	}

	/**
	 * Method run during postflight process.
	 *
	 * @param   mixed  $parent  The parent that calls this method.
	 *
	 * @return  bool   True on success, otherwise false to stop the flow.
	 */
	final public function finalise($parent)
	{
		// process "finalise" tasks pool
		return $this->executeRules('finalise', $parent);
	}

	/**
	 * Method run before executing VikRestaurants for the first time
	 * after the update completion.
	 *
	 * @param   mixed  $parent  The parent that calls this method.
	 *
	 * @return  void
	 */
	final public function afterupdate($parent)
	{
		// process "afterupdate" tasks pool
		$success = $this->executeRules('afterupdate', $parent);

		if ($success)
		{
			// update BC version to the current one
			\VREFactory::getConfig()->set('bcv', $this->getVersion($safe = false));
		}

		return $success;
	}

	/**
	 * Executes all the rules attached to the specified action.
	 *
	 * @param   string  $action  The action to launch.
	 * @param   mixed   $parent  The parent that calls this method.
	 *
	 * @return  bool    True on success, false otherwise.
	 */
	private function executeRules(string $action, $parent)
	{
		if (!isset($this->rules[$action]))
		{
			// nothing to execute
			return true;
		}

		// iterate all rules
		foreach ($this->rules[$action] as $rule)
		{
			// do not run the same rule more than once
			if (!$rule->did())
			{
				// trigger the rule
				if ($rule->launch($parent) === false)
				{
					// something went wrong, do not go ahead
					return false;
				}
			}
		}

		// all the rules went fine
		return true;
	}

	/**
	 * Attaches a new rule to execute.
	 * It is possible to pass either a `UpdateRule` instance or a string,
	 * which will be used as identifier to auto-load the rule class.
	 * 
	 * In example, in case of "FooBar", the system will search for a class named
	 * "E4J\VikRestaurants\Update\Adapters\Update[VERSION]\FooBar".
	 *
	 * The version has to be safely reported by replacing the dots with the underscores:
	 * "1.9" becomes "1_9".
	 *
	 * @param   string  $action  The action to launch.
	 * @param   mixed   $rule    Either a rule class or a string.
	 *
	 * @return  self    This object to support chaining.
	 *
	 * @throws  \RuntimeException
	 */
	final protected function attachRule(string $action, $rule)
	{
		if (!isset($this->rules[$action]))
		{
			// init action pool first
			$this->rules[$action] = [];
		}

		if (!$rule instanceof UpdateRule)
		{
			// string received, auto-load it from the version folder
			$version = $this->getVersion();

			if (!$version)
			{
				// version not contained within the classname
				throw new \RuntimeException('Unable to detect the version', 500);
			}

			// constuct update rule class name
			$classname = 'E4J\\VikRestaurants\\Update\\Adapters\\Update' . $version . '\\' . $rule;

			// make sure the class exists
			if (!class_exists($classname))
			{
				// class not found
				throw new \RuntimeException(sprintf('Update rule class [%s] not found', $classname), 404);
			}

			// instantiate rule
			$rule = new $classname($this->getVersion(false));

			if (!$rule instanceof UpdateRule)
			{
				// not a valid update rule
				throw new \UnexpectedValueException('Update rule instance expected, ' . get_class($rule) . ' given');
			}
		}

		// append the rule
		$this->rules[$action][] = $rule;

		return $this;
	}

	/**
	 * Returns the adapter version.
	 *
	 * @param   bool  $safe  False to replace underscores with dots.
	 *
	 * @return  string
	 */
	protected function getVersion(bool $safe = true)
	{
		// get the classname of the current class
		$classname = get_class($this);

		// make sure the adapter includes the version within the name
		if (preg_match("/UpdateAdapter([0-9_]+)$/", $classname, $match))
		{
			$version = end($match);

			if (!$safe)
			{
				// replaces underscores with dots
				$version = preg_replace("/_+/", '.', $version);
			}
			
			return $version;
		}

		// unable to detect the version
		return null;
	}
}
