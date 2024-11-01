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
 * VikRestaurants mail preview view.
 *
 * @since 1.9
 */
class VikRestaurantsViewmailpreview extends JViewVRE
{	
	/**
	 * VikRestaurants view display method.
	 *
	 * @return 	void
	 */
	function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$config = VREFactory::getConfig();
		$model  = JModelVRE::getInstance('mailpreview');

		$this->supportedAliases = [
			'restaurant' => [
				'customer'     => JText::translate('VRCONFIGSMSAPITO0'),
				'admin'        => JText::translate('VRCONFIGSMSAPITO1'),
				'cancellation' => JText::translate('VRE_CANCELLATION_FIELDSET'),
			],
			'takeaway' => [
				'customer'     => JText::translate('VRCONFIGSMSAPITO0'),
				'admin'        => JText::translate('VRCONFIGSMSAPITO1'),
				'cancellation' => JText::translate('VRE_CANCELLATION_FIELDSET'),
				'review'       => JText::translate('VRMANAGECONFIG67'),
				'stock'        => JText::translate('VRMANAGECONFIGTK17'),
			],
		];

		$this->supportedFiles = [
			'restaurant' => [
				'customer'     => $config->get('mailtmpl'),
				'admin'        => $config->get('adminmailtmpl'),
				'cancellation' => $config->get('cancmailtmpl'),
			],
			'takeaway' => [
				'customer'     => $config->get('tkmailtmpl'),
				'admin'        => $config->get('tkadminmailtmpl'),
				'cancellation' => $config->get('tkcancmailtmpl'),
				'review'       => $config->get('tkreviewmailtmpl'),
				'stock'        => $config->get('tkstockmailtmpl'),
			],
		];

		$this->savedCSS = [];

		foreach ($this->supportedFiles as $group => $aliases)
		{
			$this->savedCSS[$group] = [];

			foreach ($aliases as $alias => $file)
			{
				$this->savedCSS[$group][$alias] = $model->getCSS($group, $alias);
			}
		}

		$filters = [];
		$filters['group']   = $app->input->getString('group', '');
		$filters['alias']   = $app->input->getString('alias', '');
		$filters['file']    = $app->input->getString('file', '');
		$filters['langtag'] = $app->input->getString('langtag', JFactory::getLanguage()->getTag());

		$filters['group'] = JHtml::fetch('vrehtml.admin.getgroup', $filters['group'], ['restaurant', 'takeaway']);

		if (!isset($this->supportedAliases[$filters['group']][$filters['alias']]))
		{
			// get first available alias
			$filters['alias'] = key($this->supportedAliases[$filters['group']]);
		}

		if ($filters['file'])
		{
			// overwrite supported file for the given group and alias
			$this->supportedFiles[$filters['group']][$filters['alias']] = $filters['file'];
		}
		else
		{
			$filters['file'] = $this->supportedFiles[$filters['group']][$filters['alias']];
		}

		$filters['css'] = $this->savedCSS[$filters['group']][$filters['alias']] ?? '';

		$this->filters = $filters;

		// force blank template
		$app->input->set('tmpl', 'component');

		// set up preview URL
		$this->patternURL = 'index.php?option=com_vikrestaurants&task=mailpreview.rendertemplate&group=%s&alias=%s&file=%s&langtag=%s';
		$this->patternURL = VREFactory::getPlatform()->getUri()->addCSRF($this->patternURL);

		$this->url = sprintf($this->patternURL, $filters['group'], $filters['alias'], $filters['file'], $filters['langtag']);

		/** @var E4J\VikRestaurants\Platform\Form\FormFactory */
		$this->formFactory = VREFactory::getPlatform()->getFormFactory();

		// display the template
		parent::display($tpl);
	}
}
