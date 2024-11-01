<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Config\Pool;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Config\AbstractConfiguration;

/**
 * Concrete class working with a physical configuration stored into a CMS database.
 *
 * @since 1.9
 */
class DatabaseConfiguration extends AbstractConfiguration
{
	/** @var JDatabaseDriver */
	protected $db;

	/**
	 * @inheritDoc
	 */
	public function __construct(array $options = [])
	{
		if (!isset($options['table']))
		{
			$options['table'] = '#__vikrestaurants_config';
		}

		if (!isset($options['key']))
		{
			$options['key'] = 'param';
		}

		if (!isset($options['value']))
		{
			$options['value'] = 'setting';
		}

		if (isset($options['db']))
		{
			$this->db = $db;
			unset($options['db']);	
		}
		else
		{
			$this->db = \JFactory::getDbo();
		}

		parent::__construct($options);
	}

	/**
	 * @inheritDoc
	 */
	protected function retrieve(string $key)
	{
		$q = $this->db->getQuery(true);

		$q->select($this->db->qn($this->options['value']))
			->from($this->db->qn($this->options['table']))
			->where($this->db->qn($this->options['key']) . ' = ' . $this->db->q($key));

		$this->db->setQuery($q, 0, 1);
		$value = $this->db->loadResult();

		return !is_null($value) ? $value : false;
	}

	/**
	 * @inheritDoc
	 */
	protected function register(string $key, $val)
	{
		// get config table
		$configModel = \JModelVRE::getInstance('configuration');

		if (!$configModel)
		{
			// Models not yet loaded...
			// Auto include the default models folder.
			\JModelLegacy::addIncludePath(VREADMIN . DIRECTORY_SEPARATOR . 'models');

			// try again to load the configuration model
			$configModel = \JModelVRE::getInstance('configuration');
		}

		// save configuration setting
		return $configModel->save([
			'param'   => $key,
			'setting' => $val,
		]);
	}
}
