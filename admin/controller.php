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
 * General Controller of VikRestaurants component.
 *
 * @since 1.0
 */
class VikRestaurantsController extends JControllerVRE
{
	/**
	 * @inheritDoc
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$input = JFactory::getApplication()->input;

		$view = $input->get('view');

		if (empty($view))
		{
			$input->set('view', $view = VikRestaurantsHelper::getDefaultView());
		}
		
		/**
		 * Fetch here whether to display the menu or not.
		 *
		 * @since 1.8
		 */
		$hasMenu = $this->shouldDisplayMenu($view);

		if ($hasMenu)
		{
			VikRestaurantsHelper::printMenu();
		}

		// call parent behavior
		parent::display();

		/**
		 * Fetch here whether to display the footer or not.
		 *
		 * @since 1.8
		 */
		if ($hasMenu)
		{
			VikRestaurantsHelper::printFooter();
		}
	}

	////////////////////////
	////// AJAX UTILS //////
	////////////////////////

	/**
	 * AJAX end-point to obtain a list of available working
	 * shifts for the given date and group (1: restaurant, 2: take-away).
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function get_working_shifts()
	{
		$app = JFactory::getApplication();
		
		$date  = $app->input->get('date', '', 'string');
		$group = $app->input->get('group', 1, 'uint');
		
		$shifts = JHtml::fetch('vikrestaurants.times', $group, $date);

		$html = '';
		
		foreach ($shifts as $optgroup => $options)
		{
			if ($optgroup)
			{
				$html .= '<optgroup label="' . $optgroup . '">';
			}

			foreach ($options as $opt)
			{
				$html .= '<option value="' . $opt->value . '">' . $opt->text . '</option>';
			}

			if ($optgroup)
			{
				$html .= '</optgroup>';
			}
		}
		
		E4J\VikRestaurants\Http\Document::getInstance($app)->json(json_encode($html));
	}

	/**
	 * AJAX end-point to fetch a Google image for the specified location.
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	public function get_googlemaps_image()
	{
		$app = JFactory::getApplication();

		$lat  = $app->input->get('lat', null, 'float');
		$lng  = $app->input->get('lng', null, 'float');
		$size = $app->input->get('size', null, 'string');
		$attr = $app->input->get('imageattr', null, 'array');

		$options = [
			// define image center
			'center' => [
				'lat' => $lat,
				'lng' => $lng,
			],
			// define image size
			'size' => $size,
		];

		E4J\VikRestaurants\Http\Document::getInstance($app)->json(json_encode(JHtml::fetch('vrehtml.site.googlemapsimage', $options)));
	}

	/**
	 * AJAX end-point used to fetch a list of suggestions based on
	 * the translations that have been already made for the given
	 * language tag.
	 *
	 * @return  void
	 *
	 * @since   1.8
	 */
	public function get_suggested_translations()
	{
		$app = JFactory::getApplication();

		// do not filter term in order to support HTML
		$term = $app->input->get('term', '', 'raw');
		$tag  = $app->input->get('tag', null, 'string');

		$translator = VREFactory::getTranslator();

		// fetch suggestions
		$suggestions = $translator->getSuggestions($term, $tag);

		if ($suggestions)
		{
			// map suggestions to always encode HTML special characters
			$suggestions = array_map(function($hint)
			{
				return htmlentities($hint);
			}, $suggestions);
		}
		else
		{
			$suggestions = [];
		}

		E4J\VikRestaurants\Http\Document::getInstance($app)->json($suggestions);
	}

	/**
	 * Checks whether the specified view should display the menu or not.
	 *
	 * @param   string  $view  The view to check.
	 * @param   array   $list  An additional list of supported views.
	 *
	 * @return  bool
	 *
	 * @since   1.8
	 */
	protected function shouldDisplayMenu($view, array $list = [])
	{
		$tmpl = JFactory::getApplication()->input->get('tmpl');

		// do not display in case of tmpl=component
		if (!strcmp((string) $tmpl, 'component'))
		{
			return false;
		}

		// defines list of views that supports menu and footer
		$views = [
			'coupons',
			'customers',
			'customf',
			'editconfig',
			'editconfigres',
			'editconfigtk',
			'editconfigapp',
			'invoices',
			'shifts',
			'specialdays',
			'maps',
			'media',
			'menus',
			'menusproducts',
			'operators',
			'operatorlogs',
			'payments',
			'rescodes',
			'reservations',
			'restaurant',
			'reviews',
			'roomclosures',
			'rooms',
			'statuscodes',
			'tables',
			'taxes',
			'tkareas',
			'tkdeals',
			'tkmenuattr',
			'tkmenus',
			'tkproducts',
			'tkreservations',
			'tktoppings',
			'tktopseparators',
		];

		// merge lookup with overrides
		$views = array_merge($views, $list);

		// check whether the view is in the list
		return in_array($view, $views);
	}
}
