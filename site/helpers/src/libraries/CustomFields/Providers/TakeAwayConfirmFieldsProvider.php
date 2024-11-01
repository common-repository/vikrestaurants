<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Providers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Collection\DatasetProvider;
use E4J\VikRestaurants\Collection\Filters\PropertyFilter;

/**
 * Creates a fields collection to display within the take-away confirmation page.
 * Useful to inject a new field to easily pick a saved address for the provided
 * customer record.
 * 
 * @since 1.9
 */
class TakeAwayConfirmFieldsProvider implements DatasetProvider
{
	/** @var object|null */
	protected $customer;

	/**
	 * Class constructor.
	 * 
	 * @param  object|null  $customer  The customer details.
	 */
	public function __construct($customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		$list = [];

		/**
		 * Retrieve custom fields for the take-away section by using the related helper.
		 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
		 *
		 * @since 1.9
		 */
		$fields = \E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new \E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter);

		// filter the list to obtain the first available field for delivery service
		$deliveryServiceFields = $fields->filter(new \E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new PropertyFilter('service', 'delivery'));

		foreach ($fields as $field)
		{
			// make sure the customer has some locations and we found the first field for delivery service
			if (!empty($this->customer->locations) && count($deliveryServiceFields) && $field === $deliveryServiceFields[0])
			{
				// inject "addresses list" custom field 
				$list[] = \E4J\VikRestaurants\CustomFields\Field::getInstance([
					'name'        => 'user_delivery_address',
					'id'          => 'user-address-sel',
					'type'        => 'select',
					'service'     => 'delivery',
					'hiddenLabel' => true,
					'options'     => array_merge(
						[
							\JHtml::fetch('select.option', '', \JText::translate('VRTKDELIVERYADDRPLACEHOLDER')),
						],
						array_map(function($location)
						{
							// create address from location (exclude country and secondary info)
							$text = \VikRestaurants::deliveryAddressToStr($location, ['country', 'address_2']);
							return \JHtml::fetch('select.option', $location->id, $text);
						}, $this->customer->locations ?? [])
					),
				]);
			}

			if (count($deliveryServiceFields) && $field === $deliveryServiceFields[0])
			{
				// register also a <div> to display validation responses
				$list[] = \E4J\VikRestaurants\CustomFields\Field::getInstance([
					'name'   => 'address-response',
					'type'   => 'html',
					'html'   => '<div class="vrtk-address-response" style="display: none;"></div>',
					'hidden' => true,
				]);
			}

			// copy custom field within the list
			$list[] = $field;
		}

		return $list;
	}
}
