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
 * VikRestaurants backups view.
 *
 * @since 1.9
 */
class VikRestaurantsViewbackups extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$model = JModelVRE::getInstance('backup');

		// set the toolbar
		$this->addToolBar();

		$filters = [];
		$filters['date'] = $app->getUserStateFromRequest($this->getPoolName() . '.date', 'date', '', 'string');
		$filters['type'] = $app->getUserStateFromRequest($this->getPoolName() . '.type', 'type', '', 'string');

		$this->filters = $filters;

		$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'createdon', 'string');
		$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'DESC', 'string');

		// db object
		$lim 	= $app->getUserStateFromRequest('com_vikrestaurants.limit', 'limit', $app->get('list_limit'), 'uint');
		$lim0 	= $this->getListLimitStart($filters);
		$navbut	= "";

		// load all the export types
		$this->exportTypes = $model->getExportTypes();

		$rows = [];

		// fetch folder in which the backup are stored
		$folder = VREFactory::getConfig()->get('backupfolder');

		if (!$folder)
		{
			// use temporary folder if not specified
			$folder = JFactory::getApplication()->get('tmp_path');
		}

		if ($folder && JFolder::exists($folder))
		{
			// load all backup archives
			$rows = JFolder::files($folder, 'backup_', $recurse = false, $fullpath = true);
		}

		// fetch backup details
		$rows = array_map(function($file) use ($model)
		{
			return $model->getItem($file);
		}, $rows);

		// filter the backups
		$rows = array_values(array_filter($rows, function($r) use ($filters)
		{
			if ($filters['type'] && $r->type->id !== $filters['type'])
			{
				return false;
			}

			if (E4J\VikRestaurants\Helpers\DateHelper::isNull($filters['date']) === false)
			{
				// get locale SQL strings to have dates adjusted to the current
				// timezone. This way the dates will be refactored for being
				// used in UTC, even if the locale is different.
				$start = E4J\VikRestaurants\Helpers\DateHelper::getDate($filters['date']);

				if ($start->format('Y-m-d') !== JFactory::getDate($r->date)->format('Y-m-d'))
				{
					return false;
				}
			}

			return true;
		}));

		$ordering  = $this->ordering;
		$direction = $this->orderDir;

		// fetch the type of ordering
		usort($rows, function($a, $b) use ($ordering, $direction)
		{
			switch ($ordering)
			{
				case 'filesize':
					// sort by file size
					$factor = $a->size - $b->size;
					break;

				default:
					// sort by creation date
					$factor = $a->timestamp - $b->timestamp;
			}

			// in case of descending direction, reverse the ordering factor
			if (preg_match("/desc/i", $direction))
			{
				$factor *= -1;
			}

			return $factor;
		});

		$tot_count = count($rows);

		if ($tot_count)
		{
			// do not slice the array in case we selected "All" from the limit dropdown
			if ($lim > 0)
			{
				if ($lim0 >= $tot_count)
				{
					// We exceeded the pagination, probably because we deleted all the records of the last page.
					// For this reason, we need to go back to the previous one.
					$lim0 = max(array(0, $lim0 - $lim));
				}

				$rows = array_slice($rows, $lim0, $lim);
			}
			else
			{
				$lim0 = 0;
			}

			jimport('joomla.html.pagination');
			$pageNav = new JPagination($tot_count, $lim0, $lim);
			$navbut = JLayoutHelper::render('blocks.pagination', ['pageNav' => $pageNav]);
		}

		$this->rows   = $rows;
		$this->navbut = $navbut;

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();
		
		// display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolBarHelper::title(JText::translate('VREMAINTITLEVIEWBACKUPS'), 'vikrestaurants');

		$user = JFactory::getUser();

		JToolbarHelper::back('JTOOLBAR_BACK', 'index.php?option=com_vikrestaurants&view=editconfigapp');

		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolBarHelper::addNew('backup.add');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolBarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'backup.delete');
		}
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return !empty($this->filters['type']);
	}
}
