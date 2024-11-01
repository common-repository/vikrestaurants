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
 * VikRestaurants menu detais view.
 * Displays the details of the menus
 * that have been selected.
 *
 * @since 1.5
 */
class VikRestaurantsViewmenudetails extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{	
		$app = JFactory::getApplication();
		$dbo = JFactory::getDbo();
		
		// get menu ID from request
		$id_menu = $app->input->get('id', 0, 'uint');
		
		/** @var JModelLegacy */
		$model = JModelVRE::getInstance('menudetails');

		// fetch menu details
		$this->menu = $model->getMenu($id_menu);

		if (!$this->menu)
		{
			// fetch registered error
			$error = $model->getError(null, false);

			if (!$rror instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			throw $error;
		}

		/**
		 * Check if the menu is printable.
		 *
		 * @since 1.7.4
		 */
		$this->isPrintable = $app->input->getInt('printable_menu', null);

		if (is_null($this->isPrintable) || $this->isPrintable == -1)
		{
			// check for parent argument
			$this->isPrintable = $app->getUserState('vre.menuslist.printable', false);
		}
		else
		{
			$this->isPrintable = (bool) $this->isPrintable;
		}

		// auto-print the menu details in case it is possible to
		// print them and in case of blank template
		if ($this->isPrintable && $app->input->get('tmpl') == 'component')
		{
			JHtml::fetch('vrehtml.sitescripts.winprint', 256);
		}

		$this->itemid = $app->input->get('Itemid', null, 'uint');

		// prepare page content
		VikRestaurants::prepareContent($this);

		// extend pathway for breadcrumbs module
		$this->extendPathway($app);
		
		// display the template
		parent::display($tpl);
	}

	/**
	 * Extends the pathway for breadcrumbs module.
	 *
	 * @param   mixed  $app  The application instance.
	 *
	 * @return  void
	 *
	 * @since   1.9
	 */
	protected function extendPathway($app)
	{
		$pathway = $app->getPathway();
		$items   = $pathway->getPathway();
		$last 	 = end($items);

		$name = $this->menu->name;
		$id   = $this->menu->id;

		// Make sure this menu is not a menu item, otherwise
		// the pathway will display something like:
		// Home > Menu > Item > Item
		if ($last && strpos($last->link, '&id=' . $id) === false)
		{
			// register link into the Breadcrumb
			$link = 'index.php?option=com_vikrestaurants&view=menudetails&id=' . $id;
			$pathway->addItem($name, $link);
		}
	}
}
