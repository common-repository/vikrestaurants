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
 * VikRestaurants customer model.
 *
 * @since 1.9
 */
class VikRestaurantsModelCustomer extends JModelVRE
{
	/**
	 * The list view pagination object.
	 *
	 * @var JPagination
	 */
	protected $pagination = null;

	/**
	 * The total number of fetched rows.
	 *
	 * @var int
	 */
	protected $total = 0;

	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return  mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		$customer = parent::getItem($pk, $new);

		if (!$customer)
		{
			return null;
		}

		$customer->user      = null;
		$customer->locations = [];

		if ($customer->id)
		{
			// decode custom fields
			$customer->fields   = $customer->fields   ? json_decode($customer->fields, true)   : [];
			$customer->tkfields = $customer->tkfields ? json_decode($customer->tkfields, true) : [];

			// fetch delivery locations only whether we are loading the customer details by ID
			if (is_numeric($pk))
			{
				$db = JFactory::getDbo();

				// load user delivery locations too
				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__vikrestaurants_user_delivery'))
					->where($db->qn('id_user') . ' = ' . $customer->id)
					->order($db->qn('ordering') . ' ASC');
	
				$db->setQuery($query);
				
				foreach ($db->loadObjectList() as $l)
				{
					// get a string representation of the delivery address (exclude country and address notes)
					$l->fullString = VikRestaurants::deliveryAddressToStr($l, ['country', 'address_2']);

					$customer->locations[] = $l;
				}
			}

			if ($customer->jid > 0)
			{
				// fetch CMS user details
				$customer->account = new JUser($customer->jid);
			}
		}

