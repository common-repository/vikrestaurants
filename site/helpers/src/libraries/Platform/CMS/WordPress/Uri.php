<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\WordPress;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Uri\UriAware;

/**
 * Implements the URI interface for the WordPress platform.
 * 
 * @since 1.9
 */
class Uri extends UriAware
{
	/**
	 * @inheritDoc
	 */
	public function route($query = '', bool $xhtml = true, $itemid = null)
	{
		// check if the query already specifies the Itemid
		if (!preg_match("/&Itemid=[\d]*/", $query))
		{
			// try to extract view from query
			if (preg_match("/&view=([a-z0-9_]+)(?:&|$)/", $query, $match))
			{
				$view = end($match);
			}
			else
			{
				$view = null;
			}

			// import shortcodes model
			$model 	= \JModel::getInstance('vikrestaurants', 'shortcodes', 'admin');
			$itemid = $model->best($view);

			if ($itemid)
			{
				// update query within Itemid found
				$query .= (strpos($query, '?') === false ? '?' : '&') . 'Itemid=' . $itemid;
			}
		}

		// route URL
		return \JRoute::rewrite($query, false);
	}

	/**
	 * @inheritDoc
	 */
	public function admin($query = '', bool $xhtml = true)
	{
		if (is_array($query))
		{
			// make sure the array is not empty
			if ($query)
			{
				$query = '?' . http_build_query($query);
			}
			else
			{
				$query = '';
			}

			// the query is an array, build the query string
			$query = 'index.php' . $query;
		}

		// replace initial index.php with admin.php
		$query = preg_replace("/^index\.php/", 'admin.php', $query);
		// replace option=com_vikrestaurants with page=vikrestaurants
		$query = preg_replace("/(&|\?)option=com_vikrestaurants/", '$1page=vikrestaurants', $query);

		// finalise admin URI
		$uri = \admin_url($query);

		if ($xhtml)
		{
			$uri = str_replace('&', '&amp;', $uri);
		}

		return $uri;
	}

	/**
	 * @inheritDoc
	 */
	public function ajax($query = '', bool $xhtml = false)
	{
		// instantiate path based on specified query
		$path = new \JUri($query);

		// delete option var from query
		$path->delVar('option');

		// force action in query
		$path->setVar('action', 'vikrestaurants');

		// force application client in case of front-end
		if (\JFactory::getApplication()->isClient('site'))
		{
			$path->setVar('vik_ajax_client', 'site');
		}

		// create AJAX URI
		$uri = \admin_url('admin-ajax.php') . '?' . $path->getQuery();

		if ($xhtml)
		{
			// try to make "&" XML safe
			$uri = preg_replace("/&(?!amp;)/", '&amp;', $uri);
		}

		return $uri;
	}

	/**
	 * @inheritDoc
	 */
	public function addCSRF($query = '', bool $xhtml = false)
	{
		\JLoader::import('adapter.session.session');

		// safely append the CSRF token within the query string
		$uri = \JUri::getInstance($query);
		$uri->setVar(\JSession::getFormTokenName(), \JSession::getFormToken());

		if ($xhtml)
		{
			// try to make "&" XML safe
			$uri = preg_replace("/&(?!amp;)/", '&amp;', (string) $uri);
		}

		return (string) $uri;
	}

	/**
	 * @inheritDoc
	 */
	public function getAbsolutePath()
	{
		return ABSPATH;
	}
}
