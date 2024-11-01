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
 * VikRestaurants review model.
 *
 * @since 1.9
 */
class VikRestaurantsModelReview extends JModelVRE
{
	/**
	 * Basic item loading implementation.
	 *
	 * @param   mixed    $pk   An optional primary key value to load the row by, or an array of fields to match.
	 *                         If not set the instance property value is used.
	 * @param   boolean  $new  True to return an empty object if missing.
	 *
	 * @return  mixed    The record object on success, null otherwise.
	 */
	public function getItem($pk, $new = false)
	{
		$review = parent::getItem($pk, $new);

		if (!$review)
		{
			return null;
		}

		if (!$review->id)
		{
			// define default values
			$review->rating  = 5;
			$review->langtag = VikRestaurants::getDefaultLanguage();
		}

		$db = JFactory::getDbo();

		if ($review->jid > 0)
		{
			// fetch user details
			$review->user = new JUser($review->jid);
			
			// fetch avatar of the customer that left the review
			$query = $db->getQuery(true)
				->select($db->qn('image'))
				->from($db->qn('#__vikrestaurants_users'))
				->where($db->qn('jid') . ' = ' . (int) $review->jid);

			$db->setQuery($query, 0, 1);
			$review->customerImage = (string) $db->loadResult();
		}
		else
		{
			$review->user = null;
			$review->customerImage = '';
		}

		if ($review->id_takeaway_product > 0)
		{
			// fetch details of the reviewed product
			$query = $db->getQuery(true)
				->select($db->qn(['e.name', 'e.img_path', 'e.description']))
				->select($db->qn('m.title', 'menuTitle'))
				->from($db->qn('#__vikrestaurants_takeaway_menus_entry', 'e'))
				->join('INNER', $db->qn('#__vikrestaurants_takeaway_menus', 'm') . ' ON ' . $db->qn('e.id_takeaway_menu') . ' = ' . $db->qn('m.id'))
				->where($db->qn('e.id') . ' = ' . $review->id_takeaway_product);

			$db->setQuery($query, 0, 1);
			$review->product = $db->loadObject();
		}

		return $review;
	}

	/**
	 * Acts as a save method but applies further validations,
	 * since it assumes that the review is left by a customer.
	 *
	 * @return  bool  True on success, false otherwise.
	 */
	public function leave($data)
	{
		$config = VREFactory::getConfig();

		if (!empty($data['id_takeaway_product']))
		{
			// validate permissions for take-away products reviews
			if (!VikRestaurants::canLeaveTakeAwayReview($data['id_takeaway_product'])) 
			{
				// user cannot leave a review for this take-away product
				$this->setError(JText::translate('VRPOSTREVIEWAUTHERR'));
				return false;
			}

			// make sure the product to review actually exists
			$item = JModelVRE::getInstance('tkentry')->getItem((int) $data['id_takeaway_product']);

			if (!$item)
			{
				// register error message
				$this->setError(JText::sprintf('VRE_INVALID_REQ_FIELD', JText::translate('VRTKCARTROWNOTFOUND')));

				// invalid product
				return false;
			}
		}
		else
		{
			// missing subject
			$this->setError(JText::translate('VRPOSTREVIEWFILLERR'));
			return false;
		}

		if (empty($data['title']) || empty($data['rating']))
		{
			// title or rating are empty
			$this->setError(JText::translate('VRPOSTREVIEWFILLERR'));
			return false;
		}

		if ($config->getBool('revcommentreq') && empty($data['comment']))
		{
			// comment required and empty
			$this->setError(JText::translate('VRPOSTREVIEWFILLERR'));
			return false;
		}

		if (strlen($data['comment']) > 0 && strlen($data['comment']) < $config->getUint('revminlength'))
		{
			// comment length higher than 0 but lower than min length
			$this->setError(JText::translate('VRPOSTREVIEWFILLERR'));
			return false;
		}

		if (!isset($data['published']))
		{
			// rely on global configuration status
			$data['published'] = $config->getUint('revautopublished');
		}

		// check if this is a verified purchaser
		$args['verified'] = (int) VikRestaurants::isVerifiedTakeAwayReview($data['id_takeaway_product']);

		$user = JFactory::getUser();

		if (!$user->guest)
		{
			// always use the details of the current logged-in user
			$data['jid']   = $user->id;
			$data['name']  = $user->username;
			$data['email'] = $user->email;
		}

		// take only the maximum number of characters
		$data['comment'] = mb_substr($data['comment'], 0, $config->getUint('revmaxlength'), 'UTF-8');

		// try to save the review
		$id = $this->save($data);

		if (!$id)
		{
			// unable to save the review
			return false;	
		}

		// send a notification to the administrator(s)
		$this->sendEmailNotification($id, ['check' => true]);

		return true;
	}

	/**
	 * Sends an e-mail notification to the administrator(s) about the newly submitted review.
	 *
	 * @param   int    $id       The review ID.
	 * @param   array  $options  An array of options.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	public function sendEmailNotification(int $id, array $options = [])
	{
		try
		{
			/** @var E4J\VikRestaurants\Mail\MailTemplate */
			$mailTemplate = E4J\VikRestaurants\Mail\MailFactory::getTemplate('takeaway', 'review', $id, $options);

			// in case the "check" attribute is set, we need to make
			// sure whether the specified client should receive the
			// e-mail according to the configuration rules
			if (!empty($options['check']) && !$mailTemplate->shouldSend())
			{
				// configured to avoid receiving this kind of e-mails
				return false;
			}

			// send notification
			$sent = (new E4J\VikRestaurants\Mail\MailDeliverer)->send($mailTemplate->getMail());
		}
		catch (Exception $e)
		{
			// probably order not found, register error message
			$this->setError($e->getMessage());

			return false;
		}

		return $sent;
	}
}
