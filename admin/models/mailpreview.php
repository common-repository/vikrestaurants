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
 * VikRestaurants mail preview model.
 *
 * @since 1.9
 */
class VikRestaurantsModelMailpreview extends JModelVRE
{
	/**
	 * Sets the CSS code for the provided e-mail template.
	 * 
	 * @param   string  $group  The template group (restaurant or takeaway).
	 * @param   string  $alias  The template ID (customer, administrator etc...).
	 * @param   string  $css    The CSS code to save.
	 * 
	 * @return  bool    True on success, false otherwise.
	 */
	public function setCSS(string $group, string $alias, string $css)
	{
		try
		{
			if (!$group || !$alias)
			{
				// bad request
				throw new InvalidArgumentException('Missing required fields! Both group and alias must be specified.', 400);
			}

			// update configuration
			VREFactory::getConfig()->set('mailcss_' . $group . '_' . $alias, $css);
		}
		catch (Exception $e)
		{
			// unhandled exception, propagate error
			$this->setError($e);
			return false;
		}
		catch (Throwable $t)
		{
			// fatal error caught
			$this->setError(new Exception($t->getMessage(), 500));
			return false;
		}

		return true;
	}

	/**
	 * Gets the CSS code currently stored for the provided e-mail template.
	 * 
	 * @param   string  $group  The template group (restaurant or takeaway).
	 * @param   string  $alias  The template ID (customer, administrator etc...).
	 * 
	 * @return  string  The CSS code.
	 */
	public function getCSS(string $group, string $alias)
	{
		return VREFactory::getConfig()->get('mailcss_' . $group . '_' . $alias, '');
	}
}
