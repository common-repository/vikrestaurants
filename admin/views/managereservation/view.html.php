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
 * VikRestaurants restaurant reservation management view.
 *
 * @since 1.0
 */
class VikRestaurantsViewmanagereservation extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		
		$ids  = $app->input->getUint('cid', []);
		$type = $ids ? 'edit' : 'new';

		/** @var JModelLegacy */
		$model = JModelVRE::getInstance('reservation');

		/** @var stdClass */
		$this->reservation = $model->getItem($ids ? $ids[0] : 0, $blank = true);

		if ($this->reservation->closure)
		{
			// use CLOSURE management layout
			$this->setLayout('closure');
		}
		else
		{
			/** @var object[] */
			$this->reservation->items = $this->reservation->id ? $model->getBillItems($this->reservation->id) : [];
		}

		// use reservation data stored in user state
		$this->injectUserStateData($this->reservation, 'vre.reservation.data');

		// set the toolbar
		$this->addToolBar($type);

		// prepare reservation filters
		$this->filters = [
			'date'     => $this->reservation->date,
			'hourmin'  => $this->reservation->hourmin,
			'people'   => $this->reservation->people,
			'staytime' => $this->reservation->stay_time,
			'id_res'   => $this->reservation->id,
		];

		/** @var JModelLegacy */
		$this->mapModel = JModelVRE::getInstance('map');

		if (!$this->reservation->closure)
		{	
			/**
			 * Retrieve custom fields for the restaurant section by using the related helper.
			 * @var E4J\VikRestaurants\CustomFields\FieldsCollection
			 *
			 * @since 1.9
			 */
			$this->customFields = E4J\VikRestaurants\CustomFields\FieldsCollection::getInstance()
				->filter(new E4J\VikRestaurants\CustomFields\Filters\RestaurantGroupFilter)
				->filter(new E4J\VikRestaurants\CustomFields\Filters\SeparatorFilter($exclude = true))
				->filter(new E4J\VikRestaurants\CustomFields\Filters\RequiredCheckboxFilter($exclude = true));

			/** @var object[] */
			$this->menus = VikRestaurants::getAllAvailableMenusOn($this->filters, $choosable = true);

			// retrieve customer details
			if ($this->reservation->id_user > 0)
			{
				/** @var object|null */
				$this->customer = VikRestaurants::getCustomer($this->reservation->id_user);
			}
			else
			{
				$this->customer = null;
			}

			// fetch all the existing products
			$query = $db->getQuery(true)
				->select($db->qn(['p.id', 'p.name', 'p.price', 'p.image', 'p.description']))
				->from($db->qn('#__vikrestaurants_section_product', 'p'))
				->order($db->qn('p.hidden') . ' ASC')
				->order($db->qn('p.ordering') . ' ASC');

			$db->setQuery($query);
			$this->allProducts = $db->loadObjectList();
		}

		$this->selectedRoom = null;

		if (!empty($this->reservation->tables))
		{
			// auto-select the room of the first assigned table
			$this->selectedRoom = $this->reservation->tables[0]->id_room;
		}
		else if ($this->reservation->id_table > 0)
		{
			// recover the ID of the room where the selected table is placed
			$this->selectedRoom = JModelVRE::getInstance('table')->getItem($this->reservation->id_table, true)->id_room;
		}
		
		// register return URL
		$this->returnTask = $app->input->get('from');

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

		// display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @param 	string  $type  The view type ('edit' or 'new').
	 *
	 * @return 	void
	 */
	private function addToolBar($type)
	{
		// add menu title and some buttons to the page
		if ($type == 'edit')
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLEEDITRESERVATION'), 'vikrestaurants');
		}
		else
		{
			JToolbarHelper::title(JText::translate('VRMAINTITLENEWRESERVATION'), 'vikrestaurants');
		}
		
		$user = JFactory::getUser();
		
		if (!$this->reservation->closure)
		{
			if ($user->authorise('core.edit', 'com_vikrestaurants')
				|| $user->authorise('core.create', 'com_vikrestaurants'))
			{
				JToolbarHelper::apply('reservation.save', JText::translate('VRSAVE'));
				JToolbarHelper::save('reservation.saveclose', JText::translate('VRSAVEANDCLOSE'));
			}

			if ($user->authorise('core.edit', 'com_vikrestaurants')
				&& $user->authorise('core.create', 'com_vikrestaurants'))
			{
				JToolbarHelper::save2new('reservation.savenew', JText::translate('VRSAVEANDNEW'));
			}
		}
		else
		{
			// save CLOSURE
			if ($user->authorise('core.edit', 'com_vikrestaurants'))
			{
				JToolbarHelper::apply('closure.save', JText::translate('VRSAVE'));
			}
		}
		
		JToolbarHelper::cancel('reservation.cancel', $type == 'edit' ? 'JTOOLBAR_CLOSE' : 'JTOOLBAR_CANCEL');
	}
}
