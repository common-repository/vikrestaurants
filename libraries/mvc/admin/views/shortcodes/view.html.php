<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * VikRestaurants Shortcodes view.
 * @wponly
 *
 * @since 1.0
 */
class VikRestaurantsViewShortcodes extends JViewVRE
{
	/**
	 * @override
	 * View display method.
	 *
	 * @return 	void
	 */
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication();
		$dbo  = JFactory::getDbo();
		$user = JFactory::getUser();

		if (!$user->authorise('core.admin', 'com_vikrestaurants'))
		{
			wp_die(
				'<h1>' . JText::translate('FATAL_ERROR') . '</h1>' .
				'<p>' . JText::translate('RESOURCE_AUTH_ERROR') . '</p>',
				403
			);
		}

		$this->returnLink = $app->input->getBase64('return', '');

		// get filters
		$filters = [];
		$filters['search'] = $app->getUserStateFromRequest($this->getPoolName() . '.filters.search', 'filter_search', '', 'string');
		$filters['lang']   = $app->getUserStateFromRequest($this->getPoolName() . '.filters.lang', 'filter_lang', '*', 'string');
		$filters['type']   = $app->getUserStateFromRequest($this->getPoolName() . '.filters.type', 'filter_type', '', 'string');

		$this->filters = $filters;

		// get shortcodes

		$this->limit  = $app->getUserStateFromRequest($this->getPoolName() . '.limit', 'limit', $app->get('list_limit'), 'int');
		$this->offset = $this->getListLimitStart($filters + ['limit' => $this->limit]);
		$navbut	= "";
		
		$this->shortcodes = $this->hierarchicalShortcodes();

		JLoader::import('adapter.filesystem.folder');

		$this->views = [];

		// get all the views that contain a default.xml file
		// [0] : base path
		// [1] : query
		// [2] : true for recursive search
		// [3] : true to return full paths
		$files = JFolder::files(VREBASE . DIRECTORY_SEPARATOR . 'views', 'default.xml', true, true);

		foreach ($files as $f)
		{
			// retrieve the view ID from the path: /views/[ID]/tmpl/default.xml
			if (preg_match("/[\/\\\\]views[\/\\\\](.*?)[\/\\\\]tmpl[\/\\\\]default\.xml$/i", $f, $matches))
			{
				$id = $matches[1];
				// load the XML form
				$form = JForm::getInstance($id, $f);
				// get the view title
				$this->views[$id] = (string) $form->getXml()->layout->attributes()->title;
			}
		}

		$this->addToolbar();
		
		// display parent
		parent::display($tpl);
	}

	/**
	 * Helper method to setup the toolbar.
	 *
	 * @return 	void
	 */
	public function addToolbar()
	{
		JToolbarHelper::title(JText::translate('VRESHORTCDSMENUTITLE'));

		// avoid to show the BACK link in case the return URL was not specified
		if ($this->returnLink)
		{
			JToolbarHelper::back('JTOOLBAR_BACK', base64_decode($this->returnLink));
		}

		JToolbarHelper::addNew('shortcodes.add');
		JToolbarHelper::editList('shortcodes.edit');
		JToolbarHelper::deleteList(VikRestaurants::getConfirmSystemMessage(), 'shortcodes.delete');
	}

	/**
	 * Checks for advanced filters set in the request.
	 *
	 * @return 	boolean  True if active, otherwise false.
	 */
	protected function hasFilters()
	{
		return ($this->filters['lang'] != '*'
			|| !empty($this->filters['type']));
	}

	/**
	 * Retrieves the shortcodes by using a hierarchical ordering.
	 * 
	 * @return 	array  An array of shortcodes.
	 * 
	 * @since 	1.3
	 */
	protected function hierarchicalShortcodes()
	{
		$dbo = JFactory::getDbo();
		
		$q = $dbo->getQuery(true)
			->select('SQL_CALC_FOUND_ROWS *')
			->from($dbo->qn('#__vikrestaurants_wpshortcodes'));

		if ($this->filters['search'])
		{
			$q->where($dbo->qn('name') . ' LIKE ' . $dbo->q("%{$this->filters['search']}%"));
		}

		if ($this->filters['lang'] != '*')
		{
			$q->where($dbo->qn('lang') . ' = ' . $dbo->q($this->filters['lang']));
		}

		if ($this->filters['type'])
		{
			$q->where($dbo->qn('type') . ' = ' . $dbo->q($this->filters['type']));
		}

		$dbo->setQuery($q);
		$rows = $dbo->loadObjectList();

		if (!$rows)
		{
			return [];
		}

		$model = JModelVRE::getInstance('vikrestaurants', 'shortcode', 'admin');

		$shortcodes = [];

		foreach ($rows as $shortcode)
		{
			// load shortcode ancestors
			$shortcode->ancestors = $model->getAncestors($shortcode);

			// create ordering leverage, based on version comparison
			$tmp = array_merge([$shortcode->id], $shortcode->ancestors);
			$shortcode->leverage = implode('.', array_reverse($tmp));

			$shortcodes[] = $shortcode;
		}

		// sort shortcodes by comparing the evaluated leverage
		usort($shortcodes, function($a, $b)
		{
			return version_compare($a->leverage, $b->leverage);
		});

		// create pagination
		jimport('joomla.html.pagination');
		$pageNav = new JPagination(count($shortcodes), $this->offset, $this->limit);
		$this->navbut = JLayoutHelper::render('blocks.pagination', ['pageNav' => $pageNav]);

		// take only the records that match the pagination query
		$shortcodes = array_splice($shortcodes, $this->offset, $this->limit);

		return $shortcodes;
	}
}
