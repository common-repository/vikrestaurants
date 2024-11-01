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

VRELoader::import('library.order.wrapper');

/**
 * Take-Away order class wrapper.
 *
 * @since 1.8
 */
class VREOrderTakeaway extends VREOrderWrapper
{
	/**
	 * @override
	 * Returns the take-away order object.
	 *
	 * @param 	integer  $id       The order ID.
	 * @param 	mixed    $langtag  The language tag. If null, the default one will be used.
	 * @param 	array 	 $options  An array of options to be passed to the order instance.
	 *
	 * @return 	mixed    The array/object to load.
	 *
	 * @throws 	Exception
	 */
	protected function load($id, $langtag = null, array $options = array())
	{
		$dbo        = JFactory::getDbo();
		$config     = VREFactory::getConfig();
		$dispatcher = VREFactory::getEventDispatcher();

		// create query
		$q = $dbo->getQuery(true);

		// select all order columns
		$q->select('r.*');
		$q->from($dbo->qn('#__vikrestaurants_takeaway_reservation', 'r'));

		// select payment name
		$q->select($dbo->qn('gp.name', 'payment_name'));
		$q->select($dbo->qn('gp.file', 'payment_file'));
		$q->select($dbo->qn('gp.note', 'payment_note'));
		$q->select($dbo->qn('gp.prenote', 'payment_prenote'));
		$q->select($dbo->qn('gp.icontype', 'payment_icontype'));
		$q->select($dbo->qn('gp.icon', 'payment_icon'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_gpayments', 'gp') . ' ON ' . $dbo->qn('r.id_payment') . ' = ' . $dbo->qn('gp.id'));

		// select status code
		$q->select($dbo->qn('rc.code', 'status_code'));
		$q->select($dbo->qn('rc.icon', 'code_icon'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_res_code', 'rc') . ' ON ' . $dbo->qn('r.rescode') . ' = ' . $dbo->qn('rc.id'));

		// select order items
		$q->select($dbo->qn('i.id', 'item_id'));
		$q->select($dbo->qn('i.id_product', 'item_id_product'));
		$q->select($dbo->qn('i.id_product_option', 'item_id_option'));
		$q->select($dbo->qn('p.name', 'item_name'));
		$q->select($dbo->qn('o.name', 'item_option_name'));
		$q->select($dbo->qn('i.quantity', 'item_quantity'));
		$q->select($dbo->qn('i.price', 'item_price'));
		$q->select($dbo->qn('i.net', 'item_net'));
		$q->select($dbo->qn('i.tax', 'item_tax'));
		$q->select($dbo->qn('i.gross', 'item_gross'));
		$q->select($dbo->qn('i.discount', 'item_discount'));
		$q->select($dbo->qn('i.tax_breakdown', 'item_tax_breakdown'));
		$q->select($dbo->qn('i.notes', 'item_notes'));
		$q->select($dbo->qn('p.ready', 'item_ready'));
		$q->select($dbo->qn('m.id', 'menu_id'));
		$q->select($dbo->qn('m.title', 'menu_title'));

		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_res_prod_assoc', 'i') . ' ON ' . $dbo->qn('i.id_res') . ' = ' . $dbo->qn('r.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry', 'p') . ' ON ' . $dbo->qn('i.id_product') . ' = ' . $dbo->qn('p.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $dbo->qn('i.id_product_option') . ' = ' . $dbo->qn('o.id'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $dbo->qn('p.id_takeaway_menu') . ' = ' . $dbo->qn('m.id'));

		// filter records by ID
		$q->where($dbo->qn('r.id') . ' = ' . (int) $id);

		// filter by order key, if specified
		if (isset($options['sid']))
		{
			$q->where($dbo->qn('r.sid') . ' = ' . $dbo->q($options['sid']));
		}

		// filter by confirmation key, if specified
		if (isset($options['conf_key']))
		{
			$q->where($dbo->qn('r.conf_key') . ' = ' . $dbo->q($options['conf_key']));
		}

		/**
		 * Always sort the records according to the default 
		 * system ordering: menu > product > variation.
		 *
		 * @since 1.8.4
		 */
		$q->order($dbo->qn('m.ordering') . ' ASC');
		$q->order($dbo->qn('p.ordering') . ' ASC');
		$q->order($dbo->qn('o.ordering') . ' ASC');

		/**
		 * External plugins can attach to this hook in order to manipulate
		 * the query at runtime, in example to alter the default ordering.
		 *
		 * @param 	mixed    &$query   A query builder instance.
		 * @param 	integer  $id       The ID of the order.
		 * @param 	mixed    $langtag  The language tag. If null, the default one will be used.
		 * @param 	array 	 $options  An array of options to be passed to the order instance.
		 *
		 * @return 	void
		 *
		 * @since 	1.8.4
		 */
		$dispatcher->trigger('onLoadTakeAwayOrderDetails', array(&$q, $id, $langtag, $options));

		$dbo->setQuery($q);
		$list = $dbo->loadObjectList();

		if (!$list)
		{
			// order not found raise error
			throw new Exception(sprintf('Order [%d] not found', $id), 404);
		}

		$order = $list[0];
		$order->items = array();

		// fetch coupon
		if ($order->coupon_str)
		{
			list($code, $amount, $type) = explode(';;', $order->coupon_str);

			$order->coupon = new stdClass;
			$order->coupon->code   = $code;
			$order->coupon->amount = $amount;
			$order->coupon->type   = $type;
		}
		else
		{
			$order->coupon = null;
		}

		/**
		 * Retrieve custom fields for the takeaway section by using the related helper.
		 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter)
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

		// decode stored CF data
		$order->fields = $order->custom_f ? (array) json_decode($order->custom_f, true) : [];

		// Translate CF data object into the current language.
		// Do not override the $order->fields property directly, otherwise the `translate` method would
		// apply the correct i18n to an object already translated.
		$translatedFields = VRCustomFields::translateObject($order->fields, $customFields);

		$order->displayFields = [];

		foreach ($customFields as $field)
		{
			$k = $field->getName();

			if (!array_key_exists($k, $translatedFields))
			{
				// field not found inside the given object, go to next one
				continue;
			}

			$v = $translatedFields[$k];

			// take only if the value is not empty
			if ((is_scalar($v) && strlen((string) $v)) || !empty($v))
			{
				// get a more readable label/text of the saved value
				$order->displayFields[$field->langname] = $v;
			}
		}

		$vars = array_values($order->fields);
		$vars = array_filter($vars, function($elem)
		{
			if (is_array($elem))
			{
				return (bool) $elem;
			}

			return strlen($elem);
		});
		
		$order->hasFields = (bool) $vars;

		// format check-in
		$order->checkin_iso8601 = JFactory::getDate(date('Y-m-d H:i:s', $order->checkin_ts), date_default_timezone_get())->toISO8601();
		$order->checkin_lc1 = JHtml::fetch('date', $order->checkin_ts, JText::translate('DATE_FORMAT_LC1') . ' ' . $config->get('timeformat'), date_default_timezone_get());
		$order->checkin_lc3 = JHtml::fetch('date', $order->checkin_ts, JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'), date_default_timezone_get());

		// decode route details
		if ($order->route)
		{
			$order->route = (object) json_decode($order->route);
		}

		// count the total number of items that don't require a preparation
		$order->itemsReady = 0;
		// count the total number of items that have to be cooked
		$order->itemsToBeCooked = 0;

		// fetch take-away order products
		foreach ($list as $row)
		{
			if ($row->item_id)
			{
				$item = new stdClass;
				$item->id            = $row->item_id;
				$item->id_product    = $row->item_id_product;
				$item->id_option     = $row->item_id_option;
				$item->name          = $row->item_name . ($item->id_option > 0 ? ' - ' . $row->item_option_name : '');
				$item->productName   = $row->item_name;
				$item->optionName    = $row->item_option_name;
				$item->quantity      = $row->item_quantity;
				$item->price         = $row->item_price;
				$item->net           = $row->item_net;
				$item->tax           = $row->item_tax;
				$item->gross         = $row->item_gross;
				$item->discount      = $row->item_discount;
				$item->tax_breakdown = json_decode($row->item_tax_breakdown ?? '');
				$item->notes         = $row->item_notes;
				$item->ready         = $row->item_ready;
				$item->toppings      = [];

				$order->items[$item->id] = $item;

				if ($item->ready)
				{
					// icrease count of ready items
					$order->itemsReady += $item->quantity;
				}
				else
				{
					// increase count of non-ready items
					$order->itemsToBeCooked += $item->quantity;
				}

				// include menu details
				$item->menu = new stdClass;
				$item->menu->id    = $row->menu_id;
				$item->menu->title = $row->menu_title;

				// recover item toppings
				$q = $dbo->getQuery(true)
					->select($dbo->qn('a.id_group'))
					->select($dbo->qn('a.id_topping'))
					->select($dbo->qn('a.units'))
					->select($dbo->qn('g.title', 'group_title'))
					->select($dbo->qn('t.name', 'topping_name'))
					->from($dbo->qn('#__vikrestaurants_takeaway_res_prod_topping_assoc', 'a'))
					->leftjoin($dbo->qn('#__vikrestaurants_takeaway_entry_group_assoc', 'g') . ' ON ' . $dbo->qn('a.id_group') . ' = ' . $dbo->qn('g.id'))
					->leftjoin($dbo->qn('#__vikrestaurants_takeaway_topping', 't') . ' ON ' . $dbo->qn('a.id_topping') . ' = ' . $dbo->qn('t.id'))
					->where($dbo->qn('a.id_assoc') . ' = ' . $item->id);

				$dbo->setQuery($q);
				
				foreach ($dbo->loadObjectList() as $t)
				{
					if (!isset($item->toppings[$t->id_group]))
					{
						$group = new stdClass;
						$group->id    = $t->id_group;
						$group->title = $t->group_title;
						$group->str   = '';
						$group->list  = [];

						$item->toppings[$t->id_group] = $group;
					}

					if (!empty($t->id_topping))
					{
						$topping = new stdClass;
						$topping->id    = $t->id_topping;
						$topping->name  = $t->topping_name;
						$topping->units = $t->units;

						/**
						 * Concatenate units to topping name in case that
						 * value is higher than 1.
						 *
						 * @since 1.8.2
						 */
						if ($topping->units > 1)
						{
							$topping->name .= ' x' . $topping->units;
						}

						$item->toppings[$t->id_group]->list[] = $topping;

						// append topping name to group "str" property
						if ($item->toppings[$t->id_group]->str)
						{
							$item->toppings[$t->id_group]->str .= ', ';
						}
						
						$item->toppings[$t->id_group]->str .= $topping->name;
					}
				}
			}
		}

		// count the total number of ordered items
		$order->itemsCount = $order->itemsReady + $order->itemsToBeCooked;

		// fetch payment data
		if ($order->payment_file)
		{
			$order->payment = new stdClass;
			$order->payment->id       = $order->id_payment;
			$order->payment->name     = $order->payment_name;
			$order->payment->driver   = $order->payment_file;
			$order->payment->iconType = $order->payment_icontype;
			$order->payment->icon     = $order->payment_icon;

			if ($order->payment->iconType == 1)
			{
				// Font Icon
				$order->payment->fontIcon = $order->payment->icon;
			}
			else
			{
				// Image Icon
				$order->payment->iconURI = JUri::root() . $order->payment->icon;

				// fetch Font Icon based on payment driver

				switch ($order->payment->driver)
				{
					case 'bank_transfer.php':
						$order->payment->fontIcon = 'fas fa-money-bill';
						break;

					case 'paypal.php':
						$order->payment->fontIcon = 'fab fa-paypal';
						break;

					default:
						$order->payment->fontIcon = 'fas fa-credit-card';
				}
			}

			$order->payment->notes = new stdClass;
			$order->payment->notes->beforePurchase = $order->payment_prenote;
			$order->payment->notes->afterPurchase  = $order->payment_note;
		}
		else
		{
			$order->payment = null;
		}

		/**
		 * Fetch status role depending on the selected status.
		 * 
		 * @since 1.9
		 */
		$order->statusRole = JHtml::fetch('vrehtml.status.role', 'takeaway', $order->status);

		// fetch paid flag based on current order status
		$order->paid = JHtml::fetch('vrehtml.status.ispaid', 'takeaway', $order->status);

		if ($order->paid)
		{
			// set as fully paid
			$order->tot_paid = max($order->total_to_pay, $order->tot_paid);
		}

		// calculate total due
		$order->total_due = max(0, $order->total_to_pay - $order->tot_paid);

		/**
		 * External plugins can use this event to manipulate the object holding
		 * the details of the take-away order. Useful to inject all the additional
		 * data fetched with the manipulation of the query.
		 *
		 * @param 	mixed  &$order  The order details object.
		 * @param 	array  $list    The query resulting array.
		 *
		 * @return 	void
		 *
		 * @since 	1.8.4
		 */
		$dispatcher->trigger('onSetupTakeAwayOrderDetails', array(&$order, $list));

		return $order;
	}

