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
 * VikRestaurants take-away order status tracker view.
 *
 * @since 1.7
 */
class VikRestaurantsViewtrackorder extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();
		
		$oid = $app->input->get('oid', 0, 'uint');
		$sid = $app->input->get('sid', '', 'alnum');
		$tid = $app->input->get('tid', 1, 'uint') == 0 ? 1 : 2;

		try
		{
			if ($tid == 1)
			{
				// get restaurant reservation
				$order = VREOrderFactory::getReservation($oid, null, ['sid' => $sid]);
			}
			else
			{
				// get take-away order
				$order = VREOrderFactory::getOrder($oid, null, ['sid' => $sid]);
			}

			// get order history
			$history = $order->history;
		}
		catch (Exception $e)
		{
			// in case the order doesn't exist, an exception will be thrown
			$order   = null;
			$history = [];
		}

		if ($history)
		{
			// group statuses by day
			$tmp = [];

			foreach ($history as $status)
			{
				// get day timestamp at midnight
				$day = strtotime('00:00:00', $status->createdon);

				if (empty($tmp[$day]))
				{
					// create group only if not exists
					$tmp[$day] = [];
				}

				$tmp[$day][] = $status;
			}

			// overwrite history
			$history = $tmp;
		}

		$this->history = $history;
		$this->order   = $order;
		$this->type    = $tid;

		// extend pathway for breadcrumbs module
		$this->extendPathway($app);
		
		// display the template
		parent::display($tpl);
	}

	/**
	 * Extends the pathway for breadcrumbs module.
	 *
	 * @param 	mixed 	$app  The application instance.
	 *
	 * @return 	void
	 *
	 * @since 	1.9
	 */
	protected function extendPathway($app)
	{
		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		// Make sure the order page is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Order > [ORDNUM]-[ORDKEY]
		if ($last && strpos($last->link, '&view=order') === false && !empty($this->order))
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=order&ordnum=' . $this->order->id . '&ordkey=' . $this->order->sid;
			$pathway->addItem($this->order->id . '-' . $this->order->sid, $link);
		}

		// register tracking link into the Breadcrumb
		$link = 'index.php?option=com_vikrestaurants&view=trackorder&oid=' . $this->order->id . '&sid=' . $this->order->sid . '&tid=' . $this->type;
		$pathway->addItem(JText::translate('VRE_ORDER_TRACK_BREADCRUMB'), $link);
	}
}
