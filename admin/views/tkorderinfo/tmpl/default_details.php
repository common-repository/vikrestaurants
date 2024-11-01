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

JHtml::fetch('formbehavior.chosen');

$config = VREFactory::getConfig();

$vik = VREApplication::getInstance();

?>

<h3><?php echo JText::translate('VRTKORDERCARTFIELDSET1'); ?></h3>

<div class="order-fields">

	<!-- Order ID -->

	<div class="order-field">

		<label>
			<?php echo JText::translate('VRMANAGERESERVATION1'); ?>
		</label>

		<div class="order-field-value">
			<b><?php echo $this->order->id . '-' . $this->order->sid; ?></b>

			<?php
			if ($this->order->author)
			{
				$creation = JText::sprintf(
					'VRRESLISTCREATEDTIP',
					JHtml::fetch('date', $this->order->created_on, JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'), date_default_timezone_get()),
					$this->order->author->name
				);

				?><i class="fas fa-calendar-check hasTooltip" title="<?php echo $this->escape($creation); ?>" style="margin-left:4px;"></i><?php
			}

			// plugins can use the "details.id" key to introduce custom
			// HTML next to the order number
			if (isset($this->addons['details.id']))
			{
				echo $this->addons['details.id'];

				// unset details ID form to avoid displaying it twice
				unset($this->addons['details.id']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"details.id","type":"field"} -->

	</div>

	<!-- Status -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGERESERVATION12'); ?></label>

		<div class="order-field-value">
			<?php echo JHtml::fetch('vrehtml.status.display', $this->order->status); ?>

			<?php
			// show remaining time available to accept the reservation
			if ($this->order->statusRole == 'PENDING')
			{
				if ($this->order->locked_until > time())
				{
					$expires_in = JText::sprintf(
						'VRTKRESEXPIRESIN',
						VikRestaurants::formatTimestamp($config->get('timeformat'), $this->order->locked_until, $local = false)
					);

					?><i class="fas fa-question-circle hasTooltip" title="<?php echo $this->escape($expires_in); ?>" style="margin-left:4px;"></i><?php
				}
			}

			// plugins can use the "details.status" key to introduce custom
			// HTML next to the reservation status
			if (isset($this->addons['details.status']))
			{
				echo $this->addons['details.status'];

				// unset details status form to avoid displaying it twice
				unset($this->addons['details.status']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"details.status","type":"field"} -->

	</div>

	<!-- Check-in -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGERESERVATION3'); ?></label>

		<div class="order-field-value">
			<b><?php echo $this->order->checkin_lc3; ?></b>

			<?php
			if ($this->order->preparation_ts)
			{
				// subtract a time slot from the preparation time
				$this->order->preparation_ts = strtotime('-' . $config->get('tkminint') . ' minutes', $this->order->preparation_ts);
				// fetch preparation time hint
				$prepTip = JText::sprintf('VRE_TKRES_PREP_TIME_HINT', date($config->get('timeformat'), $this->order->preparation_ts));

				?>
				<div style="font-weight: normal;display: inline-block;">
					<i class="fas fa-info-circle hasTooltip" title="<?php echo $this->escape($prepTip); ?>" style="margin-left:4px;"></i>
				</div>
				<?php
			}

			if ($this->order->asap)
			{
				?>
				<div style="font-weight: normal;display: inline-block;">
					<i class="fas fa-shipping-fast hasTooltip" title="<?php echo $this->escape(JText::translate('VRMANAGETKRESASAPSHORT')); ?>" style="margin-left:4px;"></i>
				</div>
				<?php
			}

			// plugins can use the "details.status" key to introduce custom
			// HTML next to the reservation status
			if (isset($this->addons['details.checkin']))
			{
				echo $this->addons['details.checkin'];

				// unset details check-in form to avoid displaying it twice
				unset($this->addons['details.checkin']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"details.checkin","type":"field"} -->

	</div>

	<!-- Service -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGETKRES13'); ?></label>

		<div class="order-field-value">
			<b>
				<?php
				/** @var array (associative) */
				$services = E4J\VikRestaurants\CustomFields\Factory::getSupportedServices();
				echo $services[$this->order->service] ?? $this->order->service;
				?>
			</b>

			<?php
			// in case of delivery service and route options, we might suggest here
			// the maximum time to start the delivery of the order
			if ($this->order->service === 'delivery' && $this->order->route && !empty($this->order->route->distancetext))
			{
				// fetch route information
				$hint = JText::sprintf(
					'VRTK_ADDR_ROUTE_NOTES',
					$this->order->route->distancetext ?? '--',
					$this->order->route->durationtext ?? '--'
				);

				// fetch delivery time
				$leave_at = strtotime('-' . $this->order->route->duration . ' seconds', $this->order->checkin_ts);
				// format delivery time
				$leave_at = date($config->get('timeformat'), $leave_at);

				$hint .= '<br /><br />' . JText::sprintf('VRTK_ADDR_ROUTE_START', $leave_at);
				?>
				<i class="fas fa-truck hasTooltip" title="<?php echo $this->escape($hint); ?>" style="margin-left:4px;"></i>
				<?php
			}

			if (strip_tags((string) $this->order->notes))
			{
				$notes = $this->order->notes;
				// always obtain short description, if any
				$vik->onContentPrepare($notes, false);

				?><i class="fas fa-sticky-note hasTooltip" title="<?php echo $this->escape($notes->text); ?>" style="margin-left:4px;"></i><?php
			}

			// plugins can use the "details.service" key to introduce custom
			// HTML next to the reservation service
			if (isset($this->addons['details.service']))
			{
				echo $this->addons['details.service'];

				// unset details service form to avoid displaying it twice
				unset($this->addons['details.service']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"details.service","type":"field"} -->

	</div>

	<!-- Address -->

	<?php if ($this->order->service === 'delivery'): ?>

		<div class="order-field">

			<label><?php echo JText::translate('VRMANAGETKRES29'); ?></label>

			<div class="order-field-value">
				<b><?php echo $this->order->purchaser_address; ?></b>

				<?php
				// plugins can use the "details.address" key to introduce custom
				// HTML next to the reservation address
				if (isset($this->addons['details.address']))
				{
					echo $this->addons['details.address'];

					// unset details address form to avoid displaying it twice
					unset($this->addons['details.address']);
				}
				?>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"details.service","type":"field"} -->

		</div>
	
	<?php endif; ?>

	<!-- Operator -->

	<?php if ($operators = JHtml::fetch('vikrestaurants.operators', $group = 2)): ?>

		<div class="order-field">

			<label><?php echo JText::translate('VROPERATORFIELDSET1'); ?></label>

			<div class="order-field-value">
				<select id="operator-assign">
					<option value="0">--</option>
					<?php echo JHtml::fetch('select.options', $operators, 'value', 'text', $this->order->id_operator); ?>
				</select>

				<?php
				// plugins can use the "details.operator" key to introduce custom
				// HTML next to the assigned operator
				if (isset($this->addons['details.operator']))
				{
					echo $this->addons['details.operator'];

					// unset details operator form to avoid displaying it twice
					unset($this->addons['details.operator']);
				}
				?>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewTkorderinfo","key":"details.operator","type":"field"} -->

		</div>

		<script>
			(function($) {
				'use strict';

				$(function() {
					VikRenderer.chosen('#operator-assign', 200);

					// take current operator
					let ID_OPERATOR = <?php echo (int) $this->order->id_operator; ?>;

					$('#operator-assign').on('change', function() {
						// disable dropdown until the end of the request
						var _select = $(this).disableChosen(true);

						UIAjax.do(
							'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=tkreservation.assignoperatorajax&tmpl=component'); ?>',
							{
								id: <?php echo $this->order->id; ?>,
								id_operator: _select.val(),
							},
							(resp) => {
								// operator assigned successfully, unlock dropdown
								_select.disableChosen(false);
								// update cached operator
								ID_OPERATOR = _select.val();
							},
							(err) => {
								// an error occurred, unlock dropdown
								_select.disableChosen(false);
								// revert to previous operator
								_select.updateChosen(ID_OPERATOR);
							}
						);

					});
				});
			})(jQuery);
		</script>
	
	<?php endif; ?>

</div>