		return $customer;
	}

	/**
	 * Basic save implementation.
	 *
	 * @param   mixed  $data  Either an array or an object of data to save.
	 *
	 * @return  mixed  The ID of the record on success, false otherwise.
	 */
	public function save($data)
	{
		$data = (array) $data;

		if (empty($data['billing_name']) && isset($data['purchaser_nominative']))
		{
			// used the reservation notation, normalize it
			$data['billing_name'] = $data['purchaser_nominative'];
			unset($data['purchaser_nominative']);
		}

		if (empty($data['billing_mail']) && isset($data['purchaser_mail']))
		{
			// used the reservation notation, normalize it
			$data['billing_mail'] = $data['purchaser_mail'];
			unset($data['purchaser_mail']);
		}

		if (empty($data['billing_phone']) && isset($data['purchaser_phone']))
		{
			// used the reservation notation, normalize it
			$data['billing_phone'] = $data['purchaser_phone'];
			unset($data['purchaser_phone']);
		}

		if (empty($data['country_code']) && isset($data['purchaser_country']))
		{
			// used the reservation notation, normalize it
			$data['country_code'] = $data['purchaser_country'];
			unset($data['purchaser_country']);
		}

		$item = null;

		if (empty($data['id']) && !empty($data['jid']))
		{
			// user logged-in, check whether we already have an existing customer
			$item = $this->getItem(['jid' => $data['jid']]);

			if ($item)
			{
				// customer found, do update
				$data['id'] = $item->id;
			}
		}

		if (empty($data['id']) && !empty($data['billing_mail']))
		{
			// search also by e-mail (and the user ID is null)
			$item = $this->getItem(['jid' => 0, 'billing_mail' => $data['billing_mail']]);

			if ($item)
			{
				// customer found by mail, do update
				$data['id'] = $item->id;
			}
		}

		$saveData = $data;

		/**
		 * When the $item variable filled in, we are probably updating an existing
		 * customer from the front-end, during the booking process. If this is the
		 * case, we should prevent the system from updating the billing details with
		 * the information provided through the custom fields.
		 * 
		 * @since 1.9
		 */
		if ($item)
		{
			// do not update name or e-mail
			unset($saveData['billing_name'], $saveData['billing_mail']);

			if ($item->billing_phone)
			{
				// do not update billing phone if already specified
				unset($saveData['billing_phone']);
			}

			// update the billing address information only in
			// case we are saving a company name or a vat number
			if ($item->billing_address && !isset($data['company']) && !isset($data['vatnum']))
			{
				unset($saveData['country_code']);
				unset($saveData['billing_state']);
				unset($saveData['billing_city']);
				unset($saveData['billing_address']);
				unset($saveData['billing_address_2']);
				unset($saveData['billing_zip']);
			}
		}

		// attempt to save the record
		$id = parent::save($saveData);

		if (!$id)
		{
			return false;
		}

		// check if we should auto-create a delivery location based on billing details
		if (!empty($data['delivery_as_billing']))
		{
			// in case of missing fields, the model will prevent the location insert
			JModelVRE::getInstance('userlocation')->save([
				'country'   => $data['country_code'] ?? '',
				'state'     => $data['billing_state'] ?? '',
				'city'      => $data['billing_city'] ?? '',
				'address'   => $data['billing_address'] ?? '',
				'address_2' => $data['billing_address_2'] ?? '',
				'zip'       => $data['billing_zip'] ?? '',
				'latitude'  => $data['latitude'] ?? 0,
				'longitude' => $data['longitude'] ?? 0,
				'id_user'   => $id,
				'ordering'  => 1,
			]);
		}

		if (isset($data['locations']))
		{
			$model = JModelVRE::getInstance('userlocation');

			// iterate all the provided delivery locations
			foreach ($data['locations'] as $i => $location)
			{
				if (is_string($location))
				{
					// JSON given, decode it
					$location = json_decode($location, true);
				}

				// update ordering
				$location['ordering'] = $i + 1;
				// attach location to this customer
				$location['id_user'] = $id;

				// save location
				$model->save($location);
			}
		}

		return $id;
	}

	/**
	 * Extend delete implementation to delete any related records
	 * stored within a separated table.
	 *
	 * @param   mixed    $ids  Either the record ID or a list of records.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function delete($ids)
	{
		// only int values are accepted
		$ids = array_map('intval', (array) $ids);

		// invoke parent first
		if (!parent::delete($ids))
		{
			// nothing to delete
			return false;
		}

		$db = JFactory::getDbo();

		// load any assigned delivery locations
		$q = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_user_delivery'))
			->where($db->qn('id_user') . ' IN (' . implode(',', $ids) . ')' );

		$db->setQuery($q);

		if ($locations = $db->loadColumn())
		{
			// delete all the delivery locations that belong to the removed customers
			JModelVRE::getInstance('userlocation')->delete($locations);
		}

		return true;
	}

	/**
	 * Checks whether the system owns a customer with matching e-mail 
	 * address and or phone number.
	 * 
	 * @param   array  $search  An array containing the search terms.
	 *                          - purchaser_mail
	 *                          - purchaser_phone
	 * 
	 * @return  int    The ID of the customer if exists, 0 otherwise.
	 */
	public function hasCustomer(array $search)
	{
		$db = JFactory::getDbo();

		$where = [];

		// search customer by e-mail address
		if (!empty($search['purchaser_mail']))
		{
			$where[] = $db->qn('billing_mail') . ' = ' . $db->q($search['purchaser_mail']);
		}

		// search customer by phone number
		if (!empty($search['purchaser_phone']))
		{
			$where[] = sprintf(
				'REPLACE(%s, \' \', \'\') = %s',             
				$db->qn('billing_phone'),
				$db->q(preg_replace("/\s+/", '', $search['purchaser_phone']))
			);
		}

		if (!$where)
		{
			// not enough data to make a search
			return 0;
		}

		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__vikrestaurants_users'))
			->where($where, 'OR');

		$db->setQuery($query, 0, 1);
		return (int) $db->loadResult();
	}

	/**
	 * Searches for the CMS users that match the specified query.
	 * It is possible to search the users by name, username and
	 * e-mail address.
	 *
	 * @param 	string  $search   The search string.
	 * @param 	mixed   $id       The user ID. When specified, the system will
	 *                            fetch also the user status, to check if the
	 *                            user has been assigned to another user.
	 * @param 	array 	$options  An array of options:
	 *                            - start  int|null  the query offset;
	 *                            - limit  int|null  the query limit;
	 *
	 * @return 	array   A list of matching users.
	 */
	public function searchUsers($search = '', $id = null, array $options = [])
	{
		// always reset pagination and total count
		$this->pagination = null;
		$this->total      = 0;

		$options = new JRegistry($options);

		$db = JFactory::getDbo();

		$q = $db->getQuery(true);

		$q->select($db->qn('u.id'));
		$q->select($db->qn('u.name'));
		$q->select($db->qn('u.email'));
		$q->select($db->qn('u.username'));

		if (!is_null($id))
		{
			// create inner query to fetch enabled/disabled status
			$inner = $db->getQuery(true)
				->select(1)
				->from($db->qn('#__vikrestaurants_users', 'a'))
				->where($db->qn('a.jid') . ' = ' . $db->qn('u.id'));

			$q->select('(' . $db->qn('id') . ' <> ' . (int) $id . ' AND EXISTS (' . $inner . ')) AS ' . $db->qn('disabled'));
		}

		$q->from($db->qn('#__users', 'u'));
			
		if ($search)
		{
			/**
			 * Reverse the search key in order to try finding
			 * users by name even if it was wrote in the opposite way.
			 * If we search by "John Smith", the system will search
			 * for "Smith John" too.
			 *
			 * @since 1.8
			 */
			$reverse = preg_split("/\s+/", $search);
			$reverse = array_reverse($reverse);
			$reverse = implode(' ', $reverse);

			$q->where(array(
				$db->qn('u.name') . ' LIKE ' . $db->q("%$search%"),
				$db->qn('u.name') . ' LIKE ' . $db->q("%$reverse%"),
				$db->qn('u.username') . ' LIKE ' . $db->q("%$search%"),
				$db->qn('u.email') . ' LIKE ' . $db->q("%$search%"),
			), 'OR');
		}

		$q->order($db->qn('u.name') . ' ASC');
		$q->order($db->qn('u.username') . ' ASC');

		/**
		 * Fetch list limit.
		 *
		 * @since 1.9
		 */
		$start = $options->get('start', 0);
		$limit = $options->get('limit', null);

		$db->setQuery($q, $start, $limit);
		$rows = $db->loadObjectList();

		if (!$rows)
		{
			// no users found
			return [];
		}

		$users = [];

		/**
		 * Reverse lookup used to check whether there is already
		 * a user with the same name.
		 * 
		 * @since 1.8
		 */
		$namesakesLookup = array();

		foreach ($rows as $u)
		{
			$u->text = $u->name;

			$users[$u->id] = $u;

			// insert name-id relation within the lookup
			if (!isset($namesakesLookup[$u->name]))
			{
				$namesakesLookup[$u->name] = array();
			}

			$namesakesLookup[$u->name][] = $u->id;
		}

		// iterate names lookup
		foreach ($namesakesLookup as $name => $ids)
		{
			// in case a name owns more than 1 ID, we have a homonym
			if (count($ids) > 1)
			{
				// iterate the list of IDS and append the e-mail to the name
				foreach ($ids as $id)
				{
					$users[$id]->text .= ' : ' . $users[$id]->username;
				}
			}
		}

		return $users;
	}

	/**
	 * Searches for the customers that match the specified query.
	 * It is possible to search the users by name, e-mail and
	 * phone number.
	 *
	 * @param 	string  $search   The search string.
	 * @param 	array 	$options  An array of options:
	 *                            - start        int|null  the query offset;
	 *                            - limit        int|null  the query limit;
	 *
	 * @return 	array   A list of matching customers.
	 */
	public function search($search = '', array $options = [])
	{
		// always reset pagination and total count
		$this->pagination = null;
		$this->total      = 0;

		$options = new JRegistry($options);

		$db = JFactory::getDbo();

		// fetch list based on search key
		$q = $db->getQuery(true);

		$q->select('SQL_CALC_FOUND_ROWS c.id');
		$q->select($db->qn('c.billing_name'));
		$q->select($db->qn('c.billing_mail'));
		$q->select($db->qn('c.billing_phone'));
		$q->select($db->qn('c.country_code'));
		$q->select($db->qn('c.billing_state'));
		$q->select($db->qn('c.billing_city'));
		$q->select($db->qn('c.billing_address'));
		$q->select($db->qn('c.billing_zip'));
		$q->select($db->qn('c.company'));
		$q->select($db->qn('c.vatnum'));
		$q->select($db->qn('c.jid'));
		$q->select($db->qn('c.image'));
		$q->select($db->qn('c.fields'));
		$q->select($db->qn('c.tkfields'));

		$q->select($db->qn('d.id', 'id_delivery'));
		$q->select($db->qn('d.country', 'delivery_country'));
		$q->select($db->qn('d.state'));
		$q->select($db->qn('d.city'));
		$q->select($db->qn('d.address'));
		$q->select($db->qn('d.address_2'));
		$q->select($db->qn('d.zip'));
		$q->select($db->qn('d.latitude'));
		$q->select($db->qn('d.longitude'));

		$q->from($db->qn('#__vikrestaurants_users', 'c'));
		$q->leftjoin($db->qn('#__vikrestaurants_user_delivery', 'd') . ' ON ' . $db->qn('c.id') . ' = ' . $db->qn('d.id_user'));

		$q->where(1);

		if ($search)
		{
			$where = [];

			$where[] = $db->qn('c.billing_name') . ' LIKE ' . $db->q("%$search%");

			/**
			 * Reverse the search key in order to try finding
			 * users by name even if it was wrote in the opposite way.
			 * If we searched by "John Smith", the system will search
			 * for "Smith John" too.
			 *
			 * @since 1.8
			 */
			$reverse = preg_split("/\s+/", $search);
			$reverse = array_reverse($reverse);
			$reverse = implode(' ', $reverse);

			$where[] = $db->qn('c.billing_name') . ' LIKE ' . $db->q("%$reverse%");

			// search by e-mail
			$where[] = $db->qn('c.billing_mail') . ' LIKE ' . $db->q("%$search%");

			/**
			 * Get rid of any white spaces to improve the search by phone number.
			 * 
			 * @since 1.9
			 */
			$where[] = sprintf(
				'REPLACE(%s, \' \', \'\') LIKE %s',             
				$db->qn('c.billing_phone'),
				$db->q('%' . preg_replace("/\s+/", '', $search) . '%')
			);

			$q->andWhere($where, 'OR');
		}

		$q->order($db->qn('c.billing_name') . ' ASC');
		$q->order($db->qn('c.billing_mail') . ' ASC');

		/**
		 * Fetch list limit.
		 *
		 * @since 1.9
		 */
		$start = $options->get('start', 0);
		$limit = $options->get('limit', null);

		$db->setQuery($q, $start, $limit);
		$rows = $db->loadObjectList();

		if (!$rows)
		{
			// no customers found
			return [];
		}

		$users = [];

		/**
		 * Reverse lookup used to check whether there is already
		 * a user with the same name.
		 * 
		 * @since 1.8
		 */
		$namesakesLookup = [];

		foreach ($rows as $u)
		{
			if (!isset($users[$u->id]))
			{
				$tmp = new stdClass;
				$tmp->id              = $u->id;
				$tmp->text            = $u->billing_name;
				$tmp->billing_name    = $u->billing_name;
				$tmp->billing_mail    = $u->billing_mail;
				$tmp->billing_phone   = $u->billing_phone;
				$tmp->country_code    = $u->country_code;
				$tmp->billing_state   = $u->billing_state;
				$tmp->billing_city    = $u->billing_city;
				$tmp->billing_address = $u->billing_address;
				$tmp->billing_zip     = $u->billing_zip;
				$tmp->company         = $u->company;
				$tmp->vatnum          = $u->vatnum;
				$tmp->jid             = $u->jid;
				$tmp->image           = $u->image;
				$tmp->fields          = $u->fields   ? json_decode($u->fields)   : [];
				$tmp->tkfields        = $u->tkfields ? json_decode($u->tkfields) : [];
				$tmp->locations       = [];

				$users[$u->id] = $tmp;

				// insert name-id relation within the lookup
				if (!isset($namesakesLookup[$u->billing_name]))
				{
					$namesakesLookup[$u->billing_name] = [];
				}

				$namesakesLookup[$u->billing_name][] = $u->id;
			}

			if (!empty($u->address) && !empty($u->zip))
			{
				$addr = new stdClass;
				$addr->country     = $u->delivery_country;
				$addr->state       = $u->state;
				$addr->city        = $u->city;
				$addr->address     = $u->address;
				$addr->address_2   = $u->address_2;
				$addr->zip         = $u->zip;
				$addr->latitude    = $u->latitude;
				$addr->longitude   = $u->longitude;

				// get a string representation of the delivery address (exclude country and address notes)
				$addr->fullString = VikRestaurants::deliveryAddressToStr($u, ['country', 'address_2']);

				$users[$u->id]->locations[$u->id_delivery] = $addr;
			}
		}

		// iterate names lookup
		foreach ($namesakesLookup as $name => $ids)
		{
			// in case a name owns more than 1 ID, we have a homonym
			if (count($ids) > 1)
			{
				// iterate the list of IDS and append the e-mail to the name
				foreach ($ids as $id)
				{
					$users[$id]->text .= ' : ' . $users[$id]->billing_mail;
				}
			}
		}

		return $users;
	}

	/**
	 * Returns the list pagination.
	 *
	 * @param   array  $filters  An array of filters.
	 * @param   array  $options  An array of options.
	 *
	 * @return  JPagination
	 */
	public function getPagination(array $filters = [], array $options = [])
	{
		if (!$this->pagination)
		{
			jimport('joomla.html.pagination');
			$db = JFactory::getDbo();
			$db->setQuery('SELECT FOUND_ROWS();');
			$this->total = (int) $db->loadResult();

			$this->pagination = new JPagination($this->total, $options['start'], $options['limit']);

			foreach ($filters as $k => $v)
			{
				// append only filters that own a value as it doesn't
				// make sense to populate the URL using empty variables
				if ($v)
				{
					$this->pagination->setAdditionalUrlParam($k, $v);
				}
			}
		}

		return $this->pagination;
	}

	/**
	 * Returns the total number of records matching the search query.
	 *
	 * @return 	int
	 */
	public function getTotal()
	{
		return $this->total;
	}
}
