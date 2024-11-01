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

JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.fontawesome');

$reservation = $this->reservation;

/**
 * In case of logged-in user, display a button to access the profile page of the user,
 * which contains all the reservations that have been booked.
 * If you wish to avoid displaying that button, just comment the line below.
 * 
 * @since 1.9
 */
echo $this->loadTemplate('backbutton');

// Check whether to display the payment form within the top position of this view.
// The payment will be displayed here only in case the position match one of these:
// top-left, top-center, top-right.
echo $this->displayPayment('top');

?>

<div class="vrorderpagediv">

	<?php
	// display the block containing the order details, such as the order number and the status
	echo $this->loadTemplate('orderdetails');

	// display the block containing the reservation details, such as the check-in and the customer
	echo $this->loadTemplate('reservation');

	if ($reservation->menus)
	{
		// display the block containing the booked menus
		echo $this->loadTemplate('menus');
	}

	if ($reservation->items)
	{
		// display the block containing the ordered food
		echo $this->loadTemplate('items');
	}
	?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayReservationSummary","type":"sitepage"} -->

	<?php
	/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
	$dispatcher = VREFactory::getPlatform()->getDispatcher();

	/**
	 * Trigger event to let the plugins add custom HTML contents below
	 * the reservation summary. In case more than one plugin returned a string,
	 * they will be displayed in different blocks.
	 *
	 * @param   object  $reservation  The object holding the reservation details.
	 *
	 * @return  string  The HTML to display.
	 *
	 * @since   1.9
	 */
	$result = $dispatcher->filter('onDisplayReservationSummary', [$reservation]);

	/** @var E4J\VikRestaurants\Event\EventResponse $result */

	foreach ($result as $block)
	{
		if (!$block)
		{
			continue;
		}

		?>
		<div class="vrorderboxcontent"><?php echo $block; ?></div>
		<?php
	}
	?>
</div>

<?php
// Check whether to display the payment form within the bottom position of this view.
// The payment will be displayed here only in case the position match one of these:
// bottom-left, bottom-center, bottom-right (or not specified).
echo $this->displayPayment('bottom');
