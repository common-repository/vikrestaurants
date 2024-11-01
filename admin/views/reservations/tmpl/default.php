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
JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.status.contextmenu', 'restaurant', '.status-hndl');

$rows = $this->rows;

$filters = $this->filters;

$currency = VREFactory::getCurrency();

$config = VREFactory::getConfig();

$created_by_default = JText::translate('VRMANAGERESERVATION23');

$vik = VREApplication::getInstance();

// get listable columns
$listable_fields = VikRestaurants::getListableFields();
// get custom fields that should be displayed in the list
$listable_cf = VikRestaurants::getListableCustomFields();

// get all reservation codes
$allCodes = JHtml::fetch('vikrestaurants.rescodes', 1);

$canEdit      = JFactory::getUser()->authorise('core.edit', 'com_vikrestaurants');
$canEditState = JFactory::getUser()->authorise('core.edit.state', 'com_vikrestaurants');

$is_searching = $this->hasFilters();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewReservationsList". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayListView($is_searching);

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append">
			<input type="text" name="search" id="vrkeysearch" size="32" 
				value="<?php echo $this->escape($filters['search']); ?>" placeholder="<?php echo $this->escape(JText::translate('JSEARCH_FILTER_SUBMIT')); ?>" />

			<button type="submit" class="btn">
				<i class="fas fa-search"></i>
			</button>
		</div>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservationsList","type":"search","key":"search"} -->

		<?php
		// plugins can use the "search" key to introduce custom
		// filters within the search bar
		if (isset($forms['search']))
		{
			echo $forms['search'];
		}
		?>

		<div class="btn-group pull-left hidden-phone">
			<button type="button" class="btn <?php echo ($is_searching ? 'btn-primary' : ''); ?>" onclick="vrToggleSearchToolsButton(this);">
				<?php echo JText::translate('JSEARCH_TOOLS'); ?>&nbsp;<i class="fas fa-caret-<?php echo ($is_searching ? 'up' : 'down'); ?>" id="vr-tools-caret"></i>
			</button>
		</div>
		
		<div class="btn-group pull-left">
			<button type="button" class="btn" onclick="clearFilters();">
				<?php echo JText::translate('JSEARCH_FILTER_CLEAR'); ?>
			</button>
		</div>

		<?php if (count($rows) == 1 && $rows[0]['cc_details']): ?>
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-primary" onclick="openCreditCardModal(<?php echo (int) $rows[0]['id']; ?>);">
					<i class="fas fa-credit-card" style="margin-right: 4px;"></i>&nbsp;<?php echo JText::translate('VRSEECCDETAILS'); ?>
				</button>
			</div>
		<?php endif; ?>
	</div>

	<div class="btn-toolbar hidden-phone" id="vr-search-tools" style="height: 32px;<?php echo ($is_searching ? '' : 'display: none;'); ?>">

		<?php
		// get order statuses
		$options = JHtml::fetch('vrehtml.admin.statuscodes', 'restaurant', $blank = true);

		// add closure status too
		$options[] = JHtml::fetch('select.option', 'CLOSURE', JText::translate('VRRESERVATIONSTATUSCLOSURE'));
		?>
		<div class="btn-group pull-left">
			<select name="status" id="vr-status-sel" class="<?php echo ($filters['status'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['status']); ?>
			</select>
		</div>

		<?php
		// get rooms
		$options = JHtml::fetch('vikrestaurants.rooms', $isAdmin = true);

		// add empty option
		array_unshift($options, JHtml::fetch('select.option', 0, JText::translate('VRMAPSCHOOSEROOM')));
		?>
		<div class="btn-group pull-left">
			<select name="id_room" id="vr-room-sel" class="<?php echo ($filters['id_room'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
				<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['id_room']); ?>
			</select>
		</div>

		<?php
		// get operators
		$options = JHtml::fetch('vikrestaurants.operators', $group = 1);

		if ($options)
		{
			// add empty option
			array_unshift($options, JHtml::fetch('select.option', 0, JText::translate('VRE_FILTER_SELECT_OPERATOR')));
			?>
			<div class="btn-group pull-left">
				<select name="id_operator" id="vr-operator-sel" class="<?php echo ($filters['id_operator'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
					<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['id_operator']); ?>
				</select>
			</div>
			<?php
		}
		?>

		<div class="btn-group pull-left vr-toolbar-setfont">
			<?php
			$attr = array();
			$attr['class']    = 'vrdatefilter';
			$attr['onChange'] = "document.adminForm.submit();";

			echo $vik->calendar($filters['date'], 'date', 'vrdatefilter', null, $attr);
			?>
		</div>

		<?php
		if ($filters['date'])
		{
			// get working shifts
			$options = JHtml::fetch('vrehtml.admin.dayshifts', 1, $filters['date'], 'interval');

			// make sure the working shifts are available for the searched day
			if ($options)
			{
				array_unshift($options, JText::translate('VRRESERVATIONSHIFTSEARCH'));
				?>
				<div class="btn-group pull-left">
					<select name="shift" id="vr-shift-sel" class="<?php echo ($filters['shift'] ? 'active' : ''); ?>" onchange="document.adminForm.submit();">
						<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $filters['shift'], true); ?>
					</select>
				</div>
				<?php
			}
		}
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewReservationsList","type":"search","key":"filters"} -->

		<?php
		// plugins can use the "filters" key to introduce custom
		// filters within the search bar
		if (isset($forms['filters']))
		{
			echo $forms['filters'];
		}
		?>

	</div>

	<?php
	if ($filters['ids'] && count($rows) > 0)
	{
		echo $vik->alert(JText::sprintf(
			'VRFILTERCIDRES', 
			'<strong>' . $rows[0]['table_name'] . '</strong>', 
			'<strong>' . date($config->get('dateformat') . " @ " . $config->get('timeformat'), $rows[0]['checkin_ts']) . '</strong>'
		), 'info');
	}
	?>
	
