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
 * - {cancellation_content}  A generic intro of the cancellation.
 * - {cancellation_reason}   The reason specified by the customer, if provided.
 * - {order_link}            A link to the back-end reservations/orders page, filtered by ID.
 *
 * @since 1.9
 */
final class CancellationMailDecorator implements MailTemplateDecorator
{
	/** @var int */
	private $group;

	/** @var \VREOrderWrapper */
	private $order;

	/**
	 * Class constructor.
	 * 
	 * @param  int              $group  The order group (1: restaurant, 2: take-away).
	 * @param  VREOrderWrapper  $order  The order details.
	 */
	public function __construct(int $group, \VREOrderWrapper $order)
	{
		$this->group = $group;
		$this->order = $order;
	}

	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		/** @var E4J\VikRestaurants\Platform\Uri\UriInterface */
		$uri = \VREFactory::getPlatform()->getUri();

		// fetch order link HREF
		$orderLinkHREF = $uri->admin('index.php?option=com_vikrestaurants&view=' . ($this->group == 1 ? 'reservations' : 'tkreservations') . '&ids[]=' . $this->order->id);

		// register additional template data
		$template->addTemplateData([
			'cancellation_content' => \JText::translate($this->group == 1 ? 'VRRESCANCELLEDCONTENT' : 'VRORDERCANCELLEDCONTENT'),
			'cancellation_reason'  => $this->order->cancellation_reason,
			'order_link'           => $orderLinkHREF,
		]);
	}
}
