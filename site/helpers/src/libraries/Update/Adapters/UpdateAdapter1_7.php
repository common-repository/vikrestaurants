<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Update\Adapters;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Update adapter for com_vikrestaurants 1.7 version.
 *
 * NOTE. do not call exit() or die() because the update won't be finalised correctly.
 * Return false instead to stop in anytime the flow without errors.
 *
 * @since 1.7
 * @since 1.9  Renamed from VikRestaurantsUpdateAdapter1_7
 */
abstract class UpdateAdapter1_7
{
	/**
	 * Method run during update process.
	 *
	 * @param   object  $parent  The parent that calls this method.
	 *
	 * @return  bool    True on success, otherwise false to stop the flow.
	 */
	public static function update($parent)
	{
		$db = \JFactory::getDbo();

		self::adaptDeliverySetting($db);

		self::adaptResCodesOrdering($db);

		self::adaptZipCodes($db);

		self::adaptTakeAwayTaxes($db);

		if (isset($parent->customFields))
		{
			self::adaptCustomFields($parent->customFields, $db);
		}

		return true;
	}

	/**
	 * Method run during postflight process.
	 *
	 * @param   object  $parent  The parent that calls this method.
	 *
	 * @return  bool    True on success, otherwise false to stop the flow.
	 */
	public static function finalise($parent)
	{
		self::moveExistingMedias();

		return true;
	}

	/**
	 * Adapt delivery setting to allow also pickup service.
	 */
	protected static function adaptDeliverySetting($db)
	{
		$config = \VREFactory::getConfig();

		if ($config->getUint('deliveryservice') == 1)
		{
			$config->set('deliveryservice', 2);
		}

		return true;
	}

	/**
	 * Insert ordering to reservation codes.
	 */
	protected static function adaptResCodesOrdering($db)
	{
		$q = $db->getQuery(true);

		$q->update($db->qn('#__vikrestaurants_res_code'))
			->set($db->qn('ordering') . ' = ' . $db->qn('id'));

		$db->setQuery($q);
		$db->execute();

		return (bool) $db->getAffectedRows();
	}

	/**
	 * Adapt take-away taxes to existing products stored.
	 */
	protected static function adaptTakeAwayTaxes($db)
	{
		$config = \VREFactory::getConfig();

		$use_taxes   = $config->getBool('tkshowtaxes');
		$taxes_ratio = $config->getFloat('tktaxesratio');

		if (!$use_taxes || $taxes_ratio <= 0)
		{
			return true;
		}

		$q = $db->getQuery(true);

		$q->update($db->qn('#__vikrestaurants_takeaway_res_prod_assoc'))
			->set($db->qn('taxes') . ' = (' . $db->qn('price') . ' * ' . $taxes_ratio . ' / 100)');

		$db->setQuery($q);
		$db->execute();

		return (bool) $db->getAffectedRows();
	}

	/**
	 * Adapt accepted zip codes to the new delivery area system.
	 */
	protected static function adaptZipCodes($db)
	{
		$config = \VREFactory::getConfig();

		$zipCodes = $config->getJSON('zipcodes');

		if ($zipCodes === false)
		{
			return true;
		}

		$charges = [];

		foreach ($zipCodes as $zip)
		{
			if (!isset($charges[$zip->charge]))
			{
				$charges[$zip->charge] = [];
			}

			$charges[$zip->charge][] = $zip;
		}

		$ordering = 1;

		foreach ($charges as $ch => $zips)
		{
			$delivery = new \stdClass;

			$delivery->name      = 'ZIPs Charge € ' . $ch;
			$delivery->type      = 3; // zip restriction
			$delivery->charge    = $ch;
			$delivery->min_cost  = 0;
			$delivery->published = 1;
			$delivery->ordering  = $ordering;
			$delivery->content   = [];

			foreach ($zips as $zip)
			{
				$delivery->content[] = [
					"from" => $zip->from,
					"to" => $zip->to,
				];
			}

			$delivery->content = json_encode($delivery->content);

			if (!$db->insertObject('#__vikrestaurants_takeaway_delivery_area', $delivery))
			{
				return false;
			}

			$ordering++;
		}

		return true;
	}

