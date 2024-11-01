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

/**
 * Implements the common methods from the manageable conditional text interface.
 *
 * @since 1.9
 */
abstract class ConditionalTextManageableAware implements ConditionalTextManageable
{
	/** @var string */
	protected $id;

	/** @var \JRegistry */
	protected $options;

	/**
	 * Class constructor.
	 * 
	 * @param  array|object  $options  The action/filter configuration.
	 */
	public function __construct($options = [])
	{
		$this->options = new \JRegistry($options);
	}

	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		if (!$this->id)
		{
			$id = get_class($this);

			if (!preg_match("/(\\\\|^)([a-zA-Z0-9_]+)(?:Action|Filter)$/", $id, $match))
			{
				// malformed class name
				throw new \RuntimeException('Unable to extract an ID from the conditional text action/filter', 500);
			}

			// cache ID into a local property
			$this->id = strtolower(end($match));
		}

		return $this->id;
	}

	/**
	 * @inheritDoc
	 */
	public function getName()
	{
		// create name from ID
		return ucwords(preg_replace("/_+/", ' ', $this->getID()));
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		// no description by default
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		// use default icon
		return 'fas fa-plug';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		// ignore summary
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		return $this->options->toArray();
	}
}
