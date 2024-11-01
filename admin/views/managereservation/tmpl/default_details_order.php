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

<!-- STATUS - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('status')
	->class('vr-status-sel')
	->value($reservation->status)
	->label(JText::translate('VRMANAGERESERVATION12'))
	->options(JHtml::fetch('vrehtml.admin.statuscodes', 'restaurant'));
?>

<!-- PAYMENTS Select -->

<?php
// get supported payments
$payments = JHtml::fetch('vrehtml.admin.payments', 'restaurant', $blank = '', $group = true, $costs = true);

if (count($payments) > 1)
{
	echo $this->formFactory->createField()
		->type('groupedlist')
		->name('id_payment')
		->value($reservation->id_payment)
		->label(JText::translate('VRMANAGERESERVATION20'))
		->options($payments);
}
?>

<!-- NOTIFY CUSTOMER - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('notifycust')
	->label(JText::translate('VRMANAGERESERVATION15'));
?>

<?php
JText::script('VRE_FILTER_SELECT_PAYMENT');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('select[name="id_payment"]').select2({
				placeholder: '--',
				allowClear: true,
				width: '90%',
			});

			$('.vr-status-sel').select2({
				allowClear: false,
				width: '90%',
			});

			// auto-toggle notify customer after selecting a "confirmed" status
			$('select[name="status"]').on('change', function() {
				let status = $(this).val();

				// update any other select too
				$('.vr-status-sel').not(this).select2('val', status);

				// get confirmed status
				const CONFIRMED = '<?php echo JHtml::fetch('vrehtml.status.confirmed', 'restaurant', 'code'); ?>';

				if (status == CONFIRMED) {
					$('input[name="notifycust"]').prop('checked', true).trigger('change');
				}
			});
		});
	})(jQuery);
</script>