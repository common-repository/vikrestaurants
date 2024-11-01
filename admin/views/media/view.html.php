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
 * VikRestaurants media view.
 *
 * @since 1.6
 */
class VikRestaurantsViewmedia extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		$path = $input->getBase64('path', null);

		if ($path)
		{
			$path = rtrim(base64_decode($path), DIRECTORY_SEPARATOR);
		}
		else
		{
			$path = VREMEDIA;
		}

		if ($input->get('layout') != 'modal')
		{
			$this->ordering = $app->getUserStateFromRequest($this->getPoolName() . '.ordering', 'filter_order', 'date', 'string');
			$this->orderDir = $app->getUserStateFromRequest($this->getPoolName() . '.orderdir', 'filter_order_Dir', 'DESC', 'string');
		}
		else
		{
			// always sort by descending creation date when
			// accessing the media manager modal
			$this->ordering = 'date';
			$this->orderDir = 'DESC';
		}
		
		// retrieve all images and apply filters
		$all_img = VikRestaurantsHelper::getMediaFromPath($path, array($this->ordering, $this->orderDir));

		if ($input->get('layout') != 'modal')
		{
			// set the toolbar
			$this->addToolBar();
			
			$filters = array();
			$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.search', 'search', '', 'string');

			// pagination
			$lim 	= $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
			$lim0 	= $app->getUserStateFromRequest($this->getPoolName() . '.limitstart', 'limitstart', 0, 'uint');
			$navbut	= "";

			if (!empty($filters['search']))
			{
				$app = array();

				foreach ($all_img as $img)
				{
					$file_name = basename($img);

					if (strpos($file_name, $filters['search']) !== false)
					{
						array_push($app, $img);
					}
				}
				$all_img = $app;
				unset($app);
			}
			
			$tot_count = count($all_img);

			if ($tot_count > 0)
			{
				/**
				 * Do not slice the array in case we selected "All" from the limit dropdown.
				 * 
				 * @since 1.9
				 */
				if ($lim > 0)
				{
					if ($lim0 % $lim)
					{
						/**
						 * The current offset is not divisible by the selected limit. For this reason,
						 * we need to reset the offset in order to properly display all the items.
						 * 
						 * @since 1.9
						 */
						$lim0 = 0;
					}

					if ($lim0 >= $tot_count)
					{
						/**
						 * We exceeded the pagination, probably because we deleted all the images of the last page
						 * or we changed the search parameters. For this reason, we need to go back to the last
						 * available page.
						 *
						 * @since 1.9
						 */
						$lim0 = floor($tot_count / $lim) * $lim;
					}

					$all_img = array_slice($all_img, $lim0, $lim);
				}
				else
				{
					$lim0 = 0;
				}

				jimport('joomla.html.pagination');
				$pageNav = new JPagination($tot_count, $lim0, $lim);
				$navbut = JLayoutHelper::render('blocks.pagination', ['pageNav' => $pageNav]);
			}

			$this->navbut  = $navbut;
			$this->filters = $filters;
		}
		else
		{
			/**
			 * Added support for 'modal' layout.
			 *
			 * @since 1.8
			 */
			$this->setLayout('modal');

			// retrieve selected media
			$this->selected = $input->get('media', [], 'string');
			// check if multi-selection is allowed
			$this->multiple = $input->get('multiple', false, 'bool');
			// check if we should accept also documents
			$this->noFilter = $input->get('nofilter', false, 'bool');

			// unset images that don't exist
			$this->selected = array_filter($this->selected, function($elem) use ($path)
			{
				return $elem && is_file($path . DIRECTORY_SEPARATOR . $elem);
			});

			/**
			 * Check if we are uploading the media files for the first time.
			 *
			 * @since 1.8.2
			 */
			if ($path === VREMEDIA)
			{
				// check if we are uploading the media files for the first time
				$this->firstConfig = VREFactory::getConfig()->getBool('firstmediaconfig');
			}
			else
			{
				// disable first config when we are outside the default media path,
				// because we are not going to create any thumbnails
				$this->firstConfig = false;
			}
		}

		$attr = VikRestaurantsHelper::getDefaultFileAttributes();

		foreach ($all_img as $i => $f)
		{
			$all_img[$i] = VikRestaurantsHelper::getFileProperties($f, $attr);
		}

		if (VikRestaurants::isMultilanguage() && $input->get('layout') != 'modal')
		{
			$translator = VREFactory::getTranslator();

			// find available translations
			$lang = $translator->getAvailableLang(
				'media',
				array_map(function($row) {
					return $row['name'];
				}, $all_img)
			);

			// assign languages found to the related elements
			foreach ($all_img as $k => $row)
			{
				$all_img[$k]['languages'] = isset($lang[$row['name']]) ? $lang[$row['name']] : [];
			}
		}
		
		$this->rows = $all_img;
		$this->path = ($path === VREMEDIA ? '' : $path);
		
		// display the template (default.php)
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	private function addToolBar()
	{
		// add menu title and some buttons to the page
		JToolbarHelper::title(JText::translate('VRMAINTITLEVIEWMEDIA'), 'vikrestaurants');

		$user = JFactory::getUser();
		
		if ($user->authorise('core.create', 'com_vikrestaurants'))
		{
			JToolbarHelper::addNew('media.add');
		}

		if ($user->authorise('core.edit', 'com_vikrestaurants'))
		{
			JToolbarHelper::editList('media.edit');
		}

		if ($user->authorise('core.delete', 'com_vikrestaurants'))
		{
			JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'media.delete');
		}
	}
}
