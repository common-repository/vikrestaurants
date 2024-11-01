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
 * Template file used to display the head part of
 * the take-away menus page, which contains the
 * front-end notes, the menus filter and the
 * input to change the check-in date.
 *
 * @since 1.8
 */

$config = VREFactory::getConfig();

// check whether the date selection is allowed
$is_date_allowed = $config->getBool('tkallowdate');

/**
 * Translate take-away notes according to the
 * current selected language.
 *
 * @since 1.8
 */
$notes = VikRestaurants::translateSetting('tknote');

if ($notes)
{
	// show take-away front notes (see configuration)
	?>
	<div class="vrtkstartnotediv">
		<?php echo $notes; ?>
	</div>
	<?php
}
?>

<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeaway' . ($this->filters['menu'] ? '&takeaway_menu=' . $this->filters['menu'] : '') . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" name="vrmenuform" id="vrmenuform">

	<div class="vrtk-menus-filter-head">
		<?php
		// get all selectable menus
		$menus = JHtml::fetch('vikrestaurants.takeawaymenus');

		// display dropdown only in case of 2 or more menus
		if (count($menus) > 1)
		{
			?>
			<div class="vrtkselectmenudiv vre-select-wrapper">
				<select name="takeaway_menu" id="vrtkselectmenu" class="vre-select">
					<option value="0"><?php echo JText::translate('VRTAKEAWAYALLMENUS'); ?></option>
					<?php echo JHtml::fetch('select.options', $menus, 'value', 'text', $this->filters['menu']); ?>
				</select>
			</div>
			<?php
		}
		?>

		<div class="vrtk-filter-secondary">
			<div class="vrtk-menus-date-block vre-calendar-wrapper">
				<?php 
				$checkin = date($config->get('dateformat'), $this->cart->getCheckinTimestamp());
				$today   = date($config->get('dateformat'));

				if ($checkin == $today)
				{
					$dt_value = JText::translate('VRJQCALTODAY');
				}
				else
				{
					$dt_value = $checkin;
				}

				if ($is_date_allowed)
				{
					// add support for datepicker events
					JHtml::fetch('vrehtml.sitescripts.datepicker', '#vrtk-menus-filter-date:input', 'takeaway');

					?>
					<input type="hidden" name="takeaway_date" value="<?php echo $this->escape($checkin); ?>" />
					<?php
				}
				?>

				<input type="text" class="vrtk-menus-filter-date<?php echo ($is_date_allowed ? ' enabled' : ''); ?> vre-calendar" id="vrtk-menus-filter-date" value="<?php echo $this->escape($dt_value); ?>" size="12" readonly="readonly" />
			</div>
			
			<?php if ($config->getBool('tkshowtimes') && $this->times): ?>
				<div class="vrtk-menus-date-block vre-select-wrapper">
					<?php
					$attrs = [
						'id'    => 'vrtk-menus-filter-time',
						'class' => 'vre-select',
					];

					// display times dropdown
					echo JHtml::fetch('vrehtml.site.timeselect', 'takeaway_time', $this->cart->getCheckinTime(), $this->times, $attrs);
					?>
				</div>
			<?php endif; ?>
		</div>

	</div>

	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="view" value="takeaway" />

</form>

<script>
	(function($) {
		'use strict';

		$(function() {
			let MENUS_ROUTE_LOOKUP = {};

			<?php
			$options = array_merge(
				[JHtml::fetch('select.option', 0, '')],
				$menus
			);

			foreach ($options as $menu)
			{
				// fetch URL to access menu details
				$url = 'index.php?option=com_vikrestaurants&view=takeaway' . ($menu->value ? '&takeaway_menu=' . $menu->value : '') . ($this->itemid ? '&Itemid=' . $this->itemid : '');
				?>
				MENUS_ROUTE_LOOKUP['<?php echo $menu->value; ?>'] = '<?php echo JRoute::rewrite($url, false); ?>';
				<?php
			}
			?>

			$('#vrtkselectmenu').on('change', function() {
				let id = $(this).val();

				if (MENUS_ROUTE_LOOKUP.hasOwnProperty(id)) {
					// change form URL for a better SEO
					$(this).closest('form').attr('action', MENUS_ROUTE_LOOKUP[id]);
				}

				// submit form
				document.vrmenuform.submit();
			});

			$('#vrtk-menus-filter-time').on('change', () => {
				document.vrmenuform.submit();
			});

			<?php if ($is_date_allowed): ?>
				$('#vrtk-menus-filter-date:input').on('change', function() {
					$('input[name="takeaway_date"]').val($(this).val());
					document.vrmenuform.submit();
				});
			<?php endif; ?>
		});
	})(jQuery);
</script>