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
 * VikRestaurants HTML admin helper.
 *
 * @since 1.8
 */
abstract class VREHtmlAdmin
{
	/**
	 * Method to sort a column in a grid.
	 *
	 * @param   string  $title          The link title.
	 * @param   string  $order          The order field for the column.
	 * @param   string  $direction      The current direction.
	 * @param   string  $selected       The selected ordering.
	 * @param   string  $task           An optional task override.
	 * @param   string  $new_direction  An optional direction for the new column.
	 * @param   string  $tip            An optional text shown as tooltip title instead of $title.
	 * @param   string  $form           An optional form selector.
	 *
	 * @return  string
	 * 
	 * @since   1.9
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = '', $task = null, $new_direction = 'asc', $tip = '', $form = null)
	{
		if (!$tip && preg_match("/ordering$/i", $order))
		{
			// force "Ordering" as tooltip in order to avoid
			// display the HTML of the icon
			$tip = 'JGRID_HEADING_ORDERING';
		}

		// render grid HTML
		$html = JHtml::fetch('grid.sort', $title, $order, $direction, $selected, $task, $new_direction, $tip, $form);

		// turn off tooltip or popover
		$html = preg_replace("/\bhas(?:Tooltip|Popover)\b/", '', $html);

		return $html;
	}

	/**
	 * Creates an action link to display and toggle the status of a column.
	 *
	 * @param 	mixed   $state  The current state of the column or an array/object
	 *                          holding the state and the publishing dates (start, end).
	 * @param 	mixed   $id     An optional record ID.
	 * @param 	string  $task   The task to reach to perform the status change.
	 * @param 	mixed   $can    The user permissions. Leave null to auto-detect.
	 * @param 	array   $args   An associative array used to extend the query string.
	 * @param 	string  $title  An optional title to attach to the state icon.
	 * @param   array   $attrs  An associative array of attributes to inject within the HTML tag.
	 *
	 * @return  string  The resulting HTML.
	 * 
	 * @since   1.9
	 */
	public static function stateaction($state, $id = null, $task = null, $can = null, array $args = [], $title = '', array $attrs = [])
	{
		if ($task && is_null($can))
		{
			// retrieve permissions
			$can = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');
		}

		// check if we have an object
		if (is_array($state) || is_object($state))
		{
			$data = new JObject($state);
			
			// try to extract state and publishing dates
			$state = $data->get('state', 0);
			$start = $data->get('start', null);
			$end   = $data->get('end', null);
		}
		else
		{
			$start = $end = null;
		}

		// fetch class status
		if ($state)
		{
			// published
			$state_class = 'fas fa-check-circle ok';

			if (is_numeric($start) || is_numeric($end))
			{
				// timestamp provided
				$now = VikRestaurants::now();
				$tz  = date_default_timezone_get();
			}
			else
			{
				// date time provided
				$now = JDate::getInstance()->toSql();
				$tz  = true;
			}

			// check whether the start publishing is in the future
			if (!E4J\VikRestaurants\Helpers\DateHelper::isNull($start) && $now < $start)
			{
				// not yet started
				$state_class = 'fas fa-minus-circle warn';
				// override title
				$start = JHtml::fetch('date', $start, 'DATE_FORMAT_LC3', $tz);
				$title = JText::sprintf('VRE_PUBL_START_ON', $start);
			}
			else if (!E4J\VikRestaurants\Helpers\DateHelper::isNull($end) && $now > $end)
			{
				// expired
				$state_class = 'fas fa-minus-circle warn';
				// override title
				$end   = JHtml::fetch('date', $end, 'DATE_FORMAT_LC3', $tz);
				$title = JText::sprintf('VRE_PUBL_END_ON', $end);
			}
		}
		else
		{
			// unpublished
			$state_class = 'fas fa-dot-circle no';
		}

		$class = trim($state_class . ' ' . ($attrs['class'] ?? 'big'));

		if ($title)
		{
			// define title attribute
			$title  = ' title="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '"';
			$class .= ' hasTooltip';
		}

		// create badge icon
		$html = '<i class="' . $class . '"' . $title . '></i>';

		if ($can && $task && $id)
		{
			// create action URL
			$url = 'index.php?option=com_vikrestaurants&task=';

			if (preg_match("/(^|\.)publish$/i", $task))
			{
				// we are using the native "publish" and "unpublish" tasks
				if ($state)
				{
					// replace "publish" with "unpublish"
					$task = preg_replace("/(^|\.)publish$/i", '$1unpublish', $task);
				}
				
				$url .= $task;
			}
			else
			{
				// we are using a custom task, so we need to defin the new state
				$url .= $task . '&state=' . ($state ? 0 : 1);
			}

			// append record ID
			$url .= '&cid[]=' . $id;

			if ($args)
			{
				// extend the query string with the given parameters
				$url .= '&' . http_build_query($args);
			}

			// append CSRF token
			$url = VREFactory::getPlatform()->getUri()->addCSRF($url, $xhtml = true);

			// wrap badge within a link to implement status change
			$html = '<a href="' . $url . '">' . $html . '</a>';
		}
		
		return $html;
	}

