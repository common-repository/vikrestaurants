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

VRELoader::import('library.menu.custom');

/**
 * Extends the CustomShape class to display a button to check the Joomla software version.
 *
 * @since 1.7
 * @since 1.8 Renamed from LeftBoardMenuVersion to LeftboardCustomShapeVersion.
 */
class LeftboardCustomShapeVersion extends CustomShape
{
	/**
	 * @override
	 * Builds and returns the html structure of the custom menu item.
	 * This method must be implemented to define a specific graphic of the custom item.
	 *
	 * @return 	string 	The html of the custom item.
	 */
	public function buildHtml()
	{
		$dispatcher = VREFactory::getEventDispatcher();

		// check if VikUpdater is available
		$callable  = $dispatcher->is('onUpdaterSupported');
		$to_update = 0;

		// prepare display data
		$data = array(
			'newupdate'  => (bool) $to_update,
			'vikupdater' => (bool) $callable,
			'connect'    => false,
			'url'        => $this->get('url'),
			'label'      => $this->get('label'),
			'title' 	 => '',
		);

		$config = VREFactory::getConfig();

		$params = new stdClass;
		$params->version = $config->get('version');
		$params->alias   = 'com_vikrestaurants'; 

		// search for a cached update
		$result = $dispatcher->triggerOnce('onGetVersionContents', array(&$params));

		if ($result)
		{ 
			if ($result->status)
			{
				if ($result->response->status)
				{
					$data['label'] = $result->response->shortTitle;
					$data['title'] = $result->response->title;

					if ($result->response->compare == 1)
					{
						$to_update = 1;
					}
				}
				else
				{
					$data['label'] = JText::translate('VRERROR');
					$data['title'] = $result->response->error;
				}
			}
			else
			{
				$data['label'] = JText::translate('VRERROR');
			}
		}

		$data['connect'] = !$result;

		$layout = new JLayoutFile('menu.leftboard.custom.version');
		
		return $layout->render($data);
	}
}
