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
 * VikRestaurants go to pro view.
 * @wponly
 *
 * @since 1.0
 */
class VikRestaurantsViewgotopro extends JViewVRE
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

		// Set the toolbar
		$this->addToolBar();

		VikRestaurantsLoader::import('update.license');

		$lic_key  = VikRestaurantsLicense::getKey();
		$lic_date = VikRestaurantsLicense::getExpirationDate();
		$is_pro   = VikRestaurantsLicense::isPro();

		if ($is_pro) 
		{
			$tpl = 'pro';
		}
		
		$this->licenseKey  = $lic_key;
		$this->licenseDate = $lic_date;
		$this->isPro       = $is_pro;
		
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
		JToolBarHelper::title(JText::translate('VREMAINGOTOPROTITLE'), 'vikrestaurants');
	}
}
