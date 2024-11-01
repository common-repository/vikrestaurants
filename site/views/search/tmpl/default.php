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

JHtml::fetch('vrehtml.assets.fontawesome');

$config = VREFactory::getConfig();

if ($this->step == 0 && $config->getUint('reservationreq') == 2)
{
	// automatically increase step in case of none selection
	$this->step = 1;
}

if ($this->step == 0)
{
	// animate to search form only for the first step, otherwise
	// the system could face a conflict with a different animation
	JHtml::fetch('vrehtml.sitescripts.animate');
}

// display step bar using the view sub-template
echo $this->loadTemplate('stepbar');
?>

<div class="vrreservationform" id="vrresultform">

	<!-- display reservation summary -->

	<?php
	// display summary using the view sub-template
	echo $this->loadTemplate('summary');
	?>

	<!-- display hints in case of failure -->

	<?php
	if ($this->attempt == 3)
	{	
		// display hints using the sub-template
		echo $this->loadTemplate('hints');
	}
	else
	{
		?>
		<div id="vrbookingborderdiv" class="vrbookingouterdiv">
			<?php
			if ($this->step == 0)
			{
				?>
				<div class="vrresultbookdiv vrsuccess" id="vrsearchsuccessdiv">
					<?php
					echo JText::sprintf('VRSUCCESSMESSSEARCH', $this->args['people']);

					switch ($config->getUint('reservationreq'))
					{
						case 0:
							// choose table and room
							echo ' ' . JText::translate('VRMESSNOWCHOOSETABLE');
							break;

						case 1: 
							// choose room only
							echo ' ' . JText::translate('VRMESSNOWCHOOSEROOM');
							break;
					}
					?>
				</div>

				<div class="vrbookcontinuebuttoncont">
					<button type="button" class="vre-btn primary" id="vre-search-showtables-btn" onClick="showRoomTable(this);">
						<?php echo JText::translate('VRCONTINUEBUTTON' . $config->getUint('reservationreq')); ?>
					</button>
				</div>
				<?php
			}
			?>

			<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=search' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>"  id="vrresform" name="vrresform" method="post">

				<div id="table-booking-wrapper" style="<?php echo $this->step == 0 ? 'display:none;' : ''; ?>">
					<?php
					if ($config->getUint('reservationreq') != 2)
					{
						// display room details in case it is possible to 
						// choose the table and/or the room
						echo $this->loadTemplate('room');
					}

					if ($config->getUint('reservationreq') == 0)
					{
						// display tables map in case it is possible to
						// choose the table
						echo $this->loadTemplate('tables');
					}

					if (count($this->menus))
					{
						// display menus box in case the customers can
						// choose the menus for their reservations
						?>
						<div id="menu-selection-wrapper" style="display:none;">
							<?php echo $this->loadTemplate('menus'); ?>
						</div>
						<?php
					}
					?>

					<div class="vrbookcontinuebuttoncont">
						<button type="button" class="vre-btn primary big" onClick="continueBooking();" id="vre-search-continue-btn">
							<?php echo JText::translate('VRCONTINUE'); ?>
						</button>
					</div>
				</div>

				<input type="hidden" name="date" value="<?php echo $this->args['date']; ?>" />
				<input type="hidden" name="hourmin" value="<?php echo $this->args['hourmin']; ?>" />
				<input type="hidden" name="people" value="<?php echo $this->args['people']; ?>" />
				<input type="hidden" name="family" value="<?php echo JFactory::getApplication()->getUserState('vre.search.family', 0); ?>" />

				<input type="hidden" name="option" value="com_vikrestaurants" />
				<input type="hidden" name="view" value="search" />

			</form>
		</div>
		<?php
	}
	?>

</div>

<?php if ($this->attempt != 3): ?>
	<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=confirmres' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>"  id="vrconfirmform" name="vrconfirmform" method="post">
					
		<input type="hidden" name="date" value="<?php echo $this->escape($this->args['date']); ?>" />
		<input type="hidden" name="hourmin" value="<?php echo $this->escape($this->args['hourmin']); ?>" />
		<input type="hidden" name="people" value="<?php echo $this->escape($this->args['people']); ?>" />
		<input type="hidden" name="table" value="" />
			
		<input type="hidden" name="option" value="com_vikrestaurants" />
		<input type="hidden" name="view" value="confirmres" />
		
	</form>
<?php endif; ?>

<?php
JText::script('VRERRCHOOSETABLEFIRST');
?>

<script>
	(function($, w) {
		'use strict';

		let BOOKING_STEP = <?php echo (int) $this->step; ?>;

		w.SELECTED_TABLE = null;

		$(function() {
			<?php if ($this->step == 1): ?>
				// auto-scroll page to room/table selection
				$('html,body').animate({
					scrollTop: $('#table-booking-wrapper').offset().top - 20,
				}, {
					duration: 'slow',
				});
			<?php endif; ?>
		});
		
		w.showRoomTable = (button) => {
			// hide button
			$(button).parent().hide();
			// show table booking box
			$('#table-booking-wrapper').show();

			$('html,body').animate({
				scrollTop: $('#table-booking-wrapper').offset().top - 20,
			}, {
				duration: 'slow',
			});

			BOOKING_STEP++;
		}

		w.continueBooking = () => {
			<?php if ($config->getUint('reservationreq') !== 2): ?>
				// validate table selection only in case the user is allowed to choose at least
				// one among the table and the room
				if (!w.SELECTED_TABLE) {
					// table selection is mandatory
					$('#vrbooknoselsp').text(Joomla.JText._('VRERRCHOOSETABLEFIRST'));
					$('#vrbooknoselsp').fadeIn('normal').delay(2000).fadeOut('normal');
					return false;
				}
			<?php endif; ?>

			// get confirmation form
			const form = $('form#vrconfirmform');

			// update table input
			form.find('input[name="table"]').val(w.SELECTED_TABLE);

			// check if the customers are allowed to select a menu
			let isMenuSelection = <?php echo $this->menus ? 1 : 0; ?>;

			if (BOOKING_STEP == 1 && isMenuSelection) {
				// increase booking step
				BOOKING_STEP++;

				// show menus selection box
				$('#menu-selection-wrapper').show();

				// scroll down to menus list
				$('html,body').animate({
					scrollTop: $('#menu-selection-wrapper').offset().top - 20,
				}, {
					duration: 'slow',
				});

				// do not go ahead
				return false;
			}

			if (BOOKING_STEP == 2 && !validateMenus()) {
				// missing menus selection
				$('#vrbookmenuselsp')
					.addClass('vrbookmenunopeople')
						.delay(2000)
							.queue(function(next) {
								$(this).removeClass('vrbookmenunopeople');
								next();
							});

				return false;
			}

			// submit form to confirmation page
			form.submit();
		}
	})(jQuery, window);
</script>