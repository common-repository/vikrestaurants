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

$reservation = $this->reservation;

?>

<!-- BILL CLOSED - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('bill_closed')
	->checked($reservation->bill_closed)
	->label(JText::translate('VRMANAGERESERVATION11'))
	->description(JText::translate('VRMANAGERESERVATION11_DESC'))
	->onchange('billClosedValueChanged(this.checked)');
?>

<!-- DEPOSIT - Number -->

<?php
if ($is_pending = JHtml::fetch('vrehtml.status.ispending', 'restaurant', $reservation->status))
{
	// explain 
	$description = JText::translate('VRMANAGERESERVATION9_DESC');
}
else if ($reservation->deposit > $reservation->tot_paid && !JHtml::fetch('vrehtml.status.ispaid', 'restaurant', $reservation->status))
{
	// inform the administrator that the deposit (or a part of it) haven't been paid through VikRestaurants
	$description = '<i class="fas fa-exclamation-triangle warn"></i> ' . JText::translate('VRORDERDEPNOTPAID');
}

echo $this->formFactory->createField()
	->type('number')
	->name('deposit')
	->value($reservation->deposit)
	->label(JText::translate('VRMANAGERESERVATION9'))
	->description($description ?? '')
	->readonly(!$is_pending)
	->min(0)
	->step('any')
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<script>
	(function($, w) {
		'use strict';

		w.billClosedValueChanged = (checked) => {
			// trigger open/close event for subscribed listeners
			let event = $.Event('bill.changed');
			
			event.bill = {
				closed: checked,
				open: !checked,
			};

			$(w).trigger(event);
		}
	})(jQuery, window);
</script>