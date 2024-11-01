<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters\Update1_9;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Update\UpdateRule;

/**
 * Before the 1.9 version, the default taxes were specified within the configuration of the program, with
 * the possibility to override such value from the details page of a take-away menu. In order to preserve
 * the same behavior, we need to detect all the existing tax ratios and manually create the related rules.
 *
 * @since 1.9
 */
class TaxesCreator extends UpdateRule
{
	/**
	 * @inheritDoc
	 */
	protected function run($parent)
	{
		try
		{
			$this->createDefaultTaxes();
		}
		catch (\Exception $e)
		{
			// an error has occurred
			\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Scans all the existing taxes and creates an apposite rule for each of them.
	 * 
	 * @return  void
	 * 
	 * @throws  \Exception
	 */
	private function createDefaultTaxes()
	{
		$config = \VREFactory::getConfig();

		$taxes = [];

		// register default taxes for the restaurant
		$taxId = $this->createTaxRule([
			'ratio'    => $config->getFloat('taxesratio', 0),
			'excluded' => $config->getBool('usetaxes', false),
		]);

		if ($taxId)
		{
			$taxes[] = $taxId;

			// register as default rule for the restaurant
			$config->set('deftax', $taxId);
		}

		// register default taxes for the take-away
		$taxId = $this->createTaxRule([
			'ratio'    => $config->getFloat('tktaxesratio', 0),
			'excluded' => $config->getBool('tkusetaxes', false),
		]);

		if ($taxId)
		{
			$taxes[] = $taxId;

			// register as default rule for the take-away
			$config->set('tkdeftax', $taxId);
		}

		$db = \JFactory::getDbo();

		// load all the take-away menus that override the default rule
		$query = $db->getQuery(true)
			->select($db->qn(['id', 'taxes_amount']))
			->from($db->qn('#__vikrestaurants_takeaway_menus'))
			->where($db->qn('taxes_type') . ' = 1');

		$db->setQuery($query);

		foreach ($db->loadObjectList() as $menu)
		{
			// register tax override for the take-away menu
			$taxId = $this->createTaxRule([
				'ratio'    => $menu->taxes_amount,
				'excluded' => $config->getBool('tkusetaxes', false),
			]);

			if ($taxId)
			{
				// automatically assign to all the products of the menu the newly created tax rule
				$query = $db->getQuery(true)
					->update($db->qn('#__vikrestaurants_takeaway_menus_entry'))
					->set($db->qn('id_tax') . ' = ' . (int) $taxId)
					->where($db->qn('id_takeaway_menu') . ' = ' . (int) $menu->id);

				$db->setQuery($query);
				$db->execute();

				$taxes[] = $taxId;
			}
		}
	}

	/**
	 * Defines a new tax rule.
	 *
	 * @return 	int|bool  The tax ID on success, false otherwise.
	 * 
	 * @throws  \Exception
	 */
	private function createTaxRule($data)
	{
		if (empty($data['ratio']))
		{
			// invalid ratio, ignore tax creation
			return false;
		}

		// set up tax name
		$taxName = sprintf(
			'%s %s%%',
			empty($data['excluded']) ? 'VAT' : 'Tax',
			$data['ratio']
		);

		$db = \JFactory::getDbo();

		// make sure we haven't created yet the same rule
		$query = $db->getQuery(true)
			->select(1)
			->from($db->qn('#__vikrestaurants_tax'))
			->where($db->qn('name') . ' = ' . $db->q($taxName));

		$db->setQuery($query);
		$db->execute();

		if ($db->getNumRows())
		{
			// tax rule manually created, do not need to go ahead
			return false;
		}

		$taxModel = \JModelVRE::getInstance('tax');

		// register default taxes
		$taxId = $taxModel->save([
			'name'        => $taxName,
			'description' => 'Automatically created while updating to the latest version',
		]);

		if (!$taxId)
		{
			// get latest registered error
			$error = $taxModel->getError($index = null, $string = true);

			if (!$error)
			{
				// define default error message
				$error = 'Unable to define the tax rule. Create it manually from <a href="index.php?option=com_vikrestaurants&task=tax.add">HERE</a>.';
			}

			if ($error instanceof \Exception)
			{
				// wrap error in an exception
				throw new \RuntimeException($error);
			}

			// propagate error
			throw $error;
		}

		$taxRuleModel = \JModelVRE::getInstance('taxrule');

		// register tax rule
		$taxRuleModel->save([
			'id_tax'   => $taxId,
			'name'     => empty($data['excluded']) ? 'VAT' : 'Tax',
			'operator' => empty($data['excluded']) ? 'Vat' : 'Add',
			'amount'   => abs((float) $data['ratio']),
			'apply'    => 1,
		]);

		return $taxId;
	}
}