<?php
if (count($rows) == 0)
{
	echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
}
else
{
	/**
	 * Trigger event to display custom columns.
	 *
	 * @since 1.9
	 */
	$columns = $this->onDisplayTableColumns();
	?>

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayReservationsTableTH","type":"th"} -->

	<!-- Define role to detect the supported hook -->
	<!-- {"rule":"customizer","event":"onDisplayReservationsTableTD","type":"td"} -->

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="<?php echo $vik->getAdminTableClass(); ?>">
		
		<?php echo $vik->openTableHead(); ?>
			<tr>

				<th width="1%">
					<?php echo $vik->getAdminToggle(count($rows)); ?>
				</th>

				<!-- ORDER NUMBER -->
				
				<?php if (in_array('id', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="4%" style="text-align: left;">
						<?php echo JHtml::fetch('vrehtml.admin.sort', 'JGRID_HEADING_ID', 'r.id', $this->orderDir, $this->ordering); ?>
					</th>
				<?php endif; ?>

				<!-- ORDER KEY -->

				<?php if (in_array('sid', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="10%" style="text-align: left;">
						<?php
						if (in_array('id', $listable_fields))
						{
							// ID column shown, display plain text
							echo JText::translate('VRMANAGERESERVATION2');	
						}
						else
						{
							// ID column hidden, add the possibility to sort by ID here
							echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGERESERVATION2', 'r.id', $this->orderDir, $this->ordering);
						}
						?>
					</th>
				<?php endif; ?>

				<!-- CHECK-IN -->

				<?php if (in_array('checkin_ts', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="10%" style="text-align: left;">
						<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGERESERVATION3', 'r.checkin_ts', $this->orderDir, $this->ordering); ?>
					</th>
				<?php endif; ?>

				<!-- CUSTOMER -->

				<?php if (in_array('customer', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left'); ?>" width="10%" style="text-align: left;">
						<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGERESERVATION17', 'r.purchaser_nominative', $this->orderDir, $this->ordering); ?>
					</th>
				<?php endif; ?>

				<!-- PHONE NUMBER -->

				<?php if (in_array('phone', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="8%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGERESERVATION16'); ?>
					</th>
				<?php endif; ?>

				<!-- CUSTOM FIELDS -->

				<?php
				/**
				 * Iterate all the custom fields that should be shown within the head of the table.
				 *
				 * @since 1.7.4
				 */
				foreach ($this->customFields as $field)
				{
					if (in_array($field->id, $listable_cf))
					{
						?>
						<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="10%" style="text-align: center;">
							<?php echo JText::translate($field->getName()); ?>
						</th>
						<?php
					}
				}
				?>

				<!-- CUSTOM -->

				<?php
				/**
				 * Display here the custom columns fetched by third-party plugins.
				 *
				 * @since 1.9
				 */
				foreach ($columns as $k => $col): ?>
					<th data-id="<?php echo $this->escape($k); ?>" class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>">
						<?php echo $col->th; ?>
					</th>
				<?php endforeach; ?>

				<!-- INFO -->

				<?php if (in_array('info', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass(); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRINFO'); ?>
					</th>
				<?php endif; ?>

				<!-- BILL -->

				<?php if (in_array('billval', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="8%" style="text-align: left;">
						<?php echo JHtml::fetch('vrehtml.admin.sort', 'VRMANAGERESERVATION10', 'r.bill_value', $this->orderDir, $this->ordering); ?>
					</th>
				<?php endif; ?>

				<!-- PAYMENT -->

				<?php if (in_array('payment', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="10%" style="text-align: left;">
						<?php echo JText::translate('VRMANAGERESERVATION20'); ?>
					</th>
				<?php endif; ?>

				<!-- COUPON -->

				<?php if (in_array('coupon', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGERESERVATION8'); ?>
					</th>
				<?php endif; ?>

				<!-- ROOM -->

				<?php if (in_array('rname', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="7%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGETABLE4'); ?>
					</th>
				<?php endif; ?>

				<!-- TABLE -->

				<?php if (in_array('tname', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGERESERVATION5'); ?>
					</th>
				<?php endif; ?>

				<!-- RESERVATION CODE -->

				<?php if (in_array('rescode', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('hidden-phone'); ?>" width="5%" style="text-align: center;">
						<?php echo JText::translate('VRMANAGERESERVATION19'); ?>
					</th>
				<?php endif; ?>

				<!-- STATUS -->

				<?php if (in_array('status', $listable_fields)): ?>
					<th class="<?php echo $vik->getAdminThClass('left hidden-phone'); ?>" width="10%" style="text-align: left;">
						<?php echo JText::translate('VRMANAGERESERVATION12'); ?>
					</th>
				<?php endif; ?>
				
			</tr>
		<?php echo $vik->closeTableHead(); ?>

		<?php
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];

			$oid_tooltip = '';

			if ($row['created_on'] > 0)
			{
				if ($row['created_by'] > 0)
				{
					$created_by = $row['createdby_name'];
				}
				else
				{
					$created_by = $created_by_default;
				}

				$oid_tooltip = JText::sprintf('VRRESLISTCREATEDTIP', date($config->get('dateformat') . ' ' . $config->get('timeformat'), $row['created_on']), $created_by);
			}

			/**
			 * Adjust attributes for CLOSURE reservation.
			 *
			 * @since 1.8
			 */
			if ($row['closure'])
			{
				// do not show (CLOSURE) nominative
				$row['purchaser_nominative'] = '';
				// use a different status
				$row['status'] = 'CLOSURE';
			}

			// decode stored CF data
			$cf_json = $row['custom_f'] ? (array) json_decode($row['custom_f'], true) : [];

			/**
			 * Translate custom fields values stored in the database.
			 *
			 * @since 1.8
			 */
			$cf_json = VRCustomFields::translateObject($cf_json, $this->customFields);
			?>
			<tr class="row<?php echo ($i % 2); ?>">

				<td>
					<input type="checkbox" id="cb<?php echo (int) $i; ?>" name="cid[]" value="<?php echo (int) $row['id']; ?>" onClick="<?php echo $vik->checkboxOnClick(); ?>">
				</td>

				<!-- ORDER NUMBER -->
				
				<?php if (in_array('id', $listable_fields)): ?>
					<td class="hidden-phone text-nowrap">
						<div class="td-flex">
							<span class="hasTooltip" title="<?php echo $this->escape($oid_tooltip); ?>">
								<?php echo $row['id']; ?>
							</span>

							<?php
							// hide order status folder in case of CLOSURE
							if (!$row['closure']): ?>
								<a href="index.php?option=com_vikrestaurants&amp;view=rescodesorder&amp;id_order=<?php echo (int) $row['id']; ?>&amp;group=1" style="margin-left: 4px;">
									<i class="fa<?php echo (!$row['order_status_count'] ? 'r' : 's'); ?> fa-folder big" id="vrordfoldicon<?php echo (int) $row['id']; ?>"></i>
								</a>
							<?php endif; ?>
						</div>
					</td>
				<?php endif; ?>

				<!-- ORDER KEY -->

				<?php if (in_array('sid', $listable_fields)): ?>
					<td class="hidden-phone text-nowrap">
						<div>
							<?php
							if ($canEdit)
							{
								?>
								<a href="index.php?option=com_vikrestaurants&amp;task=reservation.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
									<?php echo $row['sid']; ?>
								</a>
								<?php
							}
							else
							{
								echo $row['sid'];
							}
							?>
						</div>

						<div class="td-secondary td-flex">
							<?php
							if ($row['created_on'] > 0)
							{
								echo JHtml::fetch('date', $row['created_on'], JText::translate('DATE_FORMAT_LC3') . ' ' . $config->get('timeformat'), date_default_timezone_get());
							}

							// display order status link in case the ID column is turned off
							if (!in_array('id', $listable_fields))
							{
								?>
								<a href="index.php?option=com_vikrestaurants&amp;view=rescodesorder&amp;id_order=<?php echo (int) $row['id']; ?>&amp;group=1" style="margin-left: 4px;">
									<i class="fa<?php echo (!$row['order_status_count'] ? 'r' : 's'); ?> fa-folder medium-big" id="vrordfoldicon<?php echo (int) $row['id']; ?>"></i>
								</a>
								<?php
							}
							?>
						</div>
					</td>
				<?php endif; ?>

				<!-- CHECK-IN -->

				<?php
				if (in_array('checkin_ts', $listable_fields)) 
				{
					// use check-in date as primary field by default
					$primary   = JHtml::fetch('date', $row['checkin_ts'], JText::translate('DATE_FORMAT_LC3'), date_default_timezone_get());
					$secondary = date($config->get('timeformat'), $row['checkin_ts']);

					if (!empty($filters['date']))
					{
						// switch time and date because it doesn't make sense having
						// a primary column repeated for all the rows
						$tmp       = $primary;
						$primary   = $secondary;
						$secondary = $tmp;	
					}
					?>
					<td>
						<div class="td-primary text-nowrap">
							<?php
							// make checkin date clickable to access the details of the reservation
							// in case the "Order Key" column is turned off
							if ($canEdit && !in_array('sid', $listable_fields))
							{
								?>
								<a href="index.php?option=com_vikrestaurants&amp;task=reservation.edit&amp;cid[]=<?php echo (int) $row['id']; ?>">
									<?php echo $primary; ?>
								</a>
								<?php
							}
							else
							{
								echo $primary;
							}
							?>
						</div>

						<div class="td-secondary td-flex">
							<span class="checkin-time">
								<?php echo $secondary; ?>
							</span>

							<?php
							// hide people in case of CLOSURE
							if (in_array('people', $listable_fields) && !$row['closure']): ?>
								<span class="checkin-people">
									<?php
									echo $row['people'] . ' ';

									for ($p = 1; $p <= min(array(2, $row['people'])); $p++)
									{
										?><i class="fas fa-male"></i><?php
									}
									?>
								</span>
							<?php endif; ?>
						</div>
					</td>
					<?php
				}
				?>

				<!-- CUSTOMER -->

				<?php if (in_array('customer', $listable_fields)): ?>
					<td>
						<?php
						// use primary for mail in case the nominative is empty
						$mail_class = 'td-primary';

						if ($row['purchaser_nominative'])
						{
							// nominative not empty, use secondary class for mail
							$mail_class = 'td-secondary';
							?>
							<div class="td-primary">
								<?php
								if ($row['id_user'] > 0)
								{
									?>
									<a href="javascript:void(0)" onclick="openCustomerModal(<?php echo (int) $row['id_user']; ?>);">
										<?php echo $row['purchaser_nominative']; ?>
									</a>
									<?php
								}
								else
								{
									echo $row['purchaser_nominative'];
								}
								?>
							</div>
							<?php
						}
						?>

						<?php if (in_array('mail', $listable_fields)): ?>
							<div class="<?php echo $this->escape($mail_class); ?>">
								<?php echo $row['purchaser_mail']; ?>
							</div>
						<?php endif; ?>

						<div class="mobile-only">
							<?php
							if (!$row['closure'])
							{
								echo JHtml::fetch('vrehtml.status.display', $row['status'], 'badge');
							}
							else
							{
								?>
								<span class="badge badge-important">
									<?php echo JText::translate('VRRESERVATIONSTATUSCLOSURE'); ?>
								</span>
								<?php
							}
							?>
						</div>
					</td>
				<?php endif; ?>

				<!-- PHONE -->

				<?php if (in_array('phone', $listable_fields)): ?>
					<td style="text-align: center;" class="hidden-phone">
						<?php echo $row['purchaser_phone']; ?>
					</td>
				<?php endif; ?>

				<!-- CUSTOM FIELDS -->

				<?php
				/**
				 * Iterate all the custom fields that should be shown within the body of the table.
				 *
				 * @since 1.7.4
				 */
				foreach ($this->customFields as $field)
				{
					if (in_array($field->id, $listable_cf))
					{
						?>
						<td style="text-align: center;" class="hidden-phone">
							<?php
							/**
							 * Translate field name in order to support
							 * those fields that still use the old
							 * translation method. 
							 *
							 * @since 1.8
							 */
							if (isset($cf_json[$field->name]))
							{
								echo $cf_json[$field->name];
							}
							?>
						</td>
						<?php
					}
				}
				?>

				<!-- CUSTOM -->

				<?php
				/**
				 * Display here the custom columns fetched by third-party plugins.
				 *
				 * @since 1.9
				 */
				foreach ($columns as $k => $col): ?>
					<td data-id="<?php echo $this->escape($k); ?>" class="hidden-phone">
						<?php echo isset($col->td[$i]) ? $col->td[$i] : ''; ?>
					</td>
				<?php endforeach; ?>

				<!-- INFO -->

				<?php if (in_array('info', $listable_fields)): ?>
					<td style="text-align: center;">
						<?php
						// hide info link in case of CLOSURE
						if (!$row['closure']): ?>
							<a href="javascript:void(0)" onclick="openOrderInfoModal(<?php echo (int) $row['id']; ?>);">
								<i class="fas fa-tag big-2x fa-flip-horizontal"></i>
							</a>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<!-- BILL -->

				<?php if (in_array('billval', $listable_fields)): ?>
					<td class="hidden-phone text-nowrap">
						<?php
						// hide bill in case of closure
						if (!$row['closure']): ?>
							<div class="td-flex">
								<div class="td-primary">
									<?php echo $currency->format($row['bill_value']); ?>
								</div>

								<?php echo JHtml::fetch('vrehtml.admin.stateaction', $row['bill_closed'], $row['id'], 'reservation.changebill', $canEditState); ?>
							</div>

							<?php if (!JHtml::fetch('vrehtml.status.ispaid', 'restaurant', $row['status']) && $row['bill_value'] > 0): ?>
								<div class="td-secondary">
									<?php
									if ($row['tot_paid'] == 0 && $row['deposit'] && JHtml::fetch('vrehtml.status.isconfirmed', 'restaurant', $row['status']))
									{
										// deposit probably not paid online
										$totalPaid = $row['deposit'];
									}
									else
									{
										// take total paid
										$totalPaid = $row['tot_paid'];
									}

									if ($row['deposit'] > $row['tot_paid'] && JHtml::fetch('vrehtml.status.isconfirmed', 'restaurant', $row['status']))
									{
										// inform the administrator that the deposit (or a part of it) haven't been paid through VikRestaurants
										?>
										<i class="fas fa-exclamation-triangle warn hasTooltip" title="<?php echo $this->escape(JText::translate('VRORDERDEPNOTPAID')); ?>"></i>
										<?php
									}

									// substract total paid from bill value
									echo JText::sprintf('VRORDERDUE', $currency->format(max(0, $row['bill_value'] - $totalPaid)));
									?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				<?php endif; ?>

				<!-- PAYMENT -->

				<?php if (in_array('payment', $listable_fields)): ?>
					<td class="hidden-phone">
						<?php echo !empty($row['payment_name']) ? $row['payment_name'] : '/'; ?>
					</td>
				<?php endif; ?>

				<!-- COUPON -->

				<?php if (in_array('coupon', $listable_fields)): ?>
					<td style="text-align: center;" class="hidden-phone">
						<?php
						if ($row['coupon_str'])
						{
							list($coupon_code, $coupon_amount, $coupon_percentot) = explode(';;', $row['coupon_str']);
							?>
							<span class="badge badge-important hasTooltip" title="<?php echo $this->escape($coupon_code); ?>">
								<?php
								if ($coupon_percentot == 1)
								{
									// percentage amount
									echo $currency->format($coupon_amount, [
										'symbol'     => '%',
										'position'   => 1,
										'space'      => false,
										'no_decimal' => true,
									]);
								}
								else
								{
									// fixed amount
									echo $currency->format($coupon_amount);
								}
								?>
							</span>
							<?php
						}
						?>
					</td>
				<?php endif; ?>

				<!-- ROOM -->

				<?php if (in_array('rname', $listable_fields)): ?>
					<td style="text-align: center;" class="hidden-phone">
						<span class="badge badge-warning"><?php echo $row['room_name']; ?></span>
					</td>
				<?php endif; ?>

				<!-- TABLE -->

				<?php if (in_array('tname', $listable_fields)): ?>
					<td style="text-align: center;" class="hidden-phone">
						<?php foreach ($row['cluster'] as $tname): ?>
							<span class="badge badge-info badge-table"><?php echo $tname; ?></span>
						<?php endforeach; ?>
					</td>
				<?php endif; ?>

				<!-- RESERVATION CODE -->

				<?php if (in_array('rescode', $listable_fields)): ?>
					<td style="text-align: center;" class="hidden-phone">
						<?php
						// hide reservation code in case of CLOSURE
						if (!$row['closure'])
						{
							?>
							<a href="javascript:void(0)" data-id="<?php echo (int) $row['id']; ?>" data-code="<?php echo (int) $row['rescode']; ?>" class="vrrescodelink" id="vrrescodelink<?php echo (int) $row['id']; ?>">
								<?php
								if (empty($row['code_icon']))
								{
									echo !empty($row['code']) ? $row['code'] : '--';
								}
								else
								{
									?>
									<img src="<?php echo VREMEDIA_SMALL_URI . $row['code_icon']; ?>" title="<?php echo $this->escape($row['code']); ?>" />
									<?php
								}
								?>
							</a>

							<?php
							echo JHtml::fetch('vrehtml.statuscodes.popup', 1, '#vrrescodelink' . $row['id']);
						}
						?>
					</td>
				<?php endif; ?>

				<!-- STATUS -->

				<?php if (in_array('status', $listable_fields)): ?>
					<td class="hidden-phone">
						<div class="td-flex">
							<?php if ($row['closure'] == 0): ?>
								<span class="status-hndl" style="cursor: pointer;" data-id="<?php echo (int) $row['id']; ?>" data-status="<?php echo $this->escape($row['status']); ?>">
									<?php echo JHtml::fetch('vrehtml.status.display', $row['status']); ?>
								</span>
							<?php else: ?>
								<span class="vrreservationstatusclosure">
									<?php echo JText::translate('VRRESERVATIONSTATUSCLOSURE'); ?>
								</span>
							<?php endif; ?>

							<a href="javascript:void(0)" class="pull-right" onclick="openOrderHistoryModal(<?php echo (int) $row['id']; ?>);" style="margin-left: 4px;">
								<i class="fas fa-history medium-big"></i>
							</a>
						</div>
					</td>
				<?php endif; ?>
			
			</tr>
			<?php
		}		
		?>
	</table>
	<?php
}
?>
	
	<!-- invoice submit fields -->
	<input type="hidden" name="notifycust" value="0" />

	<!-- print orders submit fields -->
	<input type="hidden" name="printorders[header]" value="" />
	<input type="hidden" name="printorders[footer]" value="" />
	<input type="hidden" name="printorders[update]" value="0" />

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="reservations" />

	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->ordering); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDir); ?>" />

	<?php echo JHtml::fetch('form.token'); ?>
	<?php echo $this->navbut; ?>
</form>

<?php
// order details modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-respinfo',
	array(
		'title'       => JText::translate('VRMANAGERESERVATION7'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);

// customer details modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-custinfo',
	array(
		'title'       => JText::translate('VRMANAGERESERVATION17'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);

// reservation history modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-history',
	array(
		'title'       => JText::translate('VRORDERSTATUSES'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);

// CC details modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-ccdetails',
	array(
		'title'       => JText::translate('VRSEECCDETAILS'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'url'		  => '', // it will be filled dinamically
	)
);
?>

<!-- INVOICE DIALOG -->
<div id="dialog-invoice" style="display: none;">
	<h3 style="margin-top: 0;"><?php echo JText::translate('VRINVOICEDIALOG'); ?></h3>

	<p><?php echo JText::translate('VRGENERATEINVOICESTXT'); ?></p>

	<div>
		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->name('notifycust_radio')
			->label(JText::translate('VRMANAGEINVOICE7'))
			->onchange('notifyCustValueChanged(this.checked)');
		?>
	</div>
</div>

<!-- PRINT ORDERS DIALOG -->
<div id="dialog-printorders" style="display: none;">
	<?php $printorders_text = VikRestaurants::getPrintOrdersText(); ?>

	<h3 style="margin-top: 0;"><?php echo JText::translate('VRPRINT'); ?></h3>

	<div>
		<?php
		echo $this->formFactory->createField()
			->type('textarea')
			->name('printorders_header')
			->value($printorders_text['header'])
			->label(JText::translate('VRPRINTORDERS1'))
			->class('full-width')
			->height(50)
			->style('resize: vertical; max-height: 120px;');
		?>
	
		<?php
		echo $this->formFactory->createField()
			->type('textarea')
			->name('printorders_footer')
			->value($printorders_text['footer'])
			->label(JText::translate('VRPRINTORDERS2'))
			->class('full-width')
			->height(50)
			->style('resize: vertical; max-height: 120px;');
		?>

		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->name('printorders_update')
			->label(JText::translate('VRPRINTORDERS3'))
			->onchange('updatePrintValueChanged(this.checked)');
		?>
	</div>
</div>

<?php
JText::script('VROK');
JText::script('VRCANCEL');
?>

<script>
	(function($, w) {
		'use strict';

		let invoiceDialog, printDialog;

		const openModal = (id, url, jqmodal) => {
			<?php echo $vik->bootOpenModalJS(); ?>
		}

		w.openCreditCardModal = (reservationId) => {
			let url = 'index.php?option=com_vikrestaurants&view=ccdetails&tmpl=component&tid=0&id=' + reservationId;
			openModal('ccdetails', url, true);
		}

		w.openCustomerModal = (customerId) => {
			let url = 'index.php?option=com_vikrestaurants&view=customerinfo&tmpl=component&locations=0&id=' + customerId;
			openModal('custinfo', url, true);
		}

		w.openOrderInfoModal = (reservationId) => {
			let url = 'index.php?option=com_vikrestaurants&view=orderinfo&tmpl=component&id=' + reservationId;
			openModal('respinfo', url, true);
		}

		w.openOrderHistoryModal = (reservationId) => {
			let url = 'index.php?option=com_vikrestaurants&view=orderhistory&tmpl=component&group=1&id=' + reservationId;
			openModal('history', url, true);
		}

		w.clearFilters = () => {
			$('#vrkeysearch').val('');
			$('#vrdatefilter').val('');
			$('#vr-status-sel').updateChosen('');
			$('#vr-room-sel').updateChosen(0);

			if ($('#vr-operator-sel').length) {
				$('#vr-operator-sel').updateChosen(0);
			}

			if ($('#vr-shift-sel').length) {
				$('#vr-shift-sel').updateChosen(0);
			}

			$('#adminForm').append('<input type="hidden" name="ids[]" value="0" />');
			
			document.adminForm.submit();
		}

		w.notifyCustValueChanged = (checked) => {
			$('#adminForm input[name="notifycust"]').val(checked ? 1 : 0);
		}

		w.updatePrintValueChanged = (checked) => {
			$('#adminForm input[name="printorders[update]"]').val(checked ? 1 : 0);
		}

		$(function() {
			VikRenderer.chosen('.btn-toolbar');

			// create invoice dialog
			invoiceDialog = new VikConfirmDialog('#dialog-invoice', 'vik-invoice-confirm');

			// add confirm button
			invoiceDialog.addButton(Joomla.JText._('VROK'), (task, event) => {
				// submit the form
				Joomla.submitform(task);
			});

			// add cancel button
			invoiceDialog.addButton(Joomla.JText._('VRCANCEL'));

			// create print dialog
			printDialog = new VikConfirmDialog('#dialog-printorders', 'vik-print-confirm');

			// add confirm button
			printDialog.addButton(Joomla.JText._('VROK'), (task, event) => {
				// set up print orders parameters
				$('#adminForm input[name="printorders[header]"]').val($('textarea[name="printorders_header"]').val());
				$('#adminForm input[name="printorders[footer]"]').val($('textarea[name="printorders_footer"]').val());

				// prepare form to be submitted on a blank browser tab
				$('#adminForm').attr('target', '_blank');
				// change form view
				$('#adminForm input[name="view"]').val('printorders');

				// submit form (DO NOT SET TASK)
				document.adminForm.submit();

				// restore "reservations" view
				$('#adminForm input[name="view"]').val('reservations');
				// restore form target attribute
				$('#adminForm').attr('target', '');
			});

			// add cancel button
			printDialog.addButton(Joomla.JText._('VRCANCEL'));

			Joomla.submitbutton = (task) => {
				if (task == 'invoice.generate') {
					// show invoice dialog
					invoiceDialog.show(task);
				} else if (task == 'printorders') {
					// show print orders dialog
					printDialog.show(task, {
						// disable submit by keyboard, otherwise adding a new line from the
						// textareas would lead to an automated submit of the form
						submit: false,
					});
				} else {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>