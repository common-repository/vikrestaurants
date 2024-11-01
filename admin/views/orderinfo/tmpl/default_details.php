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
JHtml::fetch('vrehtml.assets.contextmenu');

$config = VREFactory::getConfig();

$vik = VREApplication::getInstance();

// get all supported tables
$allTables = JHtml::fetch('vrehtml.admin.tables');
?>

<h3><?php echo JText::translate('VRCONFIGFIELDSETRESERVATION'); ?></h3>

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

				// unset details form to avoid displaying it twice
				unset($this->addons['details.id']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"details.id","type":"field"} -->

	</div>

	<!-- Status -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGERESERVATION12'); ?></label>

		<div class="order-field-value">
			<?php
			if (!$this->order->closure)
			{
				echo JHtml::fetch('vrehtml.status.display', $this->order->status);
			}
			else
			{
				?><span class="vrreservationstatusclosure"><?php echo JText::translate('VRRESERVATIONSTATUSCLOSURE'); ?></span><?php
			}

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

				// unset details form to avoid displaying it twice
				unset($this->addons['details.status']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"details.status","type":"field"} -->

	</div>

	<!-- Check-in -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGERESERVATION3'); ?></label>

		<div class="order-field-value">
			<b><?php echo $this->order->checkin_lc3; ?></b>

			<?php
			if ($this->order->stay_time)
			{
				$checkout = JText::sprintf(
					'VRECHECKOUTEXT',
					date($config->get('timeformat'), $this->order->checkout),
					VikRestaurants::minutesToStr($this->order->stay_time)
				);

				?><i class="fas fa-stopwatch hasTooltip" title="<?php echo $this->escape($checkout); ?>" style="margin-left:4px;"></i><?php
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
		<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"details.checkin","type":"field"} -->

	</div>

	<!-- People -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGERESERVATION4'); ?></label>

		<div class="order-field-value">
			<b><?php echo $this->order->people; ?></b>&nbsp;
			<?php
			for ($p = 1; $p <= min(array(2, $this->order->people)); $p++)
			{
				?><i class="fas fa-male"></i><?php
			}

			// plugins can use the "details.people" key to introduce custom
			// HTML next to the number of participants
			if (isset($this->addons['details.people']))
			{
				echo $this->addons['details.people'];

				// unset details form to avoid displaying it twice
				unset($this->addons['details.people']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"details.people","type":"field"} -->

	</div>

	<!-- Table -->

	<div class="order-field">

		<label><?php echo JText::translate('VRMANAGERESERVATION5'); ?></label>

		<div class="order-field-value">
			<span class="badge badge-important"><?php echo $this->order->room_name; ?></span>

			<?php
			foreach ($this->order->tables as $table)
			{
				?>
				<span class="badge badge-info table-handle" data-order-id="<?php echo (int) $table->id_order; ?>" data-table-id="<?php echo (int) $table->id; ?>">
					<?php echo $table->name; ?>
				</span>
				<?php
			}
			
			if (strip_tags((string) $this->order->notes))
			{
				$notes = $this->order->notes;
				// always obtain short description, if any
				$vik->onContentPrepare($notes, false);

				?><i class="fas fa-sticky-note hasTooltip" title="<?php echo $this->escape($notes->text); ?>" style="margin-left: 4px;"></i><?php
			}

			// plugins can use the "details.table" key to introduce custom
			// HTML next to the selected tables
			if (isset($this->addons['details.table']))
			{
				echo $this->addons['details.table'];

				// unset details form to avoid displaying it twice
				unset($this->addons['details.table']);
			}
			?>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"details.table","type":"field"} -->

	</div>

	<!-- Operator -->

	<?php if ($operators = JHtml::fetch('vikrestaurants.operators', $group = 1)): ?>

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

					// unset details form to avoid displaying it twice
					unset($this->addons['details.operator']);
				}
				?>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewOrderinfo","key":"details.operator","type":"field"} -->

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
							'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=reservation.assignoperatorajax&tmpl=component'); ?>',
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

<script>
	<?php
	/**
	 * Implemented a quick and free way of assigning the
	 * reservation to a different table. It is mainly useful
	 * for those reservations that belong to a parent (cluster),
	 * since they are not editable.
	 *
	 * @since 1.8.3
	 */
	?>
	(function($) {
		'use strict';

		$(function() {
			// Helper function used to submit the selected table.
			const tableActionCallback = function(root, event) {
				// get table currently selected
				let prevId   = parseInt($(root).attr('data-table-id'))
				let prevText = $(root).text();

				// replace with selected ID
				$(root).attr('data-table-id', this.id);
				$(root).text(this.text);

				// make AJAX request
				UIAjax.do(
					'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=reservation.changetableajax'); ?>',
					{
						id_table: this.id,
						id_order: $(root).data('order-id'),
					},
					(resp) => {
						// all fine
					},
					(error) => {
						// something went wrong, restore previous value and text
						$(root).attr('data-table-id', prevId).text(prevText);	
					}
				);
			};

			// Helper function used to check whether the table should
			// be selectable or not. Only the tables already selected
			// are marked as disabled.
			const tableDisabledCallback = function(root, config) {
				let tables = [];

				// retrieve list of selected tables
				$('.table-handle').each(function() {
					tables.push(parseInt($(this).attr('data-table-id')));
				});

				// make sure the table is not selected
				return tables.indexOf(this.id) !== -1;
			};

			// Helper function used to check whether the table should
			// be displayed or not. The tables that already own a
			// reservation for the current time will be excluded.
			const tableVisibleCallback = function(root, config) {
				// load tables occupancy
				const occupancy = <?php echo json_encode($this->occupiedTables); ?>;

				// display table only if not occupied
				return occupancy.indexOf(this.id) === -1;
			};

			$('.table-handle').vikContextMenu({
				clickable: true,
				class: 'tables-context-menu',
				buttons: [
					<?php
					foreach ($allTables as $room)
					{
						foreach ($room as $table)
						{
							?>
							{
								id: <?php echo $table->value; ?>,
								text: '<?php echo addslashes($table->text); ?>',
								separator: <?php echo end($room) === $table ? 'true' : 'false'; ?>,
								action: tableActionCallback,
								disabled: tableDisabledCallback,
								visible: tableVisibleCallback,
							},
							<?php
						}
					}
					?>
				],
			});
		});
	})(jQuery);
</script>