	/**
	 * Returns a HTML block representing the status of an image.
	 *
	 * @param 	string   $image    The relative path of the image.
	 * @param 	boolean  $preview  True to enable the preview (only if image exists).
	 *
	 * @return 	string   The resulting HTML.
	 * 
	 * @since   1.9
	 */
	public static function imagestatus($image, $preview = true)
	{
		if (empty($image))
		{
			// image not uploaded
			$status = 2;
			$icon   = 'fas fa-image no';
		}
		else if (!is_file(VREMEDIA . DIRECTORY_SEPARATOR . $image))
		{
			// image not found
			$status = 0;
			$icon   = 'fas fa-eye-slash no';
		}
		else
		{
			// image ok
			$status = 1;
			$icon   = 'fas fa-image ok';
		}

		$title = JText::translate('VRIMAGESTATUS' . $status);

		// create HTML icon status
		$html = '<i class="' . $icon . ' big-2x" title="' . htmlspecialchars($title) . '"></i>';

		if ($status == 1 && $preview)
		{
			// wrap icon within a link to support preview
			$html = '<a href="' . VREMEDIA_URI . $image . '" class="modal" target="_blank">' . $html . '</a>';
		}

		return $html;
	}

	/**
	 * Returns an array of working shifts.
	 *
	 * @param 	mixed 	$shifts  Either a list of shifts or the parent group.
	 * 							 If empty, all the groups will be considered.
	 * @param 	string  $pk      The value to use in the option ('id' or 'interval').
	 *
	 * @return 	array 	A list of shifts.
	 */
	public static function shifts($shifts = null, $pk = 'id')
	{
		if (!is_array($shifts))
		{
			// check if we passed the shifts group
			if (preg_match("/^[\d]$/", $shifts))
			{
				// obtain working shifts by group
				$shifts = JHtml::fetch('vikrestaurants.shifts', $shifts);
			}
			else if (is_null($shifts))
			{
				// recover working shifts for default group if not specified
				$default = JHtml::fetch('vrehtml.admin.getgroup', 0, [1, 2]);
				$shifts  = JHtml::fetch('vikrestaurants.shifts', $default);
			}
		}

		$options = array();

		// iterate working shifts
		foreach ($shifts as $shift)
		{
			// always treat the record as an object
			$shift = (object) $shift;

			if ($pk == 'id')
			{
				// use the working shift record ID
				$value = $shift->id;
			}
			else
			{
				// use the opening interval as option value
				$value = $shift->from . '-' . $shift->to;
			}

			// add option value
			$options[] = JHtml::fetch('select.option', $value, $shift->name);
		}

		return $options;
	}

	/**
	 * Returns an array of working shifts for the given day.
	 *
	 * @param 	integer  $group  The group to which the shifts belong.
	 * @param 	mixed 	 $day    The day to look for. If not specified,
	 * 					 	     the current day will be used.
	 * @param 	string   $pk     The value to use in the option ('id' or 'interval').
	 *
	 * @return 	array 	 A list of shifts.
	 */
	public static function dayshifts($group, $day = null, $pk = 'id')
	{
		// extract timestamp from day
		if (is_null($day))
		{
			// use current date
			$day = VikRestaurants::now();
		}
		
		// convert to date string if UNIX timestamp
		if (is_numeric($day))
		{
			$day = date(VREFactory::getConfig()->get('dateformat'), $day);
		}

		// get shifts for specified day
		$shifts = JHtml::fetch('vikrestaurants.shifts', $group, $day, false);

		foreach ($shifts as &$shift)
		{
			// cast shift to array
			$shift = (array) $shift;

			// use label as option text if specified
			if ($shift['showlabel'] && $shift['label'])
			{
				$shift['name'] = $shift['label'];
			}
		}

		// create a list of working shifts
		return self::shifts($shifts, $pk);
	}

