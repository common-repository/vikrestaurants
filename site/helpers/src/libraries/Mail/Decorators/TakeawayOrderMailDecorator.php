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
 * - {order_delivery_service}  The selected service (delivery/takeaway).
 * - {order_total_cost}        The formatted total gross.
 * - {order_total_net}         The formatted total net.
 * - {order_total_tax}         The formatted total taxes.
 * - {order_delivery_charge}   The formatted delivery charge (inclusive of taxes).
 * - {order_total_tip}         The formatted total tip.
 * - {order_link}              A link to the order details page.
 * - {cancellation_link}       A quick link to self-cancel the order (as customer).
 * - {confirmation_link}       A quick link to self-confirm the order (as customer or administrator).
 * - {rejection_link}          A quick link to reject the order (as administrator).
 * - {track_order_link}        A quick link to the status history of the order.
 *
 * @since 1.9
 */
final class TakeawayOrderMailDecorator implements MailTemplateDecorator
{
	/** @var \VREOrderTakeaway */
	private $order;

	/** @var string */
	private $langTag;

	/**
	 * Class constructor.
	 * 
	 * @param  VREOrderTakeaway  $order    The order details.
	 * @param  string            $langTag  The selected language tag.
	 */
	public function __construct(\VREOrderTakeaway $order, string $langTag)
	{
		$this->order   = $order;
		$this->langTag = $langTag;
	}

	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		/** @var E4J\VikRestaurants\Currency\Currency */
		$currency = \VREFactory::getCurrency();

		/** @var E4J\VikRestaurants\Platform\Uri\UriInterface */
		$uri = \VREFactory::getPlatform()->getUri();

		// fetch order link HREF
		$orderLinkHREF = $uri->route("index.php?option=com_vikrestaurants&view=order&ordnum={$this->order->id}&ordkey={$this->order->sid}&lang={$this->langTag}");

		// fetch cancellation link HREF
		$cancellationLinkHREF = $orderLinkHREF . '#cancel';

		// fetch confirmation link HREF
		$confirmationLinkHREF = $uri->route("index.php?option=com_vikrestaurants&task=order.approve&oid={$this->order->id}&conf_key={$this->order->conf_key}&lang={$this->langTag}");

		// fetch rejection link HREF
		$rejectionLinkHREF = $uri->route("index.php?option=com_vikrestaurants&task=order.reject&oid={$this->order->id}&conf_key={$this->order->conf_key}&lang={$this->langTag}");

		// fetch tracking link HREF
		$trackOrderLinkHREF = $uri->route("index.php?option=com_vikrestaurants&view=trackorder&oid={$this->order->id}&sid={$this->order->sid}&lang={$this->langTag}");

		// register template data
		$template->addTemplateData([
			'order_delivery_service' => \JHtml::fetch('vikrestaurants.tkservice', $this->order->service),
			'order_total_cost'       => $currency->format($this->order->total_to_pay),
			'order_total_net'        => $currency->format($this->order->total_net),
			'order_total_tax'        => $currency->format($this->order->total_tax),
			'order_delivery_charge'  => $currency->format($this->order->delivery_charge + $this->order->delivery_tax),
			'order_total_tip'        => $currency->format($this->order->tip_amount),
			'order_link'             => $orderLinkHREF,
			'cancellation_link'      => $cancellationLinkHREF,
			'confirmation_link'      => $confirmationLinkHREF,
			'rejection_link'         => $rejectionLinkHREF,
			'track_order_link'       => \JText::sprintf('VRTRACKORDERCHECKLINK', $trackOrderLinkHREF),
		]);
	}
}
