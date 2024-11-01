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

/**
 * Layout variables
 * -----------------
 * @var  array 				  $reservations  A list of tables to display.
 * @var  array 				  $waitinglist   A list of prepared dishes.
 * @var  array 				  $filters       An array of filters.
 * @var  VREStatisticsWidget  $widget        The instance of the widget to be displayed.
 */
extract($displayData);

$itemid = JFactory::getApplication()->input->get('Itemid', null, 'uint');

$vik = VREApplication::getInstance();

if (count($reservations) || count($waitinglist))
{
	?>
	<div class="vrdash-container kitchen" data-widget="<?php echo $widget->getID(); ?>">

		<div class="vr-kitchen-wall">

			<?php
			foreach ($reservations as $res)
			{
				?>
				<div class="kitchen-wall-block-wrap table-parent-wrapper">
					<div class="kitchen-wall-block">

						<div class="wall-block-head">

							<div class="block-head-text">
								<span class="block-head-table">
									<span class="badge badge-info"><?php echo $res->table->name; ?></span>

									<?php
									// show elapsed time only if higher than 4 minutes
									if ($res->elapsedTime > 4)
									{
										?>
										<span class="badge badge-important elapsed-time">
											<i class="fas fa-stopwatch"></i>
											<?php echo $res->elapsedTime; ?>'
										</span>
										<?php
									}
									?>
								</span>

								<span class="block-head-room">
									<?php
									/**
									 * Display the service in case of take-away group.
									 *
									 * @since 1.9.1
									 */
									if ($widget->isGroup('takeaway'))
									{
										?>
										<small class="badge badge-success">
											<?php echo $res->service; ?>
										</small>
										<?php
									}
									/**
									 * Display the operator name if assigned.
									 * Otherwise fallback to room name.
									 *
									 * @since 1.8.1
									 */
									else if ($res->operator)
									{
										?>
										<small class="badge badge-success">
											<?php echo $res->operator; ?>
										</small>
										<?php
									}
									else
									{
										echo $res->room->name;
									}
									?>
								</span>
							</div>

							<div class="block-head-actions">
								<?php if ($widget->isGroup('restaurant')): ?>
									<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&task=opreservation.editbill&cid[]=' . $res->id . '&bill_from=opkitchen' . ($itemid ? '&Itemid=' . $itemid : '')); ?>">
										<i class="fas fa-shopping-basket"></i>
									</a>
								<?php else: ?>
									<a href="javascript: void(0);" data-id="<?php echo $res->id; ?>" data-code="<?php echo (int) $res->rescode; ?>" class="vrrescodelink order">
										<?php
										if ($res->code)
										{
											if ($res->code->icon)
											{
												?>
												<img src="<?php echo $res->code->iconURI; ?>" title="<?php echo $this->escape($res->code->code); ?>" />
												<?php
											}
											else
											{
												?>
												<span title="<?php echo $this->escape($res->code->code); ?>">
													<?php echo strtoupper(substr($res->code->code, 0, 2)); ?>
												</span>
												<?php
											}
										}
										else
										{
											echo '--';
										}
										?>
									</a>
									<?php echo JHtml::fetch('vrehtml.statuscodes.popup', 2); ?>
								<?php endif; ?>
							</div>

						</div>

						<div class="wall-block-list">

							<?php
							if (count($res->dishes) == 0)
							{
								?>
								<div style="padding: 10px;">
									<div class="vr-kitchen-no-result" style="margin: 0;">
										<?php echo JText::translate('JGLOBAL_NO_MATCHING_RESULTS'); ?>
									</div>
								</div>
								<?php
							}
							else
							{
								$lastServingNumber = null;
								$useServingNumber = VREFactory::getConfig()->getBool('servingnumber');

								foreach ($res->dishes as $dish)
								{
									/**
									 * If supported, display the serving number.
									 * 
									 * @since 1.9.1
									 */
									if ($widget->isGroup('restaurant') && $useServingNumber)
									{
										if ($lastServingNumber === null || $lastServingNumber != $dish->servingnumber)
										{
											$lastServingNumber = $dish->servingnumber;

											?>
											<div class="dishes-serving-number-separator">
												<?php echo JText::translate('VRE_ORDERDISH_SERVING_NUMBER_' . $dish->servingnumber); ?>
											</div>
											<?php
										}
									}
									?>
									<div class="block-list-dish">

										<div class="list-dish-quantity">
											<?php echo $dish->quantity; ?>&nbsp;<small>x</small>
										</div>

										<div class="list-dish-main">
											<div class="list-dish-name"><?php echo $dish->name; ?></div>

											<?php
											if ($dish->notes)
											{
												?>
												<div class="list-dish-notes"><?php echo $dish->notes; ?></div>
												<?php
											}
											?>
										</div>

										<div class="list-dish-code">
											<a href="javascript: void(0);" data-id="<?php echo $dish->id; ?>" data-code="<?php echo (int) $dish->rescode; ?>" class="vrrescodelink food-item">
												<?php
												if ($dish->code)
												{
													if ($dish->code->icon)
													{
														?>
														<img src="<?php echo $dish->code->iconURI; ?>" title="<?php echo $this->escape($dish->code->code); ?>" />
														<?php
													}
													else
													{
														?>
														<span title="<?php echo $this->escape($dish->code->code); ?>">
															<?php echo strtoupper(substr($dish->code->code, 0, 2)); ?>
														</span>
														<?php
													}
												}
												else
												{
													echo '--';
												}
												?>
											</a>

											<?php
											echo JHtml::fetch('vrehtml.statuscodes.popup', 3);
											?>
										</div>

									</div>
									<?php
								}
							}
							?>

						</div>

					</div>
				</div>
				<?php
			}
			?>

		</div>

		<div class="vr-kitchen-waitlist">
			
			<div class="kitchen-waitlist-head">
				<?php echo JText::translate('VRE_STATS_WIDGET_KITCHEN_OUTGOING_COURSES'); ?>
			</div>

			<div class="kitchen-waitlist-groups">
				<?php
				foreach ($waitinglist as $res)
				{
					?>
					<div class="table-parent-wrapper">

						<div class="waitlist-group-title">
							<span class="waitlist-group-table">
								<span class="badge badge-info"><?php echo $res->table->name; ?></span>

								<?php
								// show elapsed time only if equals or higher than 1 minute
								if ($res->elapsedTime)
								{
									?>
									<span class="badge badge-important elapsed-time">
										<i class="fas fa-stopwatch"></i>
										<?php echo $res->elapsedTime; ?>'
									</span>
									<?php
								}
								?>
							</span>

							<span class="waitlist-group-room">
								<?php
								/**
								 * Display the service in case of take-away group.
								 *
								 * @since 1.9.1
								 */
								if ($widget->isGroup('takeaway'))
								{
									?>
									<small class="badge badge-success">
										<?php echo $res->service; ?>
									</small>
									<?php
								}
								/**
								 * Display the operator name if assigned.
								 * Otherwise fallback to room name.
								 *
								 * @since 1.8.1
								 */
								else if ($res->operator)
								{
									?>
									<small class="badge badge-success">
										<?php echo $res->operator; ?>
									</small>
									<?php
								}
								else
								{
									echo $res->room->name;
								}
								?>
							</span>
						</div>

						<div class="waitlist-group-courses">
							<?php
							foreach ($res->dishes as $dish)
							{
								?>
								<div class="waitlist-group-dish">
									<div class="waitlist-group-dish-quantity">
										<?php echo $dish->quantity; ?>&nbsp;<small>x</small>
									</div>

									<div class="waitlist-group-dish-name"><?php echo $dish->name; ?></div>

									<div class="waitlist-group-dish-code">
										<a href="javascript: void(0);" data-id="<?php echo $dish->id; ?>" data-code="<?php echo (int) $dish->rescode; ?>" class="vrrescodelink food-item">
											<?php
											if ($dish->code)
											{
												if ($dish->code->icon)
												{
													?>
													<img src="<?php echo $dish->code->iconURI; ?>" title="<?php echo $this->escape($dish->code->code); ?>" />
													<?php
												}
												else
												{
													?>
													<span title="<?php echo $this->escape($dish->code->code); ?>">
														<?php echo strtoupper(substr($dish->code->code, 0, 2)); ?>
													</span>
													<?php
												}
											}
											else
											{
												echo '--';
											}
											?>
										</a>

										<?php
										echo JHtml::fetch('vrehtml.statuscodes.popup', 3);
										?>
									</div>
								</div>
								<?php
							}
							?>
						</div>

					</div>
					<?php
				}
				?>
			</div>

		</div>

	</div>

	<script>
		jQuery('.vrdash-container.kitchen[data-widget="<?php echo $widget->getID(); ?>"]')
			.find('.vrrescodelink.food-item').each(function() {
				jQuery(this).statusCodesPopup({
					group: 3,
					controller: '<?php echo $vik->ajaxUrl('index.php?option=com_vikrestaurants&task=opkitchen.changecodeajax&section=' . ($widget->isGroup('takeaway') ? 'takeaway' : 'restaurant')); ?>',
					onShow: function(event) {
						// pause dashboard timer as long as a popup is open
						stopDashboardListener();
					},
					onHide: function(event) {
						// restart dashboard timer after closing the popup
						startDashboardListener();
					},
					onChange: function(resp, root) {
						// delete the badge containing the elapsed time
						// every time the status codes changes
						jQuery(root).closest('.table-parent-wrapper').find('.elapsed-time').remove();
					},
				});
			});

		jQuery('.vrdash-container.kitchen[data-widget="<?php echo $widget->getID(); ?>"]')
			.find('.vrrescodelink.order').each(function() {
				jQuery(this).statusCodesPopup({
					group: 2,
					controller: '<?php echo $vik->ajaxUrl('index.php?option=com_vikrestaurants&task=oversight.changecodeajax&group=2'); ?>',
					onShow: function(event) {
						// pause dashboard timer as long as a popup is open
						stopDashboardListener();
					},
					onHide: function(event) {
						// restart dashboard timer after closing the popup
						startDashboardListener();
					},
				});
			});
	</script>
	<?php
}
else
{
	?>
	<div class="vr-kitchen-no-result">
		<?php echo JText::translate('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php
}
