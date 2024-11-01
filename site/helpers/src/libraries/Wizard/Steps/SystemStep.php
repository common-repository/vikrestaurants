<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Wizard\Steps;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Wizard\WizardStep;

/**
 * Implement the wizard step used to setup the basic
 * settings of the global configuration.
 *
 * @since 1.9
 */
class SystemStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMENUCONFIG');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-cogs"></i>';
	}

	/**
	 * @inheritDoc
	 */
	public function getGroup()
	{
		// belongs to GLOBAL group
		return \JText::translate('VRMENUTITLEHEADER4');
	}

	/**
	 * @inheritDoc
	 */
	protected function doExecute($data)
	{
		$config = \VREFactory::getConfig();

		if ($currency = $data->get('currency'))
		{
			// get supported currencies
			$map = $this->getCurrencies();

			// make sure the currency exists
			if (!isset($map[$currency]))
			{
				return false;
			}

			$format = $map[$currency];

			// set currency parameters
			$config->set('currencyname', $currency);
			$config->set('currencysymb', $format['symbol']);
			$config->set('symbpos', $format['position']);
			$config->set('currdecimaldig', (int) $format['decimals']);
			$config->set('currthousandssep', $format['separator'] == '.' ? ',' : '.');
			$config->set('currdecimalsep', $format['separator']);
		}
		else
		{
			// set specified custom currency
			$config->set('currencyname', $data->get('currencyname'));
			$config->set('currencysymb', $data->get('currencysymb'));
		}

		$config->set('restname', $data->get('restname'));
		$config->set('adminemail', $data->get('adminemail'));

		if (!$config->get('senderemail'))
		{
			// set sender equals to admin e-mail if empty
			$config->set('senderemail', $data->get('adminemail'));
		}

		// set date/time format
		$config->set('dateformat', $data->get('dateformat'));
		$config->set('timeformat', $data->get('timeformat') ? 'H:i' : 'h:i A');

		return true;
	}

	/**
	 * Returns an associative array containing the most common currencies
	 * and the related formatting information.
	 *
	 * @return  array
	 */
	public function getCurrencies()
	{
		return [
			'EUR' => [
				'currency'  => 'Euro',
				'symbol'    => '€',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => ',',
			],
			'USD' => [
				'currency'  => 'US Dollar',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'GBP' => [
				'currency'  => 'Pound Sterling',
				'symbol'    => '‎£',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'JPY' => [
				'currency'  => 'Yen',
				'symbol'    => '¥',
				'position'  => 2,
				'decimals'  => 0,
				'separator' => '.',
			],
			'ARS' => [
				'currency'  => 'Argentine Peso',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => ',',
			],
			'AUD' => [
				'currency'  => 'Australian Dollar',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'BRL' => [
				'currency'  => 'Brazilian Real',
				'symbol'    => 'R$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => ',',
			],
			'CAD' => [
				'currency'  => 'Canadian Dollar',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'CLP' => [
				'currency'  => 'Chilean Peso',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'CNY' => [
				'currency'  => 'Yuan Renminbi',
				'symbol'    => '¥',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'COP' => [
				'currency'  => 'Colombian Peso',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'CZK' => [
				'currency'  => 'Czech Koruna',
				'symbol'    => 'Kč',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => ',',
			],
			'DKK' => [
				'currency'  => 'Danish Krone',
				'symbol'    => 'kr.',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => ',',
			],
			'HKD' => [
				'currency'  => 'Hong Kong Dollar',
				'symbol'    => 'HK$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'HUF' => [
				'currency'  => 'Hungarian Forint',
				'symbol'    => 'Ft',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => ',',
			],
			'INR' => [
				'currency'  => 'Indian Rupee',
				'symbol'    => '₹',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'ILS' => [
				'currency'  => 'New Israeli Shekel',
				'symbol'    => '₪',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => ',',
			],
			'KRW' => [
				'currency'  => 'Won',
				'symbol'    => '₩',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'MYR' => [
				'currency'  => 'Malaysian Ringgit',
				'symbol'    => 'RM',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'MXN' => [
				'currency'  => 'Mexican Peso',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'MAD' => [
				'currency'  => 'Moroccan Dirham',
				'symbol'    => '.د.م.',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => '.',
			],
			'NZD' => [
				'currency'  => 'New Zealand Dollar',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'NOK' => [
				'currency'  => 'Norwegian Krone',
				'symbol'    => 'kr',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'PHP' => [
				'currency'  => 'Philippine Peso',
				'symbol'    => '₱',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'PLN' => [
				'currency'  => 'Zloty',
				'symbol'    => 'zł',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => ',',
			],
			'RUB' => [
				'currency'  => 'Russian Ruble',
				'symbol'    => 'p.',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => ',',
			],
			'SAR' => [
				'currency'  => 'Saudi Riyal',
				'symbol'    => '﷼',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => '.',
			],
			'SGD' => [
				'currency'  => 'Singapore Dollar',
				'symbol'    => '$',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'ZAR' => [
				'currency'  => 'Rand',
				'symbol'    => 'R',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'SEK' => [
				'currency'  => 'Swedish Krona',
				'symbol'    => 'kr',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => ',',
			],
			'CHF' => [
				'currency'  => 'Swiss Franc',
				'symbol'    => 'fr.',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => ',',
			],
			'TWD' => [
				'currency'  => 'New Taiwan Dollar',
				'symbol'    => '元',
				'position'  => 2,
				'decimals'  => 2,
				'separator' => '.',
			],
			'THB' => [
				'currency'  => 'Baht',
				'symbol'    => '฿',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => '.',
			],
			'TRY' => [
				'currency'  => 'Turkish Lira',
				'symbol'    => '₺',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => '.',
			],
			'VND' => [
				'currency'  => 'Dong',
				'symbol'    => '₫',
				'position'  => 1,
				'decimals'  => 2,
				'separator' => ',',
			],
		];
	}
}
