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

$menuLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-cards-container cards-reservation-menus" id="cards-reservation-menus">

	<?php
	foreach ($this->menus as $menu)
	{
		$units = isset($this->reservation->menus[$menu->id]) ? $this->reservation->menus[$menu->id]->quantity : 0;

		?>
		<div class="vre-card-fieldset up-to-3" id="reservation-menu-fieldset-<?php echo (int) $menu->id; ?>">

			<?php
			$displayData = [];

			// fetch card ID
			$displayData['id'] = 'reservation-menu-card-' . $menu->id;

			if (empty($menu->image) || !is_file(VREMEDIA . DIRECTORY_SEPARATOR . $menu->image))
			{
				// use default menu image
				$displayData['image'] = VREMEDIA_URI . 'menu_default_icon.jpg';
			}
			else
			{
				// use menu image URI
				$displayData['image'] = VREMEDIA_URI . $menu->image;
			}

			// fetch primary text
			$displayData['primary'] = $menu->name;

			// fetch secondary text
			$displayData['secondary'] = '<span class="menu-controls">'
					. '<a href="javascript:void(0)" class="remove-menu" data-unit="-1">'
						. '<i class="fas fa-minus-circle no"></i>'
					. '</a>'
					. '&nbsp;<span>' . $units . '</span>&nbsp;'
					. '<a href="javascript:void(0)" class="add-menu" data-unit="1">'
						. '<i class="fas fa-plus-circle ok"></i>'
					. '</a>'
					. '<input type="hidden" name="menus[' . $menu->id . ']" data-id="' . $menu->id . '" value="' . $units . '" />'
				. '</span>';

			// render layout
			echo $menuLayout->render($displayData);
			?>
		</div>
		<?php
	}
	?>

</div>

<div style="display:none;" id="reservation-menu-struct">
	
	<?php
	// create reservation menu structure for new items
	$displayData = [];
	$displayData['id']        = 'reservation-menu-card-{id}';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';

	echo $menuLayout->render($displayData);
	?>

</div>

<?php
JText::script('VRMANAGERESERVATION_SELECTED_MENUS_WARN');
?>

<script>
	(function($, w) {
		'use strict';

		const addReservationMenuCard = (menu, units) => {
			const ID = 'reservation-menu-fieldset-' + menu.id;

			let html = $('#reservation-menu-struct').clone().html();
			html = html.replace(/{id}/, ID);

			$('#cards-reservation-menus').append('<div class="vre-card-fieldset up-to-3" id="' + ID + '">' + html + '</div>');

			// get created fieldset
			const fieldset = $('#' + ID);

			if (!menu.image) {
				menu.image = 'menu_default_icon.jpg';
			}

			// update image
			fieldset.vrecard('image', '<?php echo VREMEDIA_URI; ?>' + (menu.image || 'menu_default_icon.jpg'));

			// update primary text
			fieldset.vrecard('primary', menu.name);

			const controls = $('<span class="menu-controls"></span>');

			controls.append('<a href="javascript:void(0)" class="remove-menu" data-unit="-1"><i class="fas fa-minus-circle no"></i></a>');
			controls.append('&nbsp;<span>' + units + '</span>&nbsp;');
			controls.append('<a href="javascript:void(0)" class="add-menu" data-unit="1"><i class="fas fa-plus-circle ok"></i></a>');
			controls.append('<input type="hidden" name="menus[' + menu.id + ']" data-id="' + menu.id + '" value="' + units + '" />');

			fieldset.vrecard('secondary', controls);

			return fieldset;
		}

		w.setupAvailableMenus = (menus) => {
			// obtain all the currently selected menus
			let selected = {};

			$('.menu-controls input').each(function() {
				const menuId = parseInt($(this).data('id'));
				selected[menuId] = parseInt($(this).val());
			});

			// reset menus
			$('#cards-reservation-menus').html('');

			menus.forEach((menu) => {
				// register menu one by one
				addReservationMenuCard(menu, selected.hasOwnProperty(menu.id) ? selected[menu.id] : 0);
			});
		}

		$(function() {
			$(document).on('click', '.remove-menu, .add-menu', function() {
				const input = $(this).parent().find('input');
				let units = parseInt(input.val()) + parseInt($(this).data('unit'));

				if (units < 0) {
					return false;
				}

				input.val(units);
				$(this).parent().find('span').text(units);
			});

			onInstanceReady(() => {
				return w.reservationValidator;
			}).then((validator) => {
				validator.addCallback(() => {
					if ($('#cards-reservation-menus').children().length == 0) {
						// no menus available for the selected check-in
						return true;
					}

					let units = 0;

					// count the number of selected menus
					$('#cards-reservation-menus input[name^="menus["]').each(function() {
						units += parseInt($(this).val());
					});

					// obtain the number of participants
					const people = parseInt($('#vr-people-sel').val());

					if (units != people) {
						// invalid selection
						let warn = Joomla.JText._('VRMANAGERESERVATION_SELECTED_MENUS_WARN')
							.replace(/%d/, units)
							.replace(/%d/, people);

						if (!confirm(warn)) {
							// the user refused the submit
							return false;
						}
					}

					return true;
				});
			});
		});
	})(jQuery, window);
</script>