<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Decorators;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplate;
use E4J\VikRestaurants\Mail\MailTemplateDecorator;

/**
 * Adds support to the following template data (tags):
 * 
 * - {order_people}       The number of participants (as "N people" or "1 person").
 * - {order_room}         The name of the room booked.
 * - {order_room_table}   The name of the table(s) booked (separated by a comma).
 * - {order_deposit}      The total deposit to leave, if any.
 * - {order_link}         A link to the reservation details page.
 * - {cancellation_link}  A quick link to self-cancel the reservation (as customer).
 * - {confirmation_link}  A quick link to self-confirm the reservation (as customer or administrator).
 * - {rejection_link}     A quick link to reject the reservation (as administrator).
 *
 * @since 1.9
 */
final class RestaurantReservationMailDecorator implements MailTemplateDecorator
{
	/** @var \VREOrderRestaurant */
	private $reservation;

	/** @var string */
	private $langTag;

	/**
	 * Class constructor.
	 * 
	 * @param  VREOrderRestaurant  $reservation  The reservation details.
	 * @param  string              $langTag      The selected language tag.
	 */
	public function __construct(\VREOrderRestaurant $reservation, string $langTag)
	{
		$this->reservation = $reservation;
		$this->langTag     = $langTag;
	}

	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		/** @var E4J\VikRestaurants\Currency\Currency */
		$currency = \VREFactory::getCurrency();

		// implode all tables names
		$tables = implode(', ', array_map(function($table)
		{
			return $table->name;
		}, $this->reservation->tables));

		/** @var E4J\VikRestaurants\Platform\Uri\UriInterface */
		$uri = \VREFactory::getPlatform()->getUri();

		// fetch order link HREF
		$orderLinkHREF = $uri->route("index.php?option=com_vikrestaurants&view=reservation&ordnum={$this->reservation->id}&ordkey={$this->reservation->sid}&lang={$this->langTag}");

		// fetch cancellation link HREF
		$cancellationLinkHREF = $orderLinkHREF . '#cancel';

		// fetch confirmation link HREF
		$confirmationLinkHREF = $uri->route("index.php?option=com_vikrestaurants&task=reservation.approve&oid={$this->reservation->id}&conf_key={$this->reservation->conf_key}&lang={$this->langTag}");

		// fetch rejection link HREF
		$rejectionLinkHREF = $uri->route("index.php?option=com_vikrestaurants&task=reservation.reject&oid={$this->reservation->id}&conf_key={$this->reservation->conf_key}&lang={$this->langTag}");

		// register  template data
		$template->addTemplateData([
			'order_people'      => \JText::plural('VRE_N_PEOPLE', $this->reservation->people),
			'order_room'        => $this->reservation->room->name,
			'order_room_table'  => $tables,
			'order_deposit'     => $currency->format($this->reservation->deposit),
			'order_pin_code'    => $this->reservation->pin,
			'order_link'        => $orderLinkHREF,
			'cancellation_link' => $cancellationLinkHREF,
			'confirmation_link' => $confirmationLinkHREF,	
			'rejection_link'    => $rejectionLinkHREF,	
		]);
	}
}