	/**
	 * Returns a list of supported groups.
	 *
	 * @param 	array    $values       A list of supported values.
	 * @param 	boolean  $allowClear   True to include an empty option.
	 * @param 	string   $placeholder  A specific text to use for the empty option.
	 *
	 * @return 	array    A list of dropdown options.
	 */
	public static function groups($values = null, $allowClear = false, $placeholder = null)
	{
		if ($placeholder === null)
		{
			$placeholder = 'VRE_FILTER_SELECT_GROUP';
		}

		if ($values === null || !is_array($values) || count($values) != 2)
		{
			$values = array(0, 1);
		}

		$options = array();

		if ($allowClear)
		{
			$options[] = JHtml::fetch('select.option', '', JText::translate($placeholder));
		}

		$rs_enabled = VikRestaurants::isRestaurantEnabled();
		$tk_enabled = VikRestaurants::isTakeAwayEnabled();

		if (!$rs_enabled && !$tk_enabled)
		{
			// do not proceed in case both the sections are turned off
			return $options;
		}

		if ($rs_enabled)
		{
			// append restaurant option, if enabled
			$options[] = JHtml::fetch('select.option', $values[0], JText::translate('VRMANAGECONFIGTITLE1'));
		}

		if ($tk_enabled)
		{
			// append take-away option, if enabled
			$options[] = JHtml::fetch('select.option', $values[1], JText::translate('VRMANAGECONFIGTITLE2'));
		}

		return $options;
	}

	/**
	 * Returns the default group in case the specified on is not supported.
	 *
	 * @param 	mixed 	 $group       The group value to check. 
	 * @param 	array    $values      A list of supported values.
	 * @param 	boolean  $allowClear  True to include an empty option.
	 *
	 * @return 	mixed    The group identifier.
	 */
	public static function getgroup($group = null, $values = null, $allowClear = false)
	{
		if ((is_null($group) || $group == '') && $allowClear)
		{
			// return null in case no group was specified and the
			// dropdown supports empty values
			return null;
		}

		if ($values === null || !is_array($values))
		{
			$values = array(0, 1);
		}

		if (!VikRestaurants::isRestaurantEnabled())
		{
			// remove restaurant value from list (first)
			array_shift($values);
		}

		if (!VikRestaurants::isTakeAwayEnabled())
		{
			// remove take-away value from list (last)
			array_pop($values);
		}

		if (in_array($group, $values))
		{
			// the group is supported, return it directly
			return $group;
		}

		if (!$allowClear)
		{
			// return the first available group in case it
			// is mandatory to have an active value
			return array_shift($values);
		}

		// fallback to empty placeholder
		return null;
	}

	/**
	 * Returns a list of countries.
	 *
	 * @return 	array 	 The countries list.
	 */
	public static function countries()
	{
		static $countries = null;

		// load countries only once
		if (!$countries)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikrestaurants_countries'))
				->where($dbo->qn('published') . ' = 1')
				->order($dbo->qn('country_name') . ' ASC');

			$dbo->setQuery($q);
			$countries = $dbo->loadObjectList();
		}

		$options = array();

		foreach ($countries as $country)
		{
			$options[] = JHtml::fetch('select.option', $country->country_2_code, $country->country_name);
		}

