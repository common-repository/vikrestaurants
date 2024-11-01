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
 * Implement the wizard step used to create the products
 * of the restaurant.
 *
 * @since 1.9
 */
class RestaurantProductsStep extends WizardStep
{
	/**
	 * @inheritDoc
	 */
	public function getID()
	{
		return 'products';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle()
	{
		return \JText::translate('VRMENUMENUSPRODUCTS');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription()
	{
		return \JText::translate('VRE_WIZARD_STEP_PRODUCTS_DESC');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon()
	{
		return '<i class="fas fa-hamburger"></i>';
	}

	/**
	 * @inheritDoc
	 */
	public function getGroup()
	{
		// belongs to RESTAURANT group
		return \JText::translate('VRMENUTITLEHEADER1');
	}

	/**
	 * @inheritDoc
	 */
	public function isCompleted()
	{
		// the step is completed after creating at least a product
		return (bool) $this->getProducts();
	}

	/**
	 * @inheritDoc
	 */
	public function getExecuteButton()
	{
		// use by default the standard save button
		return '<a href="index.php?option=com_vikrestaurants&task=menusproduct.add" class="btn btn-success">' . \JText::translate('VRNEW') . '</a>';
	}

	/**
	 * @inheritDoc
	 */
	public function canIgnore()
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function isIgnored()
	{
		// get sections dependency
		$sections = $this->getDependency('sections');

		// make sure the restaurant section is enabled
		if ($sections && $sections->isRestaurant() == false)
		{
			// restaurant disabled, auto-ignore this step
			return true;
		}

		// otherwise lean on parent method
		return parent::isIgnored();
	}

	/**
	 * Returns a list of created products.
	 *
	 * @return  array  A list of products.
	 */
	public function getProducts()
	{
		static $products = null;

		// get products only once
		if (is_null($products))
		{
			$db = \JFactory::getDbo();

			$q = $db->getQuery(true)
				->select($db->qn(['name', 'published']))
				->from($db->qn('#__vikrestaurants_section_product'))
				->order($db->qn('hidden') . ' ASC')
				->order($db->qn('ordering') . ' ASC');

			$db->setQuery($q);
			$products = $db->loadObjectList();
		}

		return $products;
	}
}
