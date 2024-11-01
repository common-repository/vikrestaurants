<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Wizard;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Collection class used to manage the steps needed to complete
 * a basic configuration of VikRestaurants.
 *
 * @since 1.9
 */
class Wizard implements \ArrayAccess, \IteratorAggregate
{
	/**
	 * A list of wizard steps.
	 *
	 * @var WizardStep[]
	 */
	protected $steps = [];

	/**
	 * Flag used to check whether the wizard has been dismissed.
	 *
	 * @var bool
	 */
	protected $done;

	/**
	 * Flag used to check whether the wizard has been set up.
	 *
	 * @var bool
	 */
	protected $setup = false;

	/**
	 * An internal configuration setup.
	 *
	 * @var array 
	 */
	protected $config;

	/**
	 * A list of include paths.
	 *
	 * @var array
	 */
	protected $paths = [];

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		$config = \VREFactory::getConfig();

		// check whether the wizard is still active
		$this->done = $config->getBool('wizardstate', false);
		// retrieve wizard config from database
		$this->config = $config->getArray('wizardconfig', []);

		// add default include path
		$this->addIncludePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Steps');
	}

	/**
	 * Class destructor.
	 */
	public function __destruct()
	{
		$config = \VREFactory::getConfig();

		// iterate step
		foreach ($this->steps as $step)
		{
			// register step options
			$this->config[$step->getID()] = $step->getOptions();
		}

		// store wizard state into database
		$config->set('wizardstate', (int) $this->done);
		// store wizard config into database
		$config->set('wizardconfig', $this->config);
	}

	/**
	 * Checks whether the wizard has been dismissed.
	 *
	 * @return  bool  True if dismissed, false otherwise.
	 */
	public function isDone()
	{
		return $this->done;
	}

	/**
	 * Marks the wizard as completed.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function done()
	{
		$this->done = true;

		return $this;
	}

	/**
	 * Restores the wizard after completing it.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function restore()
	{
		$this->done = false;

		// iterate step
		foreach ($this->steps as $step)
		{
			// reset step configuration
			$step->setOptions([]);
		}

		// reset config too
		$this->config = [];
		
		return $this;
	}

	/**
	 * Checks whether the wizard setup has been already invoked.
	 *
	 * @return  bool  True if it is no more possible to setup the wizard.
	 */
	public function isSetup()
	{
		return $this->setup;
	}

	/**
	 * Set up the wizard.
	 *
	 * @param   array  $steps  A list of steps to auto-add.
	 *
	 * @return  bool   False in case the setup was already made.
	 */
	public function setup(array $steps = [])
	{
		if ($this->isSetup())
		{
			// cannot setup more than once
			return false;
		}

		// flag setup to avoid entering here again
		$this->setup = true;

		// retrieve event dispatcher
		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		/**
		 * Trigger event on wizard setup, useful to preload all the needed resources.
		 *
		 * @param   Wizard  $wizard  The wizard instance.
		 *
		 * @return  void
		 *
		 * @since   1.8.2
		 */
		$dispatcher->trigger('onSetupVikRestaurantsWizard', [$this]);

		// iterate include paths
		foreach ($this->getIncludePaths() as $path)
		{
			// check if we have a directory
			if (is_dir($path))
			{
				// scan all files contained within the directory
				$files = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.php');
			}
			else if (is_file($path) && preg_match("/\.php$/", $path))
			{
				// take only the specified file
				$files = array($path);
			}
			else
			{
				$files = array();
			}

			// iterate files one by one
			foreach ($files as $file)
			{
				// require file only once
				require_once $file;
			}
		}

		$app = \JFactory::getApplication();

		// iterate the given steps
		foreach ($steps as $step)
		{
			try
			{
				if (!$step instanceof WizardStep)
				{
					// get rid of the file extension
					$filename = preg_replace("/\.php$/", '', $step);

					// attempt to look for the class from the default namespace
					$classname = 'E4J\\VikRestaurants\\Wizard\\Steps\\' . $filename . 'Step';

					if (!class_exists($classname))
					{
						// class not found, try with the previous notation
						$classname = preg_replace("/[-_]+/", ' ', $filename);
						$classname = preg_replace("/\s+/", '', ucwords($classname));
						$classname = preg_replace("/\s+/", '', $classname);
						$classname = 'VREWizardStep' . $classname;
					}

					// make sure the class exists
					if (!class_exists($classname))
					{
						// throw error
						throw new \Exception(sprintf('Wizard step [%s] not found', $classname), 404);
					}

					// instantiate wizard step
					$step = new $classname();
				}

				// try to add the step
				$this->addStep($step);
			}
			catch (\Exception $e)
			{
				// catch error, enqueue message and go ahead
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}

		/**
		 * Trigger event after completing the wizard setup.
		 * This is useful, in example, to rearrange the registered steps.
		 *
		 * @param   Wizard  $wizard  The wizard instance.
		 *
		 * @return  void
		 *
		 * @since   1.8.2
		 */
		$dispatcher->trigger('onAfterSetupVikRestaurantsWizard', [$this]);
	}

	/**
	 * Registers a new include path in which to search
	 * for the supported wizard steps.
	 *
	 * @param   mixed  $paths  Either an array or the path to include.
	 *
	 * @return  self   This object to support chaining.
	 */
	public function addIncludePath($paths)
	{
		$paths = (array) $paths;

		// iterate paths
		foreach ($paths as $path)
		{
			// make sure the paths hasn't been registered yet
			if (!in_array($path, $this->paths))
			{
				$this->paths[] = $path;
			}
		}

		return $this;
	}

	/**
	 * Returns a list of include paths.
	 *
	 * @return  array  The include paths.
	 */
	public function getIncludePaths()
	{
		return $this->paths;
	}

	/**
	 * Returns a list of steps that haven't been ignored.
	 *
	 * @return  array
	 */
	public function getActiveSteps()
	{
		$steps = [];

		// iterate steps
		foreach ($this->steps as $step)
		{
			// make sure the step is still active
			if (!$step->isIgnored())
			{
				// push step within the list
				$steps[] = $step;
			}
		}

		return $steps;
	}

	/**
	 * Returns the number of registered steps.
	 *
	 * @return  int  The steps count.
	 */
	public function getStepsCount()
	{
		return count($this->steps);
	}

	/**
	 * Returns the step at the specified index.
	 *
	 * @param   int    $index  The index to access.
	 *
	 * @return  mixed  The step at the specified index if exists, null otherwise.
	 */
	public function getStep($index)
	{
		// make sure the index is not out of bounds
		if (preg_match("/^\d+$/", $index) && $index >= 0 && $index < $this->getStepsCount())
		{
			return $this->steps[$index];
		}

		return null;
	}

	/**
	 * Sets the step at the specified index.
	 *
	 * @param   int         $index  The index to access.
	 * @param   WizardStep  $step   The step to add.
	 *
	 * @return  self        This object to support chaining.
	 */
	public function setStep($index, WizardStep $step)
	{
		// make sure the index is not out of bounds (include the array limit for new items)
		if (preg_match("/^\d+$/", $index) && $index >= 0 && $index <= $this->getStepsCount())
		{
			// replace previous step
			$this->steps[$index] = $step;
		}
	}

	/**
	 * Finds the index in which the specified step is stored.
	 *
	 * @param   mixed  $id  Either the step id or the step itself.
	 *
	 * @return  mixed  The index of the step if exists, false otherwise.
	 */
	public function indexOf($id)
	{
		// always use step ID to search
		$id = $id instanceof WizardStep ? $id->getID() : (string) $id;

		foreach ($this->steps as $index => $step)
		{
			// compare IDs
			if ($step->getID() == $id)
			{
				// step found, return current index
				return $index;
			}
		}

		// step not found
		return false;
	}

	/**
	 * Adds a new step within the list.
	 *
	 * @param   WizardStep  $step   The step to add.
	 * @param   mixed       $index  The index in which the step should be stored.
	 *                              If not specified, the step will be always
	 *                              pushed at the end of the list.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function addStep(WizardStep $step, $index = null)
	{
		// check whether the step was already added
		if ($this->indexOf($step) === false)
		{
			$id = $step->getID();

			// fetch step options
			$options = isset($this->config[$id]) ? $this->config[$id] : [];

			// attach options to step
			$step->setOptions($options);

			if ($index === null || $index === false)
			{
				// push step at the end of the list
				$this->steps[] = $step;
			}
			else
			{
				// insert step at the specified position
				array_splice($this->steps, $index, 0, array($step));
			}
		}

		return $this;
	}

	/**
	 * Adds a new step after the specified one.
	 *
	 * @param   WizardStep  $step  The step to add.
	 * @param   mixed       $id    Either the step ID or the step itself.
	 *
	 * @return  self        This object to support chaining.
	 */
	public function addStepAfter(WizardStep $step, $id)
	{
		// find index in which the step is located
		$index = $this->indexOf($id);

		if ($index !== false)
		{
			// increase index by one to add the step
			// one slot after
			$index++;
		}

		// Add step at the specified position.
		// In case the step doesn't exist, it will
		// be added at the end of the queue.
		return $this->addStep($step, $index);
	}

	/**
	 * Adds a new step before the specified one.
	 *
	 * @param   WizardStep  $step  The step to add.
	 * @param   mixed       $id    Either the step ID or the step itself.
	 *
	 * @return  self        This object to support chaining.
	 */
	public function addStepBefore(WizardStep $step, $id)
	{
		// find index in which the step is located
		$index = $this->indexOf($id);

		// Add step at the specified position.
		// In case the step doesn't exist, it will
		// be added at the end of the queue.
		return $this->addStep($step, $index);
	}

	/**
	 * Removes the specified step from the list.
	 *
	 * @param   mixed  $step  Either the step ID, the step index or the step itself.
	 *
	 * @return  bool   True if removed, false otherwise.
	 */
	public function removeStep($step)
	{
		if (preg_match("/^\d+$/", $step))
		{
			// try to directly access the array as index
			$step = $this->getStep($step);
		}
		else
		{
			// lets recover the index with the given argument
			$step = $this->indexOf($step);
		}

		// make sure the step exists
		if (!is_int($step))
		{
			// step not found
			return false;
		}

		// splice array at the index found
		$splice = array_splice($this->steps, $step, 1);

		// extract removed step from list
		$step = array_shift($splice);

		if ($step)
		{
			// get step ID
			$id = $step->getID();

			// iterate registered steps
			foreach ($this->steps as $tmp)
			{
				// try to detach dependency from removed step
				$tmp->removeDependency($id);
			}
		}

		// step removed
		return true;
	}

	/**
	 * Checks whether all the steps of the wizard has been completed.
	 *
	 * @return  bool  True if completed, false otherwise.
	 */
	public function isCompleted()
	{
		// iterate active steps
		foreach ($this->getActiveSteps() as $step)
		{
			// check whether the step has been completed
			if (!$step->isCompleted())
			{
				// step not completed, return false
				return false;
			}
		}

		// all steps have been completed
		return true;
	}

	/**
	 * Calculates the overall progress of the wizard, based
	 * on the active steps.
	 *
	 * @return  int  The percentage progress.
	 */
	public function getProgress()
	{
		// get all active steps
		$steps = $this->getActiveSteps();
		$total = 0;

		if (!$steps)
		{
			// return 100% completion in case of no active steps
			return 100;
		}

		// iterate steps
		foreach ($steps as $step)
		{
			// increase progress total
			$total += $step->getProgress();
		}

		// calculate progress AVG
		return round($total / count($steps));
	}

	/**
	 * Checks if the given item exists.
	 *
	 * @param   mixed  $key  Either the step ID or an index.
	 *
	 * @return  bool   True if exists, false otherwise.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists($key)
	{
		if (preg_match("/^\d+$/", $key))
		{
			// try to directly access the array as index
			return $this->getStep($key) !== null;
		}
		
		// lets recover the index with the given argument
		return $this->indexOf($key) !== false;
	}

	/**
	 * Returns the value for the specified item.
	 *
	 * @param   mixed  $key  Either the step ID or an index.
	 *
	 * @return  mixed  The item value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($key)
	{
		if (!preg_match("/^\d+$/", $key))
		{
			// route step ID to index
			$key = $this->indexOf($key);
		}

		// return the step at the specified index
		return $this->getStep($key);
	}

	/**
	 * Sets the given item.
	 *
	 * @param   mixed       $key    Either the step ID or an index.
	 * @param   WizardStep  $value  The step to add.
	 *
	 * @return  void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet($key, $value)
	{
		if ($key === null)
		{
			// append step at the end of the array
			$this->addStep($value);
		}
		else
		{
			if (!preg_match("/^\d+$/", $key))
			{
				// route step ID to index
				$key = $this->indexOf($key);
			}

			// adds the step at the specified position
			$this->setStep($key, $value);
		}
	}

	/**
	 * Removes the given item.
	 *
	 * @param   mixed  $key  Either the step ID or an index.
	 *
	 * @return  void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset($key)
	{
		// remove step from the list
		$this->removeStep($key);
	}

	/**
	 * Implements an iterator for the registered steps.
	 *
	 * @return  \ArrayIterator
	 */
	#[\ReturnTypeWillChange]
	public function getIterator()
	{
		// return an iterator for the active steps
		return new \ArrayIterator($this->steps);
	}
}
