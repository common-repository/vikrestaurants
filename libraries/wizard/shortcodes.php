<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  wizard
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Implement the wizard step used to setup the shortcodes
 * to display VikRestaurants within the front-end.
 *
 * @since 1.1
 */
class VREWizardStepShortcodes extends VREWizardStep
{
	/**
	 * Returns the step title.
	 * Used as a very-short description.
	 *
	 * @return 	string  The step title.
	 */
	public function getTitle()
	{
		return __('Shortcodes');
	}

	/**
	 * Returns the step description.
	 *
	 * @return 	string  The step description.
	 */
	public function getDescription()
	{
		return __('<p>The shortcodes are used to display the plugin within the front-end. You need to create at least a shortcode, which should then be included within an apposite post/page.</p><p>After creating a shortcode, go back to the list and click the <i class="fas fa-plus-square"></i> button, under the <b>Post</b> column, to automatically create a page. The created page will include the shortcode previously generated.</p>', 'vikrestaurants');
	}

	/**
	 * Returns an optional step icon.
	 *
	 * @return 	string  The step icon.
	 */
	public function getIcon()
	{
		return '<i class="fas fa-quote-left"></i>';
	}

	/**
	 * Return the group to which the step belongs.
	 *
	 * @return 	string  The group name.
	 */
	public function getGroup()
	{
		// belongs to GLOBAL group
		return JText::translate('VRMENUTITLEHEADER4');
	}

	/**
	 * Returns the completion progress in percentage.
	 *
	 * @return 	integer  The percentage progress (always rounded).
	 */
	public function getProgress()
	{
		$progress = 100;

		if ($this->needShortcode('restaurant'))
		{
			// missing shortcode for restaurant section, decrease progress
			$progress -= 50;
		}

		if ($this->needShortcode('takeaway'))
		{
			// missing shortcode for take-away section, decrease progress
			$progress -= 50;
		}

		return $progress;
	}

	/**
	 * Checks whether the step has been completed.
	 *
	 * @return 	boolean  True if completed, false otherwise.
	 */
	public function isCompleted()
	{
		// look for 100% completion progress
		return $this->getProgress() == 100;
	}

	/**
	 * Returns the button used to process the step.
	 *
	 * @return 	string  The HTML of the button.
	 */
	public function getExecuteButton()
	{
		// point to the controller to create a new shortcode
		return '<a href="index.php?option=com_vikrestaurants&task=shortcodes.add" class="btn btn-success">' . JText::translate('VRNEW') . '</a>';
	}

	/**
	 * Returns the HTML to display description and actions
	 * needed to complete the step.
	 *
	 * @return 	string  The HTML of the step.
	 */
	public function display()
	{
		// always try to search for a layout related to this step
		return JLayoutHelper::render('html.wizard.shortcodes', array('step' => $this));
	}

	/**
	 * Checks whether the specified step can be skipped.
	 * By default, all the steps are mandatory.
	 * 
	 * @return 	boolean  True if skippable, false otherwise.
	 */
	public function canIgnore()
	{
		return true;
	}

	/**
	 * Returns a list of created shortcodes.
	 *
	 * @return 	array  A list of shortcodes.
	 */
	public function getShortcodes()
	{
		static $shortcodes = null;

		// get shortcodes only once
		if (is_null($shortcodes))
		{
			$dbo = JFactory::getDbo();

			$q = $dbo->getQuery(true)
				->select($dbo->qn(array('type', 'title', 'name')))
				->from($dbo->qn('#__vikrestaurants_wpshortcodes'))
				->order($dbo->qn('id') . ' ASC');

			$dbo->setQuery($q);
			$dbo->execute();

			if ($dbo->getNumRows())
			{
				$shortcodes = $dbo->loadObjectList();
			}
			else
			{
				$shortcodes = array();
			}
		}

		return $shortcodes;
	}

	/**
	 * Checks whether the specified group needs the
	 * creation of a shortcode.
	 *
	 * @param 	string   $group  The group to look for.
	 *
	 * @return 	boolean  True if a shortcode is needed, false otherwise.
	 */
	public function needShortcode($group)
	{
		// the step is completed after creating at least a shortcode
		// for each active section
		$types = array_map(function($shortcode)
		{
			return $shortcode->type;
		}, $this->getShortcodes());

		// get sections dependency
		$sections = $this->getDependency('sections');

		// check if the group is enabled
		if ($group == 'restaurant')
		{
			$enabled = $sections && $sections->isRestaurant();

			// define lookup of supported views
			$lookup = array('restaurants', 'confirmres', 'menuslist', 'menudetails');
		}
		else if ($group == 'takeaway')
		{
			$enabled = $sections && $sections->isTakeAway();

			// define lookup of supported views
			$lookup = array('takeaway', 'takeawayitem', 'takeawayconfirm');
		}
		else
		{
			$enabled = false;
		}

		// in case the group is active, check whether the list
		// of created types intersects at least one element of
		// the fetched lookup
		return $enabled && !array_intersect($types, $lookup);
	}
}
