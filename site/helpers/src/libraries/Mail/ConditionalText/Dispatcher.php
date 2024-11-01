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

use E4J\VikRestaurants\Collection\CollectionFilter;
use E4J\VikRestaurants\Collection\Item;
use E4J\VikRestaurants\Collection\ObjectsCollection;
use E4J\VikRestaurants\Mail\Mail;

/**
 * Dispatches the provided conditional texts into the mail objects.
 * 
 * @since 1.9
 */
class Dispatcher
{
	/** @var ConditionalText[] */
	protected $collection;

	/**
	 * Class constructor.
	 * 
	 * @param  ObjectsCollection  $collection  A collection containing conditional text instances.
	 */
	public function __construct(ObjectsCollection $collection = null)
	{
		if (is_null($collection))
		{
			// collection not provided, use the default one
			$this->collection = ConditionalTextsCollection::getInstance();
		}
		else
		{
			// use the provided collection
			$this->collection = $collection;
		}
	}

	/**
	 * Filters the conditional texts collection by obtaining only the eligible ones.
	 * 
	 * @param   string  $templateId  The the template used for the mail.
	 * @param   array   $data        The data wrapped by the mail template.
	 * 
	 * @return  ObjectsCollection
	 */
	public function filter(string $templateId, array $data)
	{
		// filter the collection to only obtain the eligible ones
		return $this->collection->filter(new class ($templateId, $data) implements CollectionFilter {
			/** @var string */
			private $templateId;

			/** @var array */
			private $data;

			/**
			 * Class constructor.
			 */
			public function __construct(string $templateId, array $data)
			{
				$this->templateId = $templateId;
				$this->data       = $data;
			}

			/**
			 * @inheritDoc
			 */
			public function match(Item $item)
			{
				if (!$item instanceof ConditionalText)
				{
					// can handle only objects that inherit the ConditionalText class
					throw new \InvalidArgumentException('ConditionalText item expected, ' . get_class($item) . ' given');
				}

				// iterate all filters to make sure all are eligible
				foreach ($item->getFilters() as $filter)
				{
					// make sure the filter is eligible
					if (!$filter->isEligible($this->templateId, $this->data))
					{
						// filter not eligible
						return false;
					}
				}

				// all filters are eligible
				return true;
			}
		});
	}

	/**
	 * Processes the conditional texts.
	 * 
	 * @param   Mail               $mail        The mail instance where the changes should be applied.
	 * @param   ObjectsCollection  $collection  A list of filtered conditional texts.
	 * 
	 * @return  void
	 */
	public function process(Mail $mail, ObjectsCollection $collection = null)
	{
		if (is_null($collection))
		{
			// use the unfiltered collection
			$collection = $this->collection;
		}

		// iterate items for preflight
		foreach ($collection as $item)
		{
			if (!$item instanceof ConditionalText)
			{
				// can handle only objects that inherit the ConditionalText class
				throw new \InvalidArgumentException('ConditionalText item expected, ' . get_class($item) . ' given');
			}

			// iterate all actions
			foreach ($item->getActions() as $actions)
			{
				// the actions are grouped per alias (body, subject, etc...)
				foreach ($actions as $action)
				{
					// do preflight first
					$action->preflight($mail);
				}
			}
		}

		// iterate items for apply
		foreach ($collection as $item)
		{
			// iterate all actions
			foreach ($item->getActions() as $actions)
			{
				// the actions are grouped per alias (body, subject, etc...)
				foreach ($actions as $action)
				{
					// then apply changes
					$action->apply($mail);
				}
			}
		}

		/**
		 * NOTE: in case the "body" action does not run, the placeholder of the supported positions
		 * won't be removed. Therefore, we need to add a sort of hack here to always make sure that,
		 * in case of no "body" actions, at least one is always executed.
		 * 
		 * In order to prevent this cheat, as other actions in the future might need to clean some
		 * data at the end of the process, we could iterate all the supported actions (even if not
		 * configured) and execute a "postflight" method. As a new instance will be created, the
		 * "postflight" method will never have access to the configuration of the action.
		 */
		$body = Factory::getInstance()->getAction('body');
		// set up positions
		$body->preflight($mail);
		// get rid of the placeholders
		$body->apply($mail);
	}
}