	/**
	 * Adapt the rules of the custom fields.
	 */
	protected static function adaptCustomFields($fields, $db)
	{
		foreach ($fields as $cf)
		{
			$rule = 0;

			if ($cf->isnominative)
			{
				$rule = 1;
			}
			else if ($cf->isemail)
			{
				$rule = 2;
			}
			else if ($cf->isphone)
			{
				$rule = 3;
			}
			else if ($cf->group == 1)
			{
				switch ($cf->name)
				{
					case 'CUSTOMF_TKDELIVERY':
					case 'CUSTOMF_TKZIP':
					case 'CUSTOMF_TKNOTE':
						$rule = 5;
						break;

					case 'CUSTOMF_TKADDRESS':
						$rule = 4;
						break;
				}
			}

			$q = $db->getQuery(true);

			$q->update($db->qn('#__vikrestaurants_custfields'))
				->set($db->qn('rule') . ' = ' . $rule)
				->where($db->qn('id') . ' = ' . $cf->id);

			$db->setQuery($q);
			$db->execute();
		}
	}

	/**
	 * Move existing media files in the proper directories.
	 */
	protected static function moveExistingMedias()
	{
		$site = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_vikrestaurants';

		// menus images

		$media = glob($site . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'menus_images' . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,bmp,gif}', GLOB_BRACE);

		foreach ($media as $img)
		{
			$img_name = str_replace('@small', '', substr($img, strrpos($img, DIRECTORY_SEPARATOR)+1));

			$dest = $site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media' . (strpos($img, '@small') !== false ? '@small' : '') . DIRECTORY_SEPARATOR . $img_name;

			if (!\JFile::copy($img, $dest))
			{
				\JFactory::getApplication()->enqueueMessage("Impossible to copy $img in $dest", "warning");
			}

		}

		// takeaway images

		$media = glob($site . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'takeaway_menus_images' . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,bmp,gif}', GLOB_BRACE);

		foreach ($media as $img) {

			$img_name = str_replace('@small', '', substr($img, strrpos($img, DIRECTORY_SEPARATOR)+1));

			$dest = $site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media' . (strpos($img, '@small') !== false ? '@small' : '') . DIRECTORY_SEPARATOR . $img_name;

			if (!\JFile::copy($img, $dest))
			{
				\JFactory::getApplication()->enqueueMessage("Impossible to copy $img in $dest", "warning");
			}

		}

		// company logo

		$media = glob($site . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'companylogo' . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,bmp,gif}', GLOB_BRACE);

		foreach ($media as $img)
		{
			$img_name = str_replace('@small', '', substr($img, strrpos($img, DIRECTORY_SEPARATOR)+1));

			$dest  = $site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $img_name;
			$dest2 = $site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media@small' . DIRECTORY_SEPARATOR . $img_name;

			if (!\JFile::copy($img, $dest))
			{
				\JFactory::getApplication()->enqueueMessage("Impossible to copy $img in $dest", "warning");
			}

			if (!\JFile::copy($img, $dest2))
			{
				\JFactory::getApplication()->enqueueMessage("Impossible to copy $img in $dest2", "warning");
			}
		}

		// media uploads

		$media = glob($site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media_uploads' . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,bmp,gif}', GLOB_BRACE);

		foreach ($media as $img)
		{
			$img_name = str_replace('@small', '', substr($img, strrpos($img, DIRECTORY_SEPARATOR)+1));

			$dest  = $site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $img_name;
			$dest2 = $site . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'media@small' . DIRECTORY_SEPARATOR . $img_name;

			if (!\JFile::copy($img, $dest))
			{
				\JFactory::getApplication()->enqueueMessage("Impossible to copy $img in $dest", "warning");
			}

			if (!\JFile::copy($img, $dest2))
			{
				\JFactory::getApplication()->enqueueMessage("Impossible to copy $img in $dest2", "warning");
			}
		}
	}
}
