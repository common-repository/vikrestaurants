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
 * VikRestaurants operator kitchen controller.
 *
 * @since 1.8
 */
class VikRestaurantsControllerOpkitchen extends VREControllerAdmin
{
	/**
	 * AJAX end-point used to change the status code of a product.
	 *
	 * @return 	void
	 */
	public function changecodeajax()
	{
		$app = JFactory::getApplication();

		/**
		 * Added token validation.
		 *
		 * @since 1.9
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// get current operator
		$operator = VikRestaurants::getOperator();

		if (!$operator)
		{
			// raise error, not authorised to access private area
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}
		
		$id_order   = $app->input->get('id', 0, 'uint');
		$id_rescode = $app->input->get('id_code', 0, 'uint');
		$notes      = $app->input->get('notes', null, 'string');
		$section    = $app->input->get('section', 'restaurant', 'string');

		if ($notes === '')
		{
			// prevent the system from resetting the existing notes in case of update
			$notes = null;
		}

		$args = [
			'id'    => $id_rescode,
			'notes' => $notes,
		];

		// check if the operator can edit the order
		if (!$operator->canSeeAll() && !$operator->canAssign($id_order))
		{
			// raise AJAX error, not authorised to edit records
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		/** @var JModelLegacy */
		$model = $this->getModel($section === 'takeaway' ? 'tkresprod' : 'resprod');

		// attempt to change the reservation code
		$saved = $model->changeCode($id_order, $args);

		if (!$saved)
		{
			// obtain the latest error
			$error = $model->getError();

			if (!$error instanceof Exception)
			{
				// create an exception for a better ease of use
				$error = new Exception($error ?: 'Error', 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}
		
		// get reservation codes details
		$rescode = JHtml::fetch('vikrestaurants.rescode', $id_rescode, $group = 3);

		$this->sendJSON($rescode);
	}
}
