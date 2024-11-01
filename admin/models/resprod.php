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
 * VikRestaurants relation between restaurant product and reservation model.
 *
 * @since 1.9
 */
class VikRestaurantsModelResprod extends JModelVRE
{
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

		/** @var JModelLegacy */
		$prodModel = JModelVRE::getInstance('menusproduct');

		if (empty($data['id']) && empty($data['id_product']))
		{
			// try to save the product at runtime
			$data['id_product'] = $prodModel->save([
				'name'      => $data['name']   ?? '',
				'price'     => $data['price']  ?? 0,
				'id_tax'    => $data['id_tax'] ?? 0,
				'published' => 0,
				'hidden'    => 1,
			]);

			if (!$data['id_product'])
			{
				// something went wrong, propagate error
				$this->setError($prodModel->getError());
				return false;
			}
		}

		if (!empty($data['id_product']))
		{
			/** @var stdClass|null */
			$product = $prodModel->getItem((int) $data['id_product']);

			if (!$product)
			{
				// item not found...
				$this->setError(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
				return false;
			}

			if (empty($data['id_product_option']) && $product->options)
			{
				// take the first available option
				$data['id_product_option'] = $product->options[0]->id;
			}
			else if (!$product->options)
			{
				// always unset option ID
				$data['id_product_option'] = 0;
			}

			if (!isset($data['price']))
			{
				// price not provided, recover it from the default product
				$data['price'] = (float) $product->price;

				foreach ($product->options as $opt)
				{
					if ($opt->id == $data['id_product_option'])
					{
						// increase price by the cost of the selected variation
						$data['price'] += (float) $opt->inc_price;
					}
				}
			}

			if (!empty($data['charge']))
			{
				// charge specified, apply surcharge/discount to the base price
				$data['price'] = $data['price'] + (float) $data['charge'];
			}

			// set up item name
			$data['name'] = $product->name;

			foreach ($product->options as $opt)
			{
				if ($opt->id == $data['id_product_option'])
				{
					// append variation name
					$data['name'] .= ' - ' . $opt->name;
				}
			}
		}

		// attempt to save the record
		return parent::save($data);
	}

	/**
	 * Changes the reservation code for the specified record.
	 * 
	 * @param   int    $id    The product ID.
	 * @param   mixed  $code  Either an array/object containing the code details
	 *                        or the primary key of the code.
	 * 
	 * @return  bool   True on success, false otherwise.
	 */
	public function changeCode(int $id, $code)
	{
		if ((int) $id <= 0)
		{
			$this->setError(new Exception('Missing product ID', 400));
			return false;
		}

		if (is_numeric($code))
		{
			/** @var stdClass */
			$code = JModelVRE::getInstance('rescode')->getItem((int) $code, $blank = true);
		}

		$code = (array) $code;

		if (!isset($code['id']))
		{
			$this->setError(new Exception('Missing reservation code ID', 400));
			return false;
		}

		// save product first
		$saved = $this->save([
			'id'      => (int) $id,
			'rescode' => $code['id'],
		]);

		if (!$saved)
		{
			// unable to save the product
			return false;
		}

		if ($code['id'])
		{
			/** @var JModelLegacy */
			$resCodeModel = JModelVRE::getInstance('rescodeorder');

			// try to update the history of the product
			$saved = $resCodeModel->save([
				'group'      => 3,
				'id_order'   => (int) $id,
				'id_rescode' => (int) $code['id'],
				'notes'      => $code['notes'] ?? null,
			]);

			if (!$saved)
			{
				// propagate encountered error
				$this->setError($resCodeModel->getError());
				return false;
			}
		}

		return true;
	}
}
