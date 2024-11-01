<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  update
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Implements the abstract methods to fix an update.
 *
 * Never use exit() and die() functions to stop the flow.
 * Return false instead to break process safely.
 *
 * @since 1.0
 */
class VikRestaurantsUpdateFixer
{
	/**
	 * The current version.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Class constructor.
	 */
	public function __construct($version)
	{
		$this->version = $version;
	}

	/**
	 * This method is called before the SQL installation.
	 *
	 * @return 	boolean  True to proceed with the update, otherwise false to stop.
	 */
	public function beforeInstallation()
	{
		if (version_compare($this->version, '1.2.3', '<') && !VikRestaurantsLiteManager::guessPro())
		{
			$dbo = JFactory::getDbo();
			// truncate the payment gateways table
			$dbo->setQuery("TRUNCATE TABLE `#__vikrestaurants_gpayments`");
			$dbo->execute();
		}

		return true;
	}

	/**
	 * This method is called after the SQL installation.
	 *
	 * @return 	boolean  True to proceed with the update, otherwise false to stop.
	 */
	public function afterInstallation()
	{
		/**
		 * Unpublish overrides and obtain tracking list.
		 *
		 * @since 1.2
		 */
		$track = $this->deactivateBreakingOverrides();

		// register breaking changes, if any
		VikRestaurantsInstaller::registerBreakingChanges($track);

		if (version_compare($this->version, '1.2.4', '<'))
		{
			// load the update adapter used for 1.8.5 version in Joomla
			E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_8_5::afterupdate($this);
		}

		if (version_compare($this->version, '1.3', '<'))
		{
			// load the update adapter used for 1.9 version in Joomla
			$adapter = new E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_9;
			// launch all the update methods
			$adapter->update($this);
			$adapter->finalise($this);
			$adapter->afterupdate($this);

			// create CSS customizer folder
			JFolder::create(VRE_CSS_CUSTOMIZER);

			// create Code Hub folder
			JFolder::create(VRE_CUSTOM_CODE_FOLDER);
		}

		if (version_compare($this->version, '1.3.1', '<'))
		{
			// load the update adapter used for 1.9.1 version in Joomla
			$adapter = new E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_9_1;
			// launch all the update methods
			$adapter->update($this);
			$adapter->finalise($this);
			$adapter->afterupdate($this);
		}

		if (version_compare($this->version, '1.3.2', '<'))
		{
			// load again the update adapter used for 1.9.1 version in Joomla as the
			// mapper used to fix the cancelled status codes has been introduced later
			$adapter = new E4J\VikRestaurants\Update\Adapters\UpdateAdapter1_9_1;
			// launch all the update methods
			$adapter->update($this);
			$adapter->finalise($this);
			$adapter->afterupdate($this);
		}

		return true;
	}

	/**
	 * Returns a list of possible overrides that may
	 * break the site for backward compatibility errors.
	 *
	 * @return 	array  The list of overrides, grouped by client.
	 */
	protected function getBreakingOverrides()
	{
		// define initial overrides lookup
		$lookup = [
			'admin'   => [],
			'site'    => [],
			'layouts' => [],
			'widgets' => [],
		];

		// check whether the current version (before the update)
		// was prior than 1.1 version
		if (version_compare($this->version, '1.1', '<'))
		{
			/**
			 * The orders property of the view doesn't contain anymore a 
			 * list of mail objects and this means that the template file
			 * cannot continue to call the `getHtml()` method to render
			 * the layout of the e-mail template. The internal `templateHTML`
			 * property should be used instead.
			 */
			$lookup['site'][] = VREBASE . '/views/optkprintorders/tmpl/default.php';
		}

		// check whether the current version (before the update)
		// was prior than 1.3 version
		if (version_compare($this->version, '1.3', '<'))
		{
			// massive changes, disable all the created overrides
			$lookup = JModel::getInstance('vikrestaurants', 'overrides', 'admin')->getAllOverrides();
		}

		// check whether the current version (before the update)
		// was prior than 1.3.3 version
		if (version_compare($this->version, '1.3.3', '<'))
		{
			/**
			 * The layouts used to display the kitchen widget introduced some code to prevent errors
			 * in case the widget is published for the take-away group.
			 */
			$lookup['layouts'][] = VREADMIN . '/layouts/statistics/widgets/kitchen/wall.php';
			$lookup['layouts'][] = VREBASE . '/layouts/statistics/widgets/kitchen/wall.php';
			$lookup['layouts'][] = VREBASE . '/layouts/oversight/widget.php';
		}

		return $lookup;
	}

	/**
	 * Helper function used to deactivate any overrides that
	 * may corrupt the system because of breaking changes.
	 *
	 * @return 	array  The list of unpublished overrides.
	 *
	 * @since 	1.2
	 */
	protected function deactivateBreakingOverrides()
	{
		// load list of breaking overrides
		$lookup = $this->getBreakingOverrides();

		$track = [];

		// get models to manage the overrides
		$listModel = JModel::getInstance('vikrestaurants', 'overrides', 'admin');
		$itemModel = JModel::getInstance('vikrestaurants', 'override', 'admin');

		foreach ($lookup as $client => $files)
		{
			// do not need to load the whole tree in case
			// the client doesn't report any files
			if ($files)
			{
				$tree = $listModel->getTree($client);

				foreach ($files as $file)
				{
					// clean file path
					$file = JPath::clean($file);

					// check whether the specified file is supported
					if ($node = $listModel->isSupported($tree, $file))
					{
						// skip in case the path has been already unpublished
						if (in_array($node['override'], $track[$client] ?? []))
						{
							continue;
						}

						// override found, check whether we have an existing
						// and published override
						if ($node['has'] && $node['published'])
						{
							// deactivate the override
							if ($itemModel->publish($node['override'], 0))
							{
								if (!isset($track[$client]))
								{
									$track[$client] = [];
								}

								// track the unpublished file for later use
								$track[$client][] = $node['override'];
							}
						}
					}
				}
			}
		}

		return $track;
	}
}