	/**
	 * @override
	 * Translates the internal properties.
	 *
	 * @param 	mixed    $langtag  The language tag. If null, the default one will be used.
	 *
	 * @return 	void
	 */
	protected function translate($langtag = null)
	{
		$dispatcher = VREFactory::getEventDispatcher();

		if (!$langtag)
		{
			// use order lang tag in case it was not specified
			$langtag = $this->get('langtag', null);

			if (!$langtag)
			{
				// the order is not assigned to any lang tag, use the current one
				$langtag = JFactory::getLanguage()->getTag();
			}
		}

		// get translator
		$translator = VREFactory::getTranslator();

		$menu_ids    = array();
		$product_ids = array();
		$option_ids  = array();
		$group_ids   = array();
		$topping_ids = array();

		foreach ($this->items as $item)
		{
			$menu_ids[] = $item->menu->id;

			$product_ids[] = $item->id_product;

			if ($item->id_option)
			{
				$option_ids[] = $item->id_option;
			}

			foreach ($item->toppings as $group)
			{
				$group_ids[] = $group->id;

				foreach ($group->list as $topping)
				{
					$topping_ids[] = $topping->id;
				}
			}
		}

		// pre-load menus translations
		$menuLang = $translator->load('tkmenu', array_unique($menu_ids), $langtag);
		// pre-load products translations
		$prodLang = $translator->load('tkentry', array_unique($product_ids), $langtag);
		// pre-load products options translations
		$optLang = $translator->load('tkentryoption', array_unique($option_ids), $langtag);
		// pre-load toppings groups translations
		$grpLang = $translator->load('tkentrygroup', array_unique($group_ids), $langtag);
		// pre-load toppings translations
		$topLang = $translator->load('tktopping', array_unique($topping_ids), $langtag);

		// iterate items and apply translationss
		foreach ($this->items as &$item)
		{
			// translate menu name for the given language
			$menu_tx = $menuLang->getTranslation($item->menu->id, $langtag);

			if ($menu_tx)
			{
				// inject translation within order item
				$item->menu->title = $menu_tx->title;
			}

			// translate product name for the given language
			$prod_tx = $prodLang->getTranslation($item->id_product, $langtag);

			if ($prod_tx)
			{
				// inject translation within order item
				$item->productName = $prod_tx->name;
			}

			if ($item->id_option)
			{
				// translate product option name for the given language
				$opt_tx = $optLang->getTranslation($item->id_option, $langtag);

				if ($opt_tx)
				{
					// inject translation within order item
					$item->optionName = $opt_tx->name;
				}
			}

			// complete traslation of full name
			$item->name = $item->productName . ($item->id_option > 0 ? ' - ' . $item->optionName : '');

			foreach ($item->toppings as &$toppingGroup)
			{
				// translate group name for the given language
				$grp_tx = $grpLang->getTranslation($toppingGroup->id, $langtag);

				if ($grp_tx)
				{
					// inject translation within order item
					$toppingGroup->title = $grp_tx->title;
				}

				// reset toppings string
				$toppingGroup->str = array();

				// iterate toppings
				foreach ($toppingGroup->list as &$toppingElem)
				{
					// translate topping name for the given language
					$top_tx = $topLang->getTranslation($toppingElem->id, $langtag);

					if ($top_tx)
					{
						// inject translation within order item
						$toppingElem->name = $top_tx->name;

						/**
						 * Concatenate units to topping name in case that
						 * value is higher than 1.
						 *
						 * @since 1.8.2
						 */
						if ($toppingElem->units > 1)
						{
							$toppingElem->name .= ' x' . $toppingElem->units;
						}
					}

					// inject translated topping within the list
					$toppingGroup->str[] = $toppingElem->name;
				}

				// stringify toppings list
				$toppingGroup->str = implode(', ', $toppingGroup->str);
			}
		}

		// translate payment if specified
		if ($this->id_payment)
		{
			// get payment translation
			$pay_tx = $translator->translate('payment', $this->id_payment, $langtag);

			if ($pay_tx)
			{
				// inject translation within order details
				$this->payment->name                  = $pay_tx->name;
				$this->payment->notes->beforePurchase = $pay_tx->prenote;
				$this->payment->notes->afterPurchase  = $pay_tx->note;
			}
		}

		/**
		 * Retrieve custom fields for the takeaway section by using the related helper.
		 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
		 *
		 * @since 1.9
		 */
		$customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
			->filter(new E4J\VikRestaurants\CustomFields\Filters\TakeAwayGroupFilter)
			->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
			->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));
		
		// translate CF data object into the provided language
		$this->fields = VRCustomFields::translateObject($this->fields, $customFields, $langtag);

		// reset display fields
		$this->displayFields = [];

		foreach ($customFields as $field)
		{
			$k = $field->getName();

			if (!array_key_exists($k, $this->fields))
			{
				// field not found inside the given object, go to next one
				continue;
			}

			$v = $this->fields[$k];

			// take only if the value is not empty
			if ((is_scalar($v) && strlen((string) $v)) || !empty($v))
			{
				// get a more readable label/text of the saved value
				$this->displayFields[$field->langname] = $v;
			}
		}

		/**
		 * External plugins can use this event to apply the translations to
		 * additional details manually included within the order object.
		 *
		 * @param 	mixed   $order    The order details object.
		 * @param   string  $langtag  The requested language tag.
		 *
		 * @return 	void
		 *
		 * @since 	1.8.4
		 */
		$dispatcher->trigger('onTranslateTakeAwayOrderDetails', array($this, $langtag));
	}

	/**
	 * @override
	 * Returns the billing details of the user that made the order.
	 *
	 * @return 	object
	 */
	protected function getBilling()
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select('*')
			->from($dbo->qn('#__vikrestaurants_users'))
			->where($dbo->qn('id') . ' = ' . (int) $this->id_user)
			->orWhere(array(
				$dbo->qn('billing_mail') . ' <> ' . $dbo->q(''),
				$dbo->qn('billing_mail') . ' IS NOT NULL',
				$dbo->qn('billing_mail') . ' = ' . $dbo->q($this->purchaser_mail),
			), 'AND');

		$dbo->setQuery($q, 0, 1);
		return $dbo->loadObject() ?: false;
	}

	/**
	 * @override
	 * Returns the account details of the order author.
	 *
	 * @return 	object
	 */
	protected function getAuthor()
	{
		if ($this->created_by <= 0)
		{
			// no registered author, do not go ahead
			return false;
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('name'))
			->select($dbo->qn('username'))
			->select($dbo->qn('email'))
			->from($dbo->qn('#__users'))
			->where($dbo->qn('id') . ' = ' . (int) $this->created_by);

		$dbo->setQuery($q, 0, 1);
		return $dbo->loadObject() ?: false;
	}

	/**
	 * @override
	 * Returns the invoice details of the order.
	 *
	 * @return 	mixed   The invoice object is exists, false otherwise.
	 */
	protected function getInvoice()
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('id'))
			->select($dbo->qn('inv_number', 'number'))
			->select($dbo->qn('inv_date', 'date'))
			->select($dbo->qn('file'))
			->select($dbo->qn('createdon'))
			->from($dbo->qn('#__vikrestaurants_invoice'))
			->where($dbo->qn('id_order') . ' = ' . (int) $this->id)
			->where($dbo->qn('group') . ' = 1');

		$dbo->setQuery($q, 0, 1);
		
		// load invoice
		$invoice = $dbo->loadObject();

		if (!$invoice)
		{
			// invoice not found
			return false;
		}

		// build invoice file path
		$invoice->path = VREINVOICE . DIRECTORY_SEPARATOR . $invoice->file;

		// build invoice file URI
		$invoice->uri = VREINVOICE_URI . $invoice->file;

		return $invoice;
	}

	/**
	 * @override
	 * Returns the history of the status codes set for the order.
	 *
	 * @return 	array
	 */
	protected function getHistory()
	{
		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('os.id'))
			->select($dbo->qn('os.id_rescode', 'idCode'))
			->select($dbo->qn('os.notes'))
			->select($dbo->qn('os.createdon'))
			->select($dbo->qn('os.createdby'))
			->select($dbo->qn('u.name', 'userName'))
			->select($dbo->qn('rc.code'))
			->select($dbo->qn('rc.icon'))
			->select($dbo->qn('rc.notes', 'codeNotes'))
			->from($dbo->qn('#__vikrestaurants_order_status', 'os'))
			->leftjoin($dbo->qn('#__vikrestaurants_res_code', 'rc') . ' ON ' . $dbo->qn('rc.id') . ' = ' . $dbo->qn('os.id_rescode'))
			->leftjoin($dbo->qn('#__users', 'u') . ' ON ' . $dbo->qn('u.id') . ' = ' . $dbo->qn('os.createdby'))
			->where($dbo->qn('os.id_order') . ' = ' . (int) $this->id)
			->where($dbo->qn('os.group') . ' = ' . 2)
			->order($dbo->qn('os.createdon') . ' DESC');

		$dbo->setQuery($q);
		$history = $dbo->loadObjectList();

		foreach ($history as $code)
		{
			if ($code->icon)
			{
				// set up icon URL
				$code->iconURL = VREMEDIA_SMALL_URI . $code->icon;
			}
		}

		return $history;
	}
}
