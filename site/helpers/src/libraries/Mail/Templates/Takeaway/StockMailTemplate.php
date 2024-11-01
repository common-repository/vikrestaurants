<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Templates\Takeaway;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplateAware;

/**
 * Wrapper used to send mail notifications to the administrators
 * about the items with a stock lower than the specified threshold.
 *
 * @since 1.9
 */
class StockMailTemplate extends MailTemplateAware
{
	/**
	 * The items list.
	 *
	 * @var object[]
	 */
	protected $items;

	/**
	 * An array of options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * An optional template file to use.
	 *
	 * @var string
	 */
	protected $templateFile;

	/**
	 * Class constructor.
	 *
	 * @param  array  $options  A configuration array.
	 */
	public function __construct(array $options = [])
	{
		if (empty($options['lang']))
		{
			// language not provided, use the current one
			$options['lang'] = \JFactory::getLanguage()->getTag();
		}

		// register options
		$this->options = $options;

		// load given language to translate template contents
		\VikRestaurants::loadLanguage($this->options['lang']);

		// fetch the products to notify
		$this->items = $this->getItems($options);

		// use global sender
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\SenderMailDecorator);

		// set all the administrators as recipient
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\RecipientAdministratorsMailDecorator);

		// inject generic company information, such as the restaurant name and the image logo
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\CompanyMailDecorator);

		// inject generic stock information
		$this->attachDecorator(new \E4J\VikRestaurants\Mail\Decorators\TakeawayStockMailDecorator);
	}

	/**
	 * @inheritDoc
	 */
	public function setFile($file)
	{
		// check if a filename or a path was passed
		if ($file && !\JFile::exists($file))
		{
			// make sure we have a valid file path
			$file = VREHELPERS . '/tk_mail_tmpls/' . $file;
		}

		$this->templateFile = \JPath::clean($file);
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplate()
	{
		// copy items details in a local
		// variable for being used directly
		// within the template file
		$items = $this->items;

		if ($this->templateFile)
		{
			// use specified template file
			$file = $this->templateFile;
		}
		else
		{
			// get template file from configuration
			$file = \VREFactory::getConfig()->get('tkstockmailtmpl');

			// build template path
			$file = \JPath::clean(VREHELPERS . '/tk_mail_tmpls/' . $file);
		}

		// make sure the file exists
		if (!is_file($file))
		{
			// missing file, return empty string
			return '';
		}

		// start output buffering 
		ob_start();
		// include file to catch its contents
		include $file;
		// write template contents within a variable
		$content = ob_get_contents();
		// clear output buffer
		ob_end_clean();

		// free space
		unset($items);

		return $content;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldSend()
	{
		// make sure there is at least a product to notify
		return (bool) $this->items;
	}

	/**
	 * @inheritDoc
	 */
	final protected function createMail(array &$args)
	{
		// inject items within the arguments of the events
		$args[] = $this->items;

		// fetch subject
		$subject = \JText::sprintf('VRTKADMINLOWSTOCKSUBJECT', \VREFactory::getConfig()->getString('restname'));
			
		// fetch body
		$body = $this->getTemplate();

		// create mail instance
		return (new Mail)
			->setSubject($subject)
			->setBody($body);
	}

	/**
	 * Finds all the items having low stocks.
	 * 
	 * @param   array     $options  A configuration array.
	 *
	 * @return 	object[]  A list of items.
	 */
	public function getItems(array $options = [])
	{
		if (!is_null($this->items))
		{
			return $this->items;
		}

		if (\VREFactory::getConfig()->getBool('tkenablestock') == false)
		{
			// do not proceed in case the stock system is disabled
			return [];
		}

		$db = \JFactory::getDbo();

		// filter products only if we are not testing the mail
		if (empty($options['test']))
		{
			/**
			 * Make sure the product hasn't been notified yet.
			 *
			 * @since 1.8.4
			 */
			$having = "`product_stock_notified` = 0 AND (`products_in_stock` - `products_used`) <= `product_notify_below`";

			$start  = 0;
			$offset = null;
		}
		else
		{
			// obtain the first 5 products
			$having = "1";

			$start  = isset($options['start'])  ? (int) $options['start']  : 0;
			$offset = isset($options['offset']) ? (int) $options['offset'] : 5;
		}

		// get any reserved codes
		$reserved = \JHtml::fetch('vrehtml.status.find', 'code', ['takeaway' => 1, 'reserved' => 1]);
		
		if ($reserved)
		{
			$reserved = $db->qn('r.status') . ' IN (' . implode(',', array_map(array($db, 'q'), $reserved)) . ') AND';
		}
		else
		{
			$reserved = '';
		}

		// build query used to retrieve items with low stocks
		$query = "SELECT
			`e`.`id` AS `id_product`, `e`.`name` AS `product_name`,
			`o`.`id` AS `id_option`, `o`.`name` AS `option_name`, `o`.`stock_enabled` AS `option_stock_enabled`,
			`m`.`id` AS `id_menu`, `m`.`title` AS `menu_title`,

			IF (
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, 0, `o`.`id`
			) AS `group_option_id`,

			IF (
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, `e`.`stock_notified`, `o`.`stock_notified`
			) AS `product_stock_notified`,

			IF(
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, `e`.`notify_below`, `o`.`notify_below`
			) AS `product_notify_below`,

			IF(
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, `e`.`items_in_stock`, `o`.`items_in_stock`
			) AS `product_original_stock`,

			IF(
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, 
				(
					IFNULL(
						(
							SELECT SUM(`so`.`items_available`) 
							FROM `#__vikrestaurants_takeaway_stock_override` AS `so` 
							WHERE `so`.`id_takeaway_entry` = `e`.`id` AND `so`.`id_takeaway_option` IS NULL
						), `e`.`items_in_stock`
					)
				), (
					IFNULL(
						(
							SELECT SUM(`so`.`items_available`) 
							FROM `#__vikrestaurants_takeaway_stock_override` AS `so` 
							WHERE `so`.`id_takeaway_entry` = `e`.`id` AND `so`.`id_takeaway_option` = `o`.`id`
						), `o`.`items_in_stock`
					)
				)
			) AS `products_in_stock`,

			IF(
				`o`.`id` IS NULL OR `o`.`stock_enabled` = 0, 
				(
					IFNULL(
						(
							SELECT SUM(`i`.`quantity`)
							FROM `#__vikrestaurants_takeaway_reservation` AS `r` 
							LEFT JOIN `#__vikrestaurants_takeaway_res_prod_assoc` AS `i` ON `i`.`id_res` = `r`.`id`
							LEFT JOIN `#__vikrestaurants_takeaway_menus_entry_option` AS `io` ON `i`.`id_product_option` = `io`.`id`
							WHERE $reserved `i`.`id_product` = `e`.`id`
							AND (`o`.`id` IS NULL OR `io`.`stock_enabled` = 0)
						), 0
					)
				), (
					IFNULL(
						(
							SELECT SUM(`i`.`quantity`)
							FROM `#__vikrestaurants_takeaway_reservation` AS `r` 
							LEFT JOIN `#__vikrestaurants_takeaway_res_prod_assoc` AS `i` ON `i`.`id_res` = `r`.`id`
							WHERE $reserved `i`.`id_product` = `e`.`id` AND `i`.`id_product_option` = `o`.`id`
						), 0
					)
				)
			) AS `products_used`

			FROM
				`#__vikrestaurants_takeaway_menus_entry` AS `e`
			LEFT JOIN
				`#__vikrestaurants_takeaway_menus_entry_option` AS `o` ON `e`.`id` = `o`.`id_takeaway_menu_entry`
			LEFT JOIN
				`#__vikrestaurants_takeaway_menus` AS `m` ON `m`.`id` = `e`.`id_takeaway_menu` 
			GROUP BY
				`e`.`id`, `group_option_id`
			HAVING
				{$having}
			ORDER BY
				`m`.`ordering` ASC,
				(`products_in_stock` - `products_used`) ASC,
				`e`.`ordering` ASC,
				`o`.`ordering` ASC";

		$db->setQuery($query, $start, $offset);
		$rows = $db->loadObjectList();

		if (!$rows)
		{
			return [];
		}
			
		// get translator
		$translator = \VREFactory::getTranslator();

		$menu_ids    = [];
		$product_ids = [];
		$option_ids  = [];

		foreach ($rows as $item)
		{
			$menu_ids[]    = $item->id_menu;
			$product_ids[] = $item->id_product;

			if ($item->id_option && $item->option_stock_enabled)
			{
				$option_ids[] = $item->id_option;
			}
		}

		// pre-load menus translations
		$menuLang = $translator->load('tkmenu', array_unique($menu_ids), $this->options['lang']);
		// pre-load products translations
		$prodLang = $translator->load('tkentry', array_unique($product_ids), $this->options['lang']);
		// pre-load products options translations
		$optLang = $translator->load('tkentryoption', array_unique($option_ids), $this->options['lang']);

		$items = [];

		// iterate items and apply translationss
		foreach ($rows as $item)
		{
			// translate menu title for the given language
			$menu_tx = $menuLang->getTranslation($item->id_menu, $this->options['lang']);

			if ($menu_tx)
			{
				// inject translation within order item
				$item->menu_title = $menu_tx->title;
			}

			// translate product name for the given language
			$prod_tx = $prodLang->getTranslation($item->id_product, $this->options['lang']);

			if ($prod_tx)
			{
				// inject translation within order item
				$item->product_name = $prod_tx->name;
			}

			if ($item->id_option && $item->option_stock_enabled)
			{
				// translate product option name for the given language
				$opt_tx = $optLang->getTranslation($item->id_option, $this->options['lang']);

				if ($opt_tx)
				{
					// inject translation within order item
					$item->option_name = $opt_tx->name;
				}
			}

			// group by menu
			if (!isset($items[$item->id_menu]))
			{
				$menu = new \stdClass;
				$menu->id    = $item->id_menu;
				$menu->title = $item->menu_title;
				$menu->list  = [];

				$items[$item->id_menu] = $menu;
			}

			// fetch name
			$item->name = $item->product_name . ($item->option_name && $item->option_stock_enabled ? ' - ' . $item->option_name : '');

			// calculate remaining in stock
			$item->remaining = $item->products_in_stock - $item->products_used;

			// replicate product and variation PKs with the stock notation for BC
			$item->id_takeaway_entry  = $item->id_product;
			$item->id_takeaway_option = $item->id_option;

			// push item in list
			$items[$item->id_menu]->list[] = $item;
		}

		return $items;
	}
}
