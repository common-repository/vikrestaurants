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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.fontawesome');

$reservation = $this->reservation;

$vik = VREApplication::getInstance();

// always use default tab while creating a new record
$active_tab = $reservation->id ? $this->getActiveTab('reservation_details', $reservation->id) : 'reservation_details';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewReservation". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$this->forms = $this->onDisplayView();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->bootStartTabSet('reservation', ['active' => $active_tab, 'cookie' => $this->getCookieTab($reservation->id)->name]); ?>

		<!-- DETAILS -->
			
		<?php
		echo $vik->bootAddTab('reservation', 'reservation_details', JText::translate('JDETAILS'));
		echo $this->loadTemplate('details');
		echo $vik->bootEndTab();
		?>

		<!-- BILL -->

		<?php
		echo $vik->bootAddTab('reservation', 'reservation_bill', JText::translate('VRBILL'));
		echo $this->loadTemplate('bill');
		echo $vik->bootEndTab();
		?>

		<!-- FIELDS -->

		<?php
		echo $vik->bootAddTab('reservation', 'reservation_fields', JText::translate('VRMANAGECUSTOMERTITLE3'));
		echo $this->loadTemplate('fields');
		echo $vik->bootEndTab();
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservation","type":"tab"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the nav bar as custom sections.
		 *
		 * @since 1.9
		 */
		foreach ($this->forms as $formName => $formHtml)
		{
			$title = JText::translate($formName);

			// fetch form key
			$key = strtolower(preg_replace("/[^a-zA-Z0-9_]/", '', $title));

			if (!preg_match("/^reservation_/", $key))
			{
				// keep same notation for fieldset IDs
				$key = 'reservation_' . $key;
			}

			echo $vik->bootAddTab('reservation', $key, $title);
			echo $formHtml;
			echo $vik->bootEndTab();
		}
		?>

	<?php echo $vik->bootEndTabSet(); ?>

	<?php echo JHtml::fetch('form.token'); ?>

	<?php if ($this->returnTask): ?>
		<input type="hidden" name="from" value="<?php echo $this->escape($this->returnTask); ?>" />
	<?php endif; ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $reservation->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
// customer management modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-managecustomer',
	array(
		'title'       => '<span class="customer-title"></span>',
		'closeButton' => true,
		'keyboard'    => false, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
		'footer'      => '<button type="button" class="btn btn-success" data-role="customer.save">' . JText::translate('JAPPLY') . '</button>',
	)
);

// busy modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-busytime',
	array(
		'title'       => JText::translate('VRRESBUSYMODALTITLE'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);

$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('JTOOLBAR_DELETE') . '</button>';
$footer .= '<button type="button" class="btn" data-role="back" style="float:right;">' . JText::translate('JTOOLBAR_BACK') . '</button>';

// render inspector to manage menu sections
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'reservation-item-inspector',
	array(
		'title'       => JText::translate('VRMANAGETKRES16'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => '80vw',
	),
	$this->loadTemplate('bill_items_modal')
);

JText::script('VRE_AJAX_GENERIC_ERROR');
?>

<script>
	(function($, w) {
		'use strict';

		w.IS_AJAX_CALLING = false;

		$(function() {
			w.reservationValidator = new VikFormValidator('#adminForm');

			// do not submit the form in case we have any pending requests
			w.reservationValidator.addCallback(function() {
				if (IS_AJAX_CALLING || UIAjax.isDoing()) {
					/**
					 * @todo 	Should we prompt an alert?
					 * 			e.g. "Please wait for the request completion."
					 */

					return false;
				}

				return true;
			});

			// register submit callback in a local variable
			w.ManageReservationSubmitButtonCallback = (task) => {
				if (task.indexOf('save') === -1 || w.reservationValidator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}

			Joomla.submitbutton = ManageReservationSubmitButtonCallback;
		});
	})(jQuery, window);
</script>