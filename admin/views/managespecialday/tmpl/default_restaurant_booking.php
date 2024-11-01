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

$specialday = $this->specialday;

?>

<!-- PEOPLE ALLOWED - Select -->

<?php
$peopleAllowedField = $this->formFactory->createField()
	->type('number')
	->name('peopleallowed')
	->value($specialday->peopleallowed)
	->min(0)
	->step(1)
	->hiddenLabel(true)
	->control([
		'class' => 'vr-peopleallowed-child',
		'style' => $specialday->peopleallowed == -1 ? 'display: none;' : '',
	]);

echo $this->formFactory->createField()
	->type('select')
	->name('peopallradio')
	->value($specialday->peopleallowed != -1 ? 2 : 1)
	->label(JText::translate('VRMANAGESPDAY21'))
	->description(JText::translate('VRMANAGESPDAY21_HELP'))
	->options([
		JHtml::fetch('select.option', 1, JText::translate('VRPEOPLEALLOPT1')),
		JHtml::fetch('select.option', 2, JText::translate('VRPEOPLEALLOPT2')),
	])->render(function($data, $input) use ($peopleAllowedField) {
		?>
		<div class="multi-field width-50">
			<?php
			// display the select first
			echo $input;

			// then display the deposit threshold (people)
			echo $peopleAllowedField->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(
				strtolower(JText::translate('VRORDERPEOPLE'))
			));
			?>
		</div>
		<?php
	});
?>

<!-- CHOOSABLE MENUS - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('choosemenu')
	->checked($specialday->choosemenu)
	->label(JText::translate('VRMANAGESPDAY19'))
	->description(JText::translate('VRMANAGESPDAY19_HELP'))
	->onchange('chooseMenuValueChanged(this.checked)')
?>

<!-- FREEDOM OF CHOICE - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('freechoose')
	->checked($specialday->freechoose)
	->label(JText::translate('VRMANAGESPDAY23'))
	->description(JText::translate('VRMANAGESPDAY23_DESC'))
	->control([
		'class' => 'vr-choosemenu-child',
		'style' => $specialday->choosemenu ? '' : 'display: none;',
	])
?>

<!-- MENUS - Select -->

<?php
echo $this->formFactory->createField()
	->type('groupedlist')
	->name($specialday->group == 1 ? 'id_menu' : '')
	->value($specialday->group == 1 ? $specialday->menus : [])
	->id('vr-restaurant-menus')
	->multiple(true)
	->label(JText::translate('VRMANAGESPDAY9'))
	->description(JText::translate('VRMANAGESPDAY9_HELP') . ' ' . JText::translate('VRMANAGESPDAY9_HELP_RS'))
	->options(JHtml::fetch('vrehtml.admin.menus', $blank = false, $group = true));
?>

<?php
JText::script('VRFILTERSELECTMENU');
?>

<script>
	(function($, w) {
		'use strict';

		w.chooseMenuValueChanged = (checked) => {
			if (checked) {
				$('.vr-choosemenu-child').show();
			} else {
				$('.vr-choosemenu-child').hide();
			}
		}

		$(function() {
			$('#vr-restaurant-menus').select2({
				placeholder: Joomla.JText._('VRFILTERSELECTMENU'),
				allowClear: true,
				width: 400,
			});

			$('select[name="peopallradio"]').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			$('select[name="peopallradio"]').on('change', function() {
				if ($(this).val() == 1) {
					$('.vr-peopleallowed-child').hide().find('input').val(-1);
				} else {
					$('.vr-peopleallowed-child').show().find('input').val(100);
				}
			});
		});
	})(jQuery, window);
</script>