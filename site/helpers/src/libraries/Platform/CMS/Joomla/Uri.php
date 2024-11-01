<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform\CMS\Joomla;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Uri\UriAware;

/**
 * Implements the URI interface for the Joomla platform.
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
		$app = \JFactory::getApplication();

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

		if (is_null($itemid) && $app->isClient('site'))
		{
			// no item id, get it from the request
			$itemid = $app->input->getInt('Itemid', 0);
		}

		if ($itemid)
		{
			if ($query)
			{
				// check if the query string contains a '?'
				if (strpos($query, '?') !== false)
				{
					// the query already starts with 'index.php?' or '?'
					$query .= '&';
				}
				else
				{
					// the query string is probably equals to 'index.php'
					$query .= '?';
				}
			}
			else
			{
				// empty query, create the default string
				$query = 'index.php?';
			}

			// the item id is set, append it at the end of the query string
			$query .= 'Itemid=' . $itemid;
		}

		// get base path
		$uri  = \JUri::getInstance();
		$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		if (method_exists('JRoute', 'link') && $app->isClient('administrator'))
		{
			/**
			 * Rewrite site URL also from the back-end.
			 * Available starting from Joomla! 3.9.0.
			 * 
			 * @since 1.8.6
			 */
			$uri = $base . \JRoute::link('site', $query, $xhtml);
		}
		else
		{
			// route the query string and append it to the base path to create the final URI
			$uri = $base . \JRoute::rewrite($query, $xhtml);
		}

		// remove administrator/ from URL in case this method is called from admin
		if ($app->isClient('administrator') && preg_match("/\/administrator\//i", $uri))
		{
			$adminPos = strrpos($uri, 'administrator/');
			$uri      = substr_replace($uri, '', $adminPos, 14);
		}

		return $uri;
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

		// finalise admin URI
		$uri = \JUri::root() . 'administrator/' . $query;

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
		if (\JFactory::getApplication()->isClient('site') && preg_match("/^index\.php/", $query))
		{
			// rewrite plain URL
			$uri = \JRoute::rewrite($query, $xhtml);
		}
		else
		{
			// routed URL given (or admin location), use it directly
			$uri = $query;

			if ($xhtml)
			{
				// try to make "&" XML safe
				$uri = preg_replace("/&(?!amp;)/", '&amp;', $uri);
			}
		}

		return $uri;
	}

	/**
	 * @inheritDoc
	 */
	public function addCSRF($query = '', bool $xhtml = false)
	{
		// safely append the CSRF token within the query string
		$uri = \JUri::getInstance($query);
		$uri->setVar(\JSession::getFormToken(), 1);

		if ($xhtml)
		{
			// try to make "&" XML safe
			$uri = str_replace('&', '&amp;', (string) $uri);
		}

		return (string) $uri;
	}

	/**
	 * @inheritDoc
	 */
	public function getAbsolutePath()
	{
		return JPATH_SITE;
	}
}
