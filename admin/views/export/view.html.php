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
 * VikRestaurants export view.
 *
 * @since 1.9
 */
class VikRestaurantsViewexport extends JViewVRE
{
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->type   = $app->input->get('datasheet_type');
		$this->return = $app->input->get('return', '', 'base64');
		$this->cid    = $app->input->get('cid', [], 'uint');

		/** @var E4J\VikRestaurants\DataSheet\DataSheetFactory */
		$dataSheetFactory = VREFactory::getDataSheetFactory();

		/** @var E4J\VikRestaurants\DataSheet\DataSheet */
		$this->dataSheet = $dataSheetFactory->getDataSheet($this->type, ['cid' => $this->cid]);

		/** @var E4J\VikRestaurants\DataSheet\Export\ExportDriver[] */
		$this->exportDrivers = $dataSheetFactory->getExportDrivers();

		$this->addToolBar();

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
		JToolBarHelper::title(JText::translate('VRMAINTITLEVIEWEXPORT'), 'vikrestaurants');

		if ($this->return)
		{
			JToolbarHelper::back('JTOOLBAR_BACK', base64_decode($this->return));
		}

		JToolBarHelper::custom('export', 'download', 'download', JText::translate('VRDOWNLOAD'), false);
	}

	/**
	 * Extracts a preview table from the provided datasheet.
	 * 
	 * @return  object
	 */
	protected function getPreviewData()
	{
		$table = new stdClass;
		$table->head = $this->dataSheet->getHead();
		$table->body = [];

		$index = 0;
		$max   = 10;

		foreach ($this->dataSheet->getBody() as $row)
		{
			$table->body[] = $row;

			if (++$index >= $max)
			{
				break;
			}
		}

		return $table;
	}
}
