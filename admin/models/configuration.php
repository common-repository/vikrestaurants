<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants configuration model.
 *
 * @since 1.9
 */
class VikRestaurantsModelConfiguration extends JModelVRE
{
	/**
	 * Hook identifier for triggers.
	 *
	 * @var string
	 */
	protected $hook = 'Config';

	/**
	 * Saves the whole configuration.
	 *
	 * @param   array  $args  An associative array.
	 *
	 * @return  bool   True in case something has changed, false otherwise.
	 */
	public function saveAll(array $args = [])
	{
		// sanitize configuration
		$this->validate($args);

		$dispatcher = VREFactory::getPlatform()->getDispatcher();

		try
		{
			/**
			 * Trigger event to allow the plugins to bind the object that
			 * is going to be saved.
			 *
			 * @param   mixed    &$config  The configuration array.
			 * @param   JModel   $model    The model instance.
			 *
			 * @return  boolean  False to abort saving.
			 *
			 * @throws  Exception  It is possible to throw an exception to abort
			 *                     the saving process and return a readable message.
			 *
			 * @since   1.8.3
			 */
			$result = $dispatcher->filter('onBeforeSave' . $this->hook, [&$args, $this]);

			/** @var E4J\VikRestaurants\Event\EventResponse */

			if ($result->isFalse())
			{
				// abort in case a plugin returned false
				return false;
			}
		}
		catch (Exception $e)
		{
			// register the error thrown by the plugin and abort 
			$this->setError($e);

			return false;
		}

		$db = JFactory::getDbo();

		$changed = false;

		foreach ($args as $param => $setting)
		{
			$q = $db->getQuery(true)
				->update($db->qn('#__vikrestaurants_config'))
				->set($db->qn('setting') . ' = ' . $db->q($setting))
				->where($db->qn('param') . ' = ' . $db->q($param));

			$db->setQuery($q);
			$db->execute();

			$changed = $changed || $db->getAffectedRows();
		}

		/**
		 * Trigger event to allow the plugins to make something after saving
		 * a record in the database.
		 *
		 * @param   array    $args     The configuration array.
		 * @param   boolean  $changed  True in case something has changed.
		 * @param   JModel   $model    The model instance.
		 *
		 * @return  void
		 *
		 * @since   1.8.3
		 */
		$dispatcher->trigger('onAfterSave' . $this->hook, [$args, $changed, $this]);

		/**
		 * Refreshes the administrator manifest whenever the configuration gets saved.
		 * 
		 * @since 1.9
		 */
		(new E4J\VikRestaurants\Document\WebApp(
		    new E4J\VikRestaurants\Document\WebApp\Apps\VikRestaurantsAdminManifest
		))->save();

		return $changed;
	}

	/**
	 * Validates and prepares the settings to be stored.
	 *
	 * @param 	array 	&$args  The configuration associative array.
	 *
	 * @return 	void
	 */
	protected function validate(&$args)
	{
		if (isset($args['senderemail']) && $args['senderemail'] == '')
		{
			// use the e-mail of the current user
			$args['senderemail'] = JFactory::getUser()->email;
		}

		if ($args['hourfrom'] < 0 || $args['hourfrom'] > 23)
		{
			// invalid opening hour
			unset($args['hourfrom']);
		}
		
		if ($args['hourto'] < 1 || $args['hourto'] > 24)
		{
			// invalid closing hour
			unset($args['hourto']);
		}

		if (isset($args['revminlength']))
		{
			// validate reviews min length [0, 256]
			$args['revminlength'] = max(  0, $args['revminlength']);	
			$args['revminlength'] = min(256, $args['revminlength']);	
		}

		if (isset($args['revmaxlength']))
		{
			// validate reviews max length [32, 2048]
			$args['revmaxlength'] = max(  32, $args['revmaxlength']);
			$args['revmaxlength'] = min(2048, $args['revmaxlength']);
		}

		if (isset($args['revminlength']) && isset($args['revmaxlength']) && $args['revminlength'] >= $args['revmaxlength'])
		{
			// the reviews minimum length cannot be equals or higher than the max length
			unset($args['revminlength'], $args['revmaxlength']);
		}
		
		if (isset($args['revlimlist']))
		{
			// validate reviews list limit (must be higher than 0)
			$args['revlimlist'] = max(1, $args['revlimlist']);	
		}

		// stringify closing days
		if (isset($args['closingdays']) && is_array($args['closingdays']))
		{
			$list = $args['closingdays'];
			$args['closingdays'] = [];

			foreach ($list as $day)
			{
				// try to JSON decode
				$json = json_decode($day);

				if ($json)
				{
					// create timestamp from the received date
					$ts = VikRestaurants::createTimestamp($json->date, 0, 0);

					// in case the specified date is in the past and it has no
					// frequency, we can safely ignore the closing day to free
					// some space
					if ($json->freq != 0 || $ts > strtotime('-1 week'))
					{
						// build string to save
						$args['closingdays'][] = $ts . ':' . $json->freq;
					}
				}
			}

			$args['closingdays'] = implode(';;', $args['closingdays']);
		}
	}

	/**
	 * Method to get a table object.
	 *
	 * @param   string  $name     The table name.
	 * @param   string  $prefix   The class prefix.
	 * @param   array   $options  Configuration array for table.
	 *
	 * @return  JTable  A table object.
	 *
	 * @throws  Exception
	 */
	public function getTable($name = '', $prefix = '', $options = array())
	{
		if (!$name)
		{
			// force configuration table
			$name = 'configuration';
		}

		if (!$prefix)
		{
			// use default system prefix
			$prefix = 'VRETable';
		}

		// invoke parent
		return parent::getTable($name, $prefix, $options);
	}
}
