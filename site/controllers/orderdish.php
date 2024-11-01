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
VRELoader::import('library.dishes.cart');

/**
 * VikRestaurants dishes ordering controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerOrderdish extends VREControllerAdmin
{
	/**
	 * AJAX end-point used to access the creation page of a new record.
	 *
	 * @return 	void
	 */
	public function add()
	{
		$app = JFactory::getApplication();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		$id_item = $app->input->get('id', 0, 'uint');
		$index   = $app->input->get('index', -1, 'int');

		try
		{
			// first of all, check reservation permissions
			$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

			if ($index == -1)
			{
				// create new item instance
				$item = new E4J\VikRestaurants\OrderDishes\Item($id_item);
			}
			else
			{
				// get current cart instance
				$cart = E4J\VikRestaurants\OrderDishes\Cart::getInstance($reservation->id);

				// get item from cart
				$item = $cart->getItemAt($index);

				if (!$item)
				{
					// item not found
					throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
				}
			}

			// make sure the item is still writable
			if (!$item->isWritable())
			{
				throw new Exception(JText::translate('VRTKCARTDISHCANTEDIT'), 403);
			}
		}
		catch (Exception $e)
		{
			// catch exception and raise error safely
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
		}

		// prepare display data
		$data = [
			'index'       => $index,
			'item'        => $item,
			'reservation' => $reservation,
		];

		// render layout of the form used to insert/update a dish
		$html = JLayoutHelper::render('orderdish.popup', $data);

		$this->sendJSON(json_encode($html));
	}

	/**
	 * AJAX end-point used to insert/update a cart item.
	 *
	 * @return 	void
	 */
	public function addcart()
	{
		$app = JFactory::getApplication();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		$id_item = $app->input->get('id', 0, 'uint');
		$index   = $app->input->get('index', -1, 'int');

		try
		{
			// first of all, check reservation permissions
			$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

			// make sure the user can actually order the food
			if (!VikRestaurants::canUserOrderFood($reservation, $errmsg))
			{
				// not allowed to order further dishes
				throw new Exception($errmsg ? $errmsg : 'Error', 403);
			}

			// get current cart instance
			$cart = E4J\VikRestaurants\OrderDishes\Cart::getInstance($reservation->id);

			if ($index == -1)
			{
				// create new item instance
				$item = new E4J\VikRestaurants\OrderDishes\Item($id_item);
			}
			else
			{
				// get item from cart
				$item = $cart->getItemAt($index);

				if (!$item)
				{
					// item not found
					throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
				}
			}

			// make sure the item is still writable
			if (!$item->isWritable())
			{
				throw new Exception(JText::translate('VRTKCARTDISHCANTEDIT'), 403);
			}
		}
		catch (Exception $e)
		{
			// catch exception and raise error safely
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
		}

		// set item notes
		$item->setAdditionalNotes($app->input->getString('notes', ''));
		// set variation
		$item->setVariation($app->input->getUint('id_product_option', 0));
		// set serving number
		$item->setServingNumber($app->input->getUint('serving_number', 0));

		// set item quantity
		$quantity = $app->input->getUint('quantity', 0);
		$item->setQuantity($quantity);

		if ($index == -1)
		{
			// try to look for an equal item already stored in the cart
			$index = $cart->indexOf($item);

			if ($index == -1)
			{
				// push item within the cart
				$cart->addItem($item);
			}
			else
			{
				// item found, get it from cart
				$item = $cart->getItemAt($index);

				// increase quantity
				$item->add($quantity);
			}
		}

		// save cart
		$cart->store();

		// build response data
		$response = new stdClass;
		$response->total    = $cart->getTotalCost();
		$response->cartHTML = JLayoutHelper::render('orderdish.cart', [
			'cart'        => $cart,
			'reservation' => $reservation,
		]);

		$this->sendJSON($response);
	}

	/**
	 * AJAX end-point used to delete a cart item.
	 *
	 * @return 	void
	 */
	public function removecart()
	{
		$app = JFactory::getApplication();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		$index = $app->input->get('index', 0, 'uint');

		try
		{
			// first of all, check reservation permissions
			$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

			// get current cart instance
			$cart = E4J\VikRestaurants\OrderDishes\Cart::getInstance($reservation->id);

			// get item from cart
			$item = $cart->getItemAt($index);

			if (!$item)
			{
				// item not found
				throw new Exception(JText::translate('VRTKCARTROWNOTFOUND'), 404);
			}

			// make sure the item is still writable
			if (!$item->isWritable() || $reservation->bill_closed)
			{
				throw new Exception(JText::translate('VRTKCARTDISHCANTEDIT'), 403);
			}
		}
		catch (Exception $e)
		{
			// catch exception and raise error safely
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
		}

		// permanently remove item from cart
		$cart->removeItemAt($index, $item->getQuantity());

		// save cart
		$cart->store();

		// build response data
		$response = new stdClass;
		$response->total    = $cart->getTotalCost();
		$response->cartHTML = JLayoutHelper::render('orderdish.cart', [
			'cart'        => $cart,
			'reservation' => $reservation,
		]);

		$this->sendJSON($response);
	}

	/**
	 * AJAX end-point used to transmit to the kitchen
	 * all the pending dishes.
	 *
	 * @return 	void
	 */
	public function transmit()
	{
		$app = JFactory::getApplication();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		try
		{
			// first of all, check reservation permissions
			$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);
		}
		catch (Exception $e)
		{
			// catch exception and raise error safely
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
		}

		// get current cart instance
		$cart = E4J\VikRestaurants\OrderDishes\Cart::getInstance($reservation->id);

		// transmit dishes
		$cart->transmit();

		// save cart
		$cart->store();

		// build response data
		$response = new stdClass;
		$response->total    = $cart->getTotalCost();
		$response->cartHTML = JLayoutHelper::render('orderdish.cart', [
			'cart'        => $cart,
			'reservation' => $reservation,
		]);

		$this->sendJSON($response);
	}

	/**
	 * AJAX end-point used to close the bill of a reservation.
	 *
	 * @return 	void
	 *
	 * @since 	1.8.1
	 */
	public function closebill()
	{
		$app = JFactory::getApplication();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		try
		{
			// first of all, check reservation permissions
			$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);
		}
		catch (Exception $e)
		{
			// catch exception and raise error safely
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($e->getCode(), $e->getMessage());
		}

		// get current cart instance
		$cart = E4J\VikRestaurants\OrderDishes\Cart::getInstance($reservation->id);

		if (!$reservation->bill_closed)
		{
			// iterate items list
			foreach ($cart->getItemsList() as $item)
			{
				// check if we have a volatile dish
				if ($item->getRecordID() == 0)
				{
					// delete item before closing the bill
					$cart->removeItem($item, $item->getQuantity());
				}
			}

			// save cart
			$cart->store();

			// get reservation code able to close the bill
			$id_code = JHtml::fetch('vikrestaurants.rescoderule', 'closebill', 1);

			// make sure the code exists
			if ($id_code)
			{
				$this->getModel('reservation')->changeCode($reservation->id, $id_code);
			}
		}

		// build response data
		$response = new stdClass;
		$response->total    = $cart->getTotalCost();
		$response->cartHTML = JLayoutHelper::render('orderdish.cart', [
			'cart'        => $cart,
			'reservation' => $reservation,
		]);

		$this->sendJSON($response);
	}

	/**
	 * Task used to select a payment method in order to 
	 * complete the payment after closing the bill.
	 *
	 * @return 	void
	 *
	 * @since 	1.8.1
	 */
	public function paynow()
	{
		$app = JFactory::getApplication();

		$oid = $app->input->get('ordnum', 0, 'uint');
		$sid = $app->input->get('ordkey', '', 'alnum');

		$itemid = $app->input->get('Itemid', null, 'uint');
		$itemid = $itemid ? '&Itemid=' . $itemid : '';

		// first of all, check reservation permissions
		$reservation = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);

		// in case the reservation doesn't exist, an exception will be thrown

		$this->setRedirect(JRoute::rewrite('index.php?option=com_vikrestaurants&view=orderdishes&ordnum=' . $oid . '&ordkey=' . $sid . $itemid, false));

		if (!$reservation->bill_closed)
		{
			// bill not yet closed
			return false;
		}

		$id_payment = $app->input->getUint('id_payment');

		/** @var E4J\VikRestaurants\Collection\Item[] */
		$payments = E4J\VikRestaurants\Payment\PaymentsCollection::getInstance()
			->filter(new E4J\VikRestaurants\Payment\Filters\RestaurantGroupFilter)
			->filter(new E4J\VikRestaurants\Payment\Filters\PublishedFilter)
			->filter(new E4J\VikRestaurants\Payment\Filters\TrustedCustomerFilter('restaurant'))
			->filter(new E4J\VikRestaurants\Payment\Filters\PluginAvailabilityFilter('restaurant'))
			->filter(new E4J\VikRestaurants\Collection\Filters\NumberFilter('id', $id_payment, '='));

		if (!$payments->count())
		{
			// the selected payment does not exist
			$app->enqueueMessage(JText::translate('VRERRINVPAYMENT'), 'error');
			return false;
		}

		/** @var JModelLegacy */
		$model = $this->getModel('reservation');

		// change payment method
		$saved = $model->save([
			'id'         => $reservation->id,
			'id_payment' => $id_payment,
		]);

		if (!$saved)
		{
			$error = $model->getError($last = null, $string = true);
			$app->enqueueMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error), 'error');
			return false;
		}

		// fetch any specified gratuity
		$tip_amount = abs($app->input->getFloat('gratuity', 0));

		if ($app->input->getBool('ceiltip'))
		{
			// round up bill total
			$ceil = ceil($reservation->bill_value);

			// add difference to tip amount
			$tip_amount += $ceil - $reservation->bill_value;
		}

		if ($tip_amount > 0)
		{
			// register tip
			$model->addTip($reservation->id, $tip_amount);
		}

		// go to reservation summary page
		$this->setRedirect(JRoute::rewrite('index.php?option=com_vikrestaurants&view=reservation&ordnum=' . $oid . '&ordkey=' . $sid . $itemid, false));
		return true;
	}

	/**
	 * Landing url of a QR code scan to start the ordering process.
	 * 
	 * @return  void
	 * 
	 * @since   1.9
	 */
	public function start()
	{
		$app = JFactory::getApplication();

		$itemid = $app->input->getUint('Itemid');

		// get selected table
		$table = $app->input->getAlnum('table', '');

		// create orderdish model
		$model = $this->getModel();

		// resolve QR code and obtain the matching reservation
		$reservation = $model->resolveQR($table);

		if (!$reservation)
		{
			// get last registered error message
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Unknown error', 500);
			}

			throw $error;
		}

		// get pin provided by the user
		$pin = $app->input->getAlnum('pin', '');

		$error = null;

		if ($pin && $reservation->pinattempts < 3)
		{
			// validate session token first
			if (!JSession::checkToken('post'))
			{
				// token not found
				throw new Exception(JText::translate('JINVALID_TOKEN'), 401);
			}

			// validate provided pin against the one generated for the reservation
			if (!strcasecmp($pin, $reservation->pin))
			{
				// pin correct, auto-redirect to the order dishes page
				$this->setRedirect(
					JRoute::rewrite(
						sprintf(
							'index.php?option=com_vikrestaurants&view=orderdishes&ordnum=%d&ordkey=%s%s',
							$reservation->id,
							$reservation->sid,
							$itemid ? '&Itemid=' . $itemid : ''
						),
						false
					)
				);
			}
			else
			{
				// increase the number of failed attempts
				$this->getModel('reservation')->wrongPin($reservation);

				// pin incorrect, self redirect to ignore the current post data
				$this->setRedirect(
					JRoute::rewrite(
						sprintf(
							'index.php?option=com_vikrestaurants&task=orderdish.start&invalid=1&table=%s%s',
							$table,
							$itemid ? '&Itemid=' . $itemid : ''
						),
						false
					)
				);
			}
		}
		else
		{
			// pin not provided, display a prompt to enter the pin
			echo JLayoutHelper::render('orderdish.pinprompt', [
				'reservation' => $reservation,
				'table'       => $table,
				'error'       => $app->input->getBool('invalid', false),
				'itemid'      => $itemid,
			]);
		}
	}
}
