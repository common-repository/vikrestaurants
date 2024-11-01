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

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants take-away controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerTakeaway extends VREControllerAdmin
{
	/**
	 * Adds the selected product within the cart.
	 *
	 * @return 	void
	 */
	public function addtocartajax()
	{
		$app = JFactory::getApplication();

		$data = [];
		$data['id_entry']  = $app->input->getUint('id_entry', 0);
		$data['id_option'] = $app->input->getInt('id_option', 0);
		$data['index'] 	   = $app->input->getInt('item_index', -1);
		
		// these parameters are provided by the tkadditem overlay
		$data['quantity'] = $app->input->getUint('quantity', 1);
		$data['notes']    = $app->input->getString('notes');
		$data['toppings'] = $app->input->get('topping', [], 'array');
		$data['units']    = $app->input->get('topping_units', [], 'array');

		// get take-away cart model
		$model = $this->getModel('tkcart');

		// try to add the provided item
		$response = $model->addItem($data);

		if (!$response)
		{
			// fetch last registered error message
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error->getMessage(), 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		// send response to caller
		$this->sendJSON($response);
	}

	/**
	 * Removes the selected item from the cart.
	 *
	 * @return 	void
	 */
	public function removefromcartajax()
	{
		$app = JFactory::getApplication();
		
		$index = $app->input->getUint('index');

		// get take-away cart model
		$model = $this->getModel('tkcart');

		// try to remove the item at the provided index
		$response = $model->removeItem($index);

		if (!$response)
		{
			// fetch last registered error message
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error->getMessage(), 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		// send response to caller
		$this->sendJSON($response);
	}

	/**
	 * Removes all the items that are currently stored
	 * within the user session.
	 *
	 * @return 	void
	 */
	public function emptycartajax()
	{
		/** @var E4J\VikRestaurants\TakeAway\Cart */
		$cart = $this->getModel('tkcart')->getCart();
		
		// remove all items stored within the cart	
		$cart->clear()->store();
			
		// return to the caller
		$this->sendJSON(1);
	}
}
