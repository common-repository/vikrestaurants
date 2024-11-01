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
 * VikRestaurants review table.
 *
 * @since 1.8
 */
class VRETableReview extends JTableVRE
{
	/**
	 * Class constructor.
	 *
	 * @param 	object 	$db  The database driver instance.
	 */
	public function __construct($db)
	{
		parent::__construct('#__vikrestaurants_reviews', 'id', $db);

		// register required fields
		$this->_requiredFields[] = 'name';
		$this->_requiredFields[] = 'title';
		$this->_requiredFields[] = 'rating';
	}

	/**
	 * Method to bind an associative array or object to the Table instance. This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function bind($src, $ignore = array())
	{
		$src = (array) $src;

		if (isset($src['rating']))
		{
			// the rating must be in the range [1,5]
			$src['rating'] = min(array(5, (int) $src['rating']));
			$src['rating'] = max(array(1, (int) $src['rating']));
		}

		if (empty($src['ipaddr']))
		{
			$src['ipaddr'] = JFactory::getApplication()->input->server->getString('REMOTE_ADDR');
		}

		if (empty($src['timestamp']) || $src['timestamp'] == JFactory::getDbo()->getNullDate())
		{
			$src['timestamp'] = VikRestaurants::now();
		}
		else if (!is_numeric($src['timestamp']))
		{
			// fetch datetime timestamp
			list($date, $time) = explode(' ', $src['timestamp']);
			list($hour, $min)  = explode(':', $time);

			$src['timestamp'] = VikRestaurants::createTimestamp($date, $hour, $min);
		}

		if (empty($src['conf_key']))
		{
			// auto-generate confirmation key
			$src['conf_key'] = VikRestaurants::generateSerialCode(12, 'review-confkey');
		}

		if ((empty($src['id']) || isset($src['langtag'])) && empty($src['langtag']))
		{
			// use current lang tag if empty or missing while creating a new review
			$src['langtag'] = JFactory::getLanguage()->getTag();
		}

		// bind the details before save
		return parent::bind($src, $ignore);
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to
	 * ensure they are safe to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		// check integrity using parent
		if (!parent::check())
		{
			return false;
		}
		
		// check rating value
		if (isset($this->rating) && ($this->rating < 1 || $this->rating > 5))
		{
			// register error message
			$this->setError(JText::sprintf('VRE_INVALID_REQ_FIELD', JText::translate('VRMANAGEREVIEW5')));

			// invalid rating
			return false;
		}

		return true;
	}
}