		return $options;
	}

	/**
	 * Returns a list of published tables, grouped by room.
	 *
	 * @param 	boolean  $blank  True to include an empty option.
	 *
	 * @return 	array 	 A list of tables.
	 */
	public static function tables($blank = false)
	{
		$options = array();

		if ($blank)
		{
			$options[0] = array(JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_TABLE')));
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('t.id'))
			->select($dbo->qn('t.name'))
			->select($dbo->qn('r.name', 'rname'))
			->from($dbo->qn('#__vikrestaurants_table', 't'))
			->leftjoin($dbo->qn('#__vikrestaurants_room', 'r') . ' ON ' . $dbo->qn('r.id') . ' = ' . $dbo->qn('t.id_room'))
			->where($dbo->qn('r.published') . ' = 1')
			->where($dbo->qn('t.published') . ' = 1')
			->order($dbo->qn('r.ordering') . ' ASC')
			->order($dbo->qn('t.name') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $t)
		{
			if (!isset($options[$t->rname]))
			{
				$options[$t->rname] = array();
			}

			$options[$t->rname][] = JHtml::fetch('select.option', $t->id, $t->name);
		}

		return $options;
	}

	/**
	 * Returns a list of restaurant menus.
	 *
	 * @param 	mixed    $blank  True to include an empty option. Use a string to specify the 
	 *                           placeholder of the blank option.
	 * @param 	boolean  $group  True to group the options by status.
	 *
	 * @return 	array    A list of menus.
	 * 
	 * @since   1.9
	 */
	public static function menus($blank = false, $group = false)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('JGLOBAL_SELECT_AN_OPTION');

			$options[] = JHtml::fetch('select.option', '', $blank);

			if ($group)
			{
				$options[0] = [$options[0]];
			}
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn(['id', 'name']))
			->from($db->qn('#__vikrestaurants_menus'));

		if ($group)
		{
			$query->select($db->qn('special_day'));
			$query->order($db->qn('special_day'));
		}

		$query->order($db->qn('ordering') . ' ASC');

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $menu)
		{
			$opt = JHtml::fetch('select.option', $menu->id, $menu->name);

			if ($group)
			{
				// create group key
				$key = $menu->special_day ? JText::translate('VRMANAGESPDAY14') : JText::translate('VRMANAGESPDAY15');

				// create group if not exists
				if (!isset($options[$key]))
				{
					$options[$key] = [];
				}

				// add within group
				$options[$key][] = $opt;
			}
			else
			{
				// add at first level
				$options[] = $opt;
			}
		}

		return $options;
	}

	/**
	 * Returns a list of take-away menus.
	 *
	 * @param 	mixed    $blank  True to include an empty option. Use a string to specify the 
	 *                           placeholder of the blank option.
	 * @param 	boolean  $group  True to group the options by status.
	 *
	 * @return 	array    A list of menus.
	 * 
	 * @since   1.9
	 */
	public static function tkmenus($blank = false, $group = false)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('JGLOBAL_SELECT_AN_OPTION');

			$options[] = JHtml::fetch('select.option', '', $blank);

			if ($group)
			{
				$options[0] = [$options[0]];
			}
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn(['id', 'title']))
			->from($db->qn('#__vikrestaurants_takeaway_menus'));

		if ($group)
		{
			$query->select($db->qn('published'));
			$query->order($db->qn('published'));
		}

		$query->order($db->qn('ordering') . ' ASC');

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $menu)
		{
			$opt = JHtml::fetch('select.option', $menu->id, $menu->title);

			if ($group)
			{
				// create group key
				$key = $menu->published ? JText::translate('JPUBLISHED') : JText::translate('JUNPUBLISHED');

				// create group if not exists
				if (!isset($options[$key]))
				{
					$options[$key] = [];
				}

				// add within group
				$options[$key][] = $opt;
			}
			else
			{
				// add at first level
				$options[] = $opt;
			}
		}

		return $options;
	}

	/**
	 * Returns a list of take-away products.
	 *
	 * @param 	boolean  $group  True to group the products by menu.
	 * @param   boolean  $vars   True to include the variations of the products.
	 *
	 * @return 	array    A list of take-away products.
	 * 
	 * @since   1.9
	 */
	public static function tkproducts($group = false, $vars = false)
	{
		$rows = [];

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// fetch products
		$query->select([
			$db->qn('e.id', 'id_product'),
			$db->qn('e.name', 'product_name'),
		]);

		$query->from($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e'));

		if ($group)
		{
			// group by menus
			$query->select([
				$db->qn('m.id', 'id_menu'),
				$db->qn('m.title', 'menu_title'),
			]);

			$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('e.id_takeaway_menu'));
			$query->order($db->qn('m.ordering') . ' ASC');
		}

		$query->order($db->qn('e.ordering') . ' ASC');

		if ($vars)
		{
			// fetch variations too
			$query->select([
				$db->qn('o.id', 'id_option'),
				$db->qn('o.name', 'option_name'),
			]);

			$query->leftjoin($db->qn('#__vikrestaurants_takeaway_menus_entry_option', 'o') . ' ON ' . $db->qn('e.id') . ' = ' . $db->qn('o.id_takeaway_menu_entry'));
			$query->order($db->qn('o.ordering') . ' ASC');
		}

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $product)
		{
			if ($group)
			{
				if (!isset($rows[$product->id_menu]))
				{
					$menu = new stdClass;
					$menu->id       = $product->id_menu;
					$menu->title    = $product->menu_title;
					$menu->products = [];

					$rows[$product->id_menu] = $menu;
				}

				if (!isset($rows[$product->id_menu]->products[$product->id_product]))
				{
					$prod = new stdClass;
					$prod->id      = $product->id_product;
					$prod->name    = $product->product_name;
					$prod->options = [];

					$rows[$product->id_menu]->products[$product->id_product] = $prod;
				}

				if ($vars && $product->id_option)
				{
					$opt = new stdClass;
					$opt->id   = $product->id_option;
					$opt->name = $product->option_name;

					$rows[$product->id_menu]->products[$product->id_product]->options[] = $opt;
				}
			}
			else
			{
				$rows[] = $product;
			}
		}

		return $rows;
	}

	/**
	 * Returns a list of take-away delivery areas.
	 *
	 * @param 	mixed    $blank  True to include an empty option. Use a string to specify the 
	 *                           placeholder of the blank option.
	 * @param 	boolean  $group  True to group the options by status.
	 *
	 * @return 	array    A list of delivery areas.
	 * 
	 * @since   1.9
	 */
	public static function tkareas($blank = false, $group = false)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('JGLOBAL_SELECT_AN_OPTION');

			$options[] = JHtml::fetch('select.option', '', $blank);

			if ($group)
			{
				$options[0] = [$options[0]];
			}
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn(['id', 'name']))
			->from($db->qn('#__vikrestaurants_takeaway_delivery_area'));

		if ($group)
		{
			$query->select($db->qn('published'));
			$query->order($db->qn('published'));
		}

		$query->order($db->qn('ordering') . ' ASC');

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $area)
		{
			$opt = JHtml::fetch('select.option', $area->id, $area->name);

			if ($group)
			{
				// create group key
				$key = $area->published ? JText::translate('JPUBLISHED') : JText::translate('JUNPUBLISHED');

				// create group if not exists
				if (!isset($options[$key]))
				{
					$options[$key] = [];
				}

				// add within group
				$options[$key][] = $opt;
			}
			else
			{
				// add at first level
				$options[] = $opt;
			}
		}

		return $options;
	}

	/**
	 * Returns a list of take-away toppings separators.
	 *
	 * @return 	array 	 The separators list.
	 * 
	 * @since   1.9
	 */
	public static function tktopseparators()
	{
		static $separators = null;

		// load separators only once
		if (!$separators)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikrestaurants_takeaway_topping_separator'))
				->order($dbo->qn('ordering') . ' ASC');

			$dbo->setQuery($q);
			$separators = $dbo->loadObjectList();
		}

		$options = array();

		foreach ($separators as $separator)
		{
			$options[] = JHtml::fetch('select.option', $separator->id, $separator->title);
		}

		return $options;
	}

	/**
	 * Returns a list of supported toppings, grouped by separator.
	 *
	 * @return  array  A list of separators and toppings.
	 * 
	 * @since   1.9
	 */
	public static function tktoppings()
	{
		$groups = [];

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);
		
		$q->select('`t`.*');
		$q->select($dbo->qn('s.title', 'separator_title'));
		
		$q->from($dbo->qn('#__vikrestaurants_takeaway_topping', 't'));
		$q->leftjoin($dbo->qn('#__vikrestaurants_takeaway_topping_separator', 's') . ' ON ' . $dbo->qn('t.id_separator') . ' = ' . $dbo->qn('s.id'));

		$q->order($dbo->qn('s.ordering') . ' ASC');
		$q->order($dbo->qn('t.ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $topping)
		{
			$id_separator = $topping->id_separator;

			if (!isset($groups[$id_separator]))
			{
				$grp = new stdClass;
				$grp->id       = $id_separator;
				$grp->title    = $topping->separator_title;
				$grp->toppings = [];

				$groups[$topping->id_separator] = $grp;
			}

			unset($topping->id_separator, $topping->separator_title);
			$groups[$id_separator]->toppings[] = $topping;
			
		}

		return array_values($groups);
	}

	/**
	 * Returns a list of take-away menus attributes.
	 *
	 * @return 	array 	 The attributes list.
	 * 
	 * @since   1.9
	 */
	public static function tkattributes()
	{
		static $attributes = null;

		// load attributes only once
		if (!$attributes)
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select('*')
				->from($dbo->qn('#__vikrestaurants_takeaway_menus_attribute'))
				->order($dbo->qn('ordering') . ' ASC');

			$dbo->setQuery($q);
			$attributes = $dbo->loadObjectList();
		}

		$options = [];

		foreach ($attributes as $attr)
		{
			$opt = JHtml::fetch('select.option', $attr->id, $attr->name);
			// preserve icon in option object
			$opt->icon = $attr->icon;

			$options[] = $opt;
		}

		return $options;
	}

	/**
	 * Returns a list of supported coupon groups.
	 *
	 * @param   mixed  $blank  True to include an empty option. Use a string to
	 *                         specify the placeholder of the empty option.
	 *
	 * @return  array  A list of coupon groups.
	 * 
	 * @since   1.9
	 */
	public static function coupongroups($blank = false)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('VRE_FILTER_SELECT_CATEGORY');

			$options[] = JHtml::fetch('select.option', '', $blank);
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(['id', 'name']))
			->from($dbo->qn('#__vikrestaurants_coupon_category'))
			->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $group)
		{
			$options[] = JHtml::fetch('select.option', $group->id, $group->name);
		}

		return $options;
	}

	/**
	 * Returns a list of supported coupon codes.
	 *
	 * @param 	mixed    $blank       True to include an empty option. Use a string to
	 *                                specify the placeholder of the empty option.
	 * @param 	boolean  $group       True to group the coupons.
	 * @param 	mixed    $section     The section to which the coupons are applicable.
	 *
	 * @return 	array 	 A list of coupons.
	 * 
	 * @since   1.9
	 */
	public static function coupons($blank = false, $group = false, $section = null)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('VRE_FILTER_SELECT_COUPON');

			$options[] = JHtml::fetch('select.option', '', $blank);

			if ($group)
			{
				$options[0] = [$options[0]];
			}
		}

		$no_group = JText::translate('VRTKOTHERSSEPARATOR');
		$currency = VREFactory::getCurrency();

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true);
		$q->select($dbo->qn(['c.code', 'c.percentot', 'c.value']));
		$q->from($dbo->qn('#__vikrestaurants_coupons', 'c'));

		if ($group)
		{
			$q->select($dbo->qn('g.name', 'group_name'));
			$q->leftjoin($dbo->qn('#__vikrestaurants_coupon_category', 'g') . ' ON ' . $dbo->qn('g.id') . ' = ' . $dbo->qn('c.id_category'));
			// sort coupons by group first
			$q->order($dbo->qn('g.ordering') . ' ASC');
		}
		
		if (!is_null($section))
		{
			if (!is_numeric($section))
			{
				$section = $section == 'restaurant' ? 0 : 1;
			}

			// restrict to applicable group only
			$q->where($dbo->qn('c.group') . ' = ' . (int) $section);
		}

		$q->order($dbo->qn('c.id') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $coupon)
		{
			if ($coupon->value <= 0)
			{
				$sfx = '';
			}
			else if ($coupon->percentot == 1)
			{
				$sfx = ' (' . $coupon->value . '%)';
			}
			else
			{
				$sfx = ' (' . $currency->format($coupon->value) . ')';
			}

			$opt = JHtml::fetch('select.option', $coupon->code, $coupon->code . $sfx);

			if ($group)
			{
				// create group key
				$key = $coupon->group_name ? $coupon->group_name : $no_group;

				// create group if not exists
				if (!isset($options[$key]))
				{
					$options[$key] = [];
				}

				// add within group
				$options[$key][] = $opt;
			}
			else
			{
				// add at first level
				$options[] = $opt;
			}
		}

		if ($group && isset($options[$no_group]))
		{
			// always move coupons without group at the end of the list
			$tmp = $options[$no_group];
			unset($options[$no_group]);
			$options[$no_group] = $tmp;
		}

		return $options;
	}

	/**
	 * Returns a list of supported reservation codes rules..
	 *
	 * @param 	mixed  $blank  True to include an empty option. In case of a
	 * 						   string, it will be used as placeholder.
	 *
	 * @return 	array  A list of rules.
	 */
	public static function rescodesrules($blank = false)
	{
		$options = array();

		if ($blank !== false)
		{
			if ($blank === true)
			{
				// use default placeholder
				$blank = JText::translate('VRE_FILTER_SELECT_RULE');
			}

			// include empty option
			$options[0] = array(JHtml::fetch('select.option', '', $blank));
		}

		// define optgroup/driver section lookup
		$lookup = array(
			'restaurant',
			'takeaway',
			'food',
		);

		foreach ($lookup as $group)
		{
			/** @var E4J\VikRestaurants\ReservationCodes\CodeRule[] */
			$drivers = E4J\VikRestaurants\ReservationCodes\CodesHandler::getSupportedRules($group);

			if ($drivers)
			{
				// add global drivers
				$options[$group] = array();

				// iterate drivers to build dropdown options
				foreach ($drivers as $d)
				{
					$options[$group][] = JHtml::fetch('select.option', $d->getID(), $d->getName());
				}
			}
		}

		return $options;
	}

	/**
	 * Returns a list of supported taxes.
	 *
	 * @param 	mixed  $blank  True to include an empty option. Use a string to specify the 
	 *                         placeholder of the blank option.
	 *
	 * @return 	array  A list of taxes.
	 * 
	 * @since   1.9
	 */
	public static function taxes($blank = false)
	{
		$options = array();

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('JGLOBAL_SELECT_AN_OPTION');

			$options[] = JHtml::fetch('select.option', '', $blank);
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn(array('id', 'name')))
			->from($dbo->qn('#__vikrestaurants_tax'))
			->order($dbo->qn('name') . ' ASC');

		$dbo->setQuery($q);
		
		// create list
		foreach ($dbo->loadObjectList() as $tax)
		{
			$options[] = JHtml::fetch('select.option', $tax->id, $tax->name);
		}

		return $options;
	}

	/**
	 * Returns a list of supported payment gateways for the given type.
	 *
	 * @param 	string   $type    The group to look for (restaurant or take-away).
	 * @param 	mixed    $blank   True to include an empty option. Use a string to specify the 
	 *                            placeholder of the blank option.
	 * @param 	boolean  $group   True to group the payments by status.
	 * @param   boolean  $costs   True to include the payment charge within the options.
	 *
	 * @return 	array 	 A list of payment gateways.
	 * 
	 * @since   1.9
	 */
	public static function payments($type = 'restaurant', $blank = false, $group = false, $costs = false)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('VRE_FILTER_SELECT_PAYMENT');

			$options[] = JHtml::fetch('select.option', '', $blank);

			if ($group)
			{
				$options[0] = [$options[0]];
			}
		}

		$dbo = JFactory::getDbo();

		$currency = VREFactory::getCurrency();

		$q = $dbo->getQuery(true);
		$q->select($dbo->qn(['id', 'name', 'published', 'charge', 'percentot']));
		$q->from($dbo->qn('#__vikrestaurants_gpayments'));
		$q->where(1);

		if ($type == 'restaurant')
		{
			// allowed for restaurant
			$q->andWhere([
				$dbo->qn('group') . ' = 0',
				$dbo->qn('group') . ' = 1',
			]);
		}
		else if ($type == 'takeaway')
		{
			// allowed for take-away
			$q->andWhere([
				$dbo->qn('group') . ' = 0',
				$dbo->qn('group') . ' = 2',
			]);
		}

		if ($group)
		{
			// take published items first when we need to group them
			$q->order($dbo->qn('published') . ' DESC');
		}

		$q->order($dbo->qn('ordering') . ' ASC');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $payment)
		{
			if ($costs && $payment->charge != 0)
			{
				if ($payment->percentot == 1)
				{
					// percentage amount
					$chargeLabel = $currency->format($payment->charge, [
						'symbol'     => '%',
						'position'   => 1,
						'space'      => false,
						'no_decimal' => true,
					]);
				}
				else
				{
					// fixed amount
					$chargeLabel = $currency->format($payment->charge);
				}

				$payment->name .= ' (' . $chargeLabel . ')';
			}

			$opt = JHtml::fetch('select.option', $payment->id, $payment->name);

			// include payment charge too
			$opt->charge       = $payment->charge;
			$opt->percentot    = $payment->percentot;
			$opt->dataCharge   = 'data-charge="' . (float) $payment->charge . '"';
			$opt->dataPercento = 'data-percentot="' . (int) $payment->percentot . '"';

			if ($group)
			{
				// create group key
				$key = JText::translate($payment->published ? 'JPUBLISHED' : 'JUNPUBLISHED');

				// create status group if not exists
				if (!isset($options[$key]))
				{
					$options[$key] = [];
				}

				// add within group
				$options[$key][] = $opt;
			}
			else
			{
				// add at first level
				$options[] = $opt;
			}
		}

		return $options;
	}

	/**
	 * Returns a list of supported payment gateways.
	 *
	 * @param 	boolean  $blank  True to include an empty option.
	 *
	 * @return 	array 	 A list of drivers.
	 */
	public static function paymentdrivers($blank = false)
	{
		// get payment drivers
		$files = VREFactory::getPlatform()->getPaymentFactory()->getDrivers();

		$options = array();

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('VRE_FILTER_SELECT_DRIVER');

			$options[] = JHtml::fetch('select.option', '', $blank);
		}

		foreach ($files as $file)
		{
			// get file name
			$value = basename($file);
			// strip file extension
			$text = preg_replace("/\.php$/", '', $value);

			$options[] = JHtml::fetch('select.option', $value, $text);
		}

		return $options;
	}

	/**
	 * Returns a list of supported SMS providers.
	 *
	 * @param 	boolean  $blank  True to include an empty option.
	 *
	 * @return 	array 	 A list of drivers.
	 */
	public static function smsdrivers($blank = false)
	{
		// get SMS drivers
		$files = VREApplication::getInstance()->getSmsDrivers();

		$options = array();

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('VRE_FILTER_SELECT_DRIVER');

			$options[] = JHtml::fetch('select.option', '', $blank);
		}

		foreach ($files as $file)
		{
			// get file name
			$value = basename($file);
			// strip file extension
			$text = preg_replace("/\.php$/", '', $value);

			$options[] = JHtml::fetch('select.option', $value, $text);
		}

		return $options;
	}

	/**
	 * Returns a list of supported API logins.
	 *
	 * @param 	boolean  $blank  True to include an empty option.
	 *
	 * @return 	array 	 A list of logins.
	 */
	public static function apilogins($blank = false)
	{
		$options = array();

		if ($blank)
		{
			$options[] = JHtml::fetch('select.option', '', JText::translate('VRE_FILTER_SELECT_APPLICATION'));
		}

		$dbo = JFactory::getDbo();

		$q = $dbo->getQuery(true)
			->select($dbo->qn('l.application'))
			->select($dbo->qn('l.username'))
			->select($dbo->qn('l.password'))
			->from($dbo->qn('#__vikrestaurants_api_login', 'l'))
			->where($dbo->qn('l.active') . ' = 1');

		$dbo->setQuery($q);
		
		foreach ($dbo->loadObjectList() as $r)
		{
			if ($r->application)
			{
				$text = $r->application . ' : ' . $r->username;
			}
			else
			{
				$text = $r->username;
			}

			$value = $r->username . ';' . $r->password;

			$options[] = JHtml::fetch('select.option', $value, $text);
		}

		return $options;
	}

	/**
	 * Returns a list of supported status codes for the given group.
	 *
	 * @param   string  $group  The group to look for (restaurant or takeaway).
	 * @param   mixed   $blank  True to include an empty option. Use a string to specify the 
	 *                          placeholder of the blank option.
	 *
	 * @return  array   A list of status codes.
	 */
	public static function statuscodes($group, $blank = false)
	{
		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('VRFILTERSELECTSTATUS');

			$options[] = JHtml::fetch('select.option', '', $blank);
		}

		$where = [];

		if ($group)
		{
			// filter only when a group is specified
			$where[$group] = 1;
		}

		// search status codes
		$codes = JHtml::fetch('vrehtml.status.find', ['code', 'name'], $where);

		// create list
		foreach ($codes as $code)
		{
			// avoid displaying duplicate status codes in case of missing group
			$options[$code->code] = JHtml::fetch('select.option', $code->code, $code->name);
		}

		return array_values($options);
	}

	/**
	 * Returns a list of supported e-mail templates.
	 *
	 * @param   string  $group  The e-mail templates group (restaurant or takeaway).
	 * @param   bool    $blank  True to include an empty option. Use a string to specify the 
	 *                          placeholder of the blank option.
	 *
	 * @return  array   A list of files.
	 * 
	 * @since   1.9
	 */
	public static function mailtemplates($group, $blank = false)
	{
		if ($group === 'restaurant')
		{
			// load restaurant e-mail templates
			$path = VREHELPERS . '/mail_tmpls';
		}
		else if ($group === 'takeaway')
		{
			// load take-away e-mail templates
			$path = VREHELPERS . '/tk_mail_tmpls';
		}
		else
		{
			// unsupported group...
			throw new UnexpectedValueException('Invalid mail template [' . $group . '] group');
		}

		// fetch templates
		$files = JFolder::files($path, '\.php$', $recursive = false, $fullPath = true);

		$options = [];

		if ($blank !== false)
		{
			$blank = is_string($blank) ? $blank : JText::translate('JGLOBAL_SELECT_AN_OPTION');

			$options[] = JHtml::fetch('select.option', '', $blank);
		}

		foreach ($files as $file)
		{
			$filename = basename($file);

			// remove file extension
			$name = preg_replace("/\.php$/i", '', $filename);
			// remove ending "_mail_tmpl"
			$name = preg_replace("/_?e?mail_?tmpl$/i", '', $name);
			// replace dashes and underscores with spaces
			$name = preg_replace("/[-_]+/", ' ', $name);
			// capitalize words
			$name = ucwords(strtolower($name));

			$opt = JHtml::fetch('select.option', $filename, $name);

			// include file path
			$opt->file = $file;

			$options[] = $opt;
		}

		return $options;
	}

	/**
	 * Returns the HTML of the handle used to rearrange the table rows.
	 * FontAwesome is required in order to display the handle icon.
	 *
	 * @param 	integer   $ordering  The ordering value.
	 * @param 	boolean   $canEdit   True if the user is allowed to edit the ordering.
	 * @param 	boolean   $canOrder  True if the table is currently sorted by "ordering" column.
	 * @param 	boolean   $input     True if the ordering input should be included in the body.
	 *
	 * @return 	string 	  The HTML of the handle.
	 */
	public static function sorthandle($ordering, $canEdit = true, $canOrder = true, $input = true)
	{
		$icon_class = $icon_title = '';

		if (!$canEdit)
		{
			$icon_class = ' inactive';
		}
		else if (!$canOrder)
		{
			$icon_class = ' inactive tip-top hasTooltip';
			$icon_title = JText::translate('JORDERINGDISABLED');
		}

		$html = '<span class="sortable-handler' . $icon_class . '" title="' . $icon_title . '">
			<i class="fas fa-ellipsis-v medium-big" aria-hidden="true"></i>
		</span>';

		if ($canEdit && $canOrder && $input)
		{
			$html .= '<input type="hidden" name="order[]" value="' . $ordering . '" />';
		}

		return $html;
	}
}
