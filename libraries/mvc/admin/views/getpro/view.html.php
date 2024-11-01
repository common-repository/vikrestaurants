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
 * VikRestaurants get pro view.
 * @wponly
 *
 * @since 1.0
 */
class VikRestaurantsViewGetpro extends JViewVRE
{
	/**
	 * @override
	 * View display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		JHtml::fetch(
			'stylesheet',
			VIKRESTAURANTS_CORE_MEDIA_URI . 'css/license.css',
			['version' => VIKRESTAURANTS_SOFTWARE_VERSION],
			['id' => 'vre-license-style']
		);

		$app = JFactory::getApplication();

		// set the toolbar
		$this->addToolBar();

		VikRestaurantsLoader::import('update.changelog');
		VikRestaurantsLoader::import('update.license');

		// get version from request
		$version = $app->input->getString('version');

		if ($version)
		{
			// init HTTP transport
			$http = new JHttp;

			// always re-download the changelog of VikRestaurants
			// before upgrading the files to the PRO version
			$url = 'https://vikwp.com/api/?task=products.changelog';

			// use version set in request because the constant and the
			// database, at this point, are always up to date
			$data = [
				'sku'     => 'vre',
				'version' => $version,
			];

			$response = $http->post($url, $data);

			if ($response->code == 200)
			{
				// save changelog on success
				VikRestaurantsChangelog::store(json_decode($response->body));
			}
		}

		$changelog = VikRestaurantsChangelog::build();
		$lic_key   = VikRestaurantsLicense::getKey();
		$lic_date  = VikRestaurantsLicense::getExpirationDate();
		$is_pro    = VikRestaurantsLicense::isPro();

		if (!$is_pro)
		{	
			$app->enqueueMessage(JText::translate('VRENOPROERROR'), 'error');
			$app->redirect('index.php?page=vikrestaurants&view=gotopro');
			return;
		}
		
		$this->changelog  = $changelog;
		$this->licenseKey = $lic_key;
		
		// display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar.
	 *
	 * @return 	void
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::translate('VREMAINGETPROTITLE'), 'vikrestaurants');
	}
}
