<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Filters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\ConditionalText\ConditionalTextFilterAware;
use E4J\VikRestaurants\Mail\ConditionalText\Helpers\CountableItemsSummaryTrait;

/**
 * Applies the conditional text only to those restaurant reservations that have
 * been assigned to the selected rooms.
 *
 * @since 1.9
 */
class RoomFilter extends ConditionalTextFilterAware
{
	use CountableItemsSummaryTrait;

	/**
	 * @inheritDoc
	 */
	public function getForm()
	{
		return [
			/**
			 * The rooms list.
			 * 
			 * @var int[]
			 */
			'rooms' => [
				'type'  => 'select',
				'label' => \JText::translate('VRMENUROOMS'),
				'value' => $this->options->get('rooms', []),
				'multiple' => true,
				'options' => \JHtml::fetch('vikrestaurants.rooms'),
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return 'fas fa-home';
	}

	/**
	 * @inheritDoc
	 */
	public function getSummary()
	{
		$rooms = [];

		// create rooms lookup
		foreach (\JHtml::fetch('vikrestaurants.rooms') as $room)
		{
			$rooms[$room->value] = $room->text;
		}

		$items = [];

		foreach ($this->options->get('rooms', []) as $roomId)
		{
			if (!isset($rooms[$roomId]))
			{
				// room no longer available
				continue;
			}

			// register the room name only
			$items[] = $rooms[$roomId];
		}

		/** @see CountableItemsSummaryTrait */
		return $this->createSummary($items);
	}

	/**
	 * @inheritDoc
	 */
	public function isEligible(string $templateId, array $data)
	{
		$order = $data[0] ?? null;

		if (!$order instanceof \VREOrderRestaurant)
		{
			// the provided e-mail template is not observable
			return false;
		}

		// fetch selected rooms
		$rooms = $this->options->get('rooms', []);

		// allow in case the room of the reservation is supported
		return in_array($order->room->id, $rooms);
	}
}
