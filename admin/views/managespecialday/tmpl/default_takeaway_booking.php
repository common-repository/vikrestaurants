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

<!-- MINIMUM COST PER ORDER - Select -->

<?php
$minTotalCostField = $this->formFactory->createField()
	->type('number')
	->name('minorder')
	->value($specialday->minorder)
	->min((float) $specialday->minorder ? 1 : 0)
	->step('any')
	->hiddenLabel(true)
	->control([
		'class' => 'ask-minorder-child',
		'style' => $specialday->minorder == 0 ? 'display: none;' : '',
	]);

echo $this->formFactory->createField()
	->type('select')
	->id('askminorder')
	->value((float) $specialday->minorder ? 1 : 0)
	->label(JText::translate('VRMANAGECONFIGTK5'))
	->description(JText::translate('VRMANAGECONFIGTK5_OVERRIDE_HELP'))
	->options([
		JHtml::fetch('select.option', 0, JText::translate('VRSPDAYSERVICEOPT1')),
		JHtml::fetch('select.option', 1, JText::translate('VRPEOPLEALLOPT2')),
	])->render(function($data, $input) use ($minTotalCostField) {
		?>
		<div class="multi-field width-50">
			<?php
			echo $input;

			echo $minTotalCostField->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
				'before' => VREFactory::getCurrency()->getSymbol(),
			]));
			?>
		</div>
		<?php
	});
?>

<!-- DELIVERY SERVICE - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('delivery_service')
	->value($specialday->delivery_service)
	->label(JText::translate('VRMANAGESPDAY22'))
	->options([
		JHtml::fetch('select.option', -1, JText::translate('VRSPDAYSERVICEOPT1')),
		JHtml::fetch('select.option',  0, JText::translate('VRSPDAYSERVICEOPT2')),
		JHtml::fetch('select.option',  1, JText::translate('VRSPDAYSERVICEOPT3')),
		JHtml::fetch('select.option',  2, JText::translate('VRSPDAYSERVICEOPT4')),
	]);
?>

<!-- MENUS - Select -->

<?php
echo $this->formFactory->createField()
	->type('groupedlist')
	->name($specialday->group == 2 ? 'id_menu' : '')
	->value($specialday->group == 2 ? $specialday->menus : [])
	->id('vr-takeaway-menus')
	->multiple(true)
	->label(JText::translate('VRMANAGESPDAY9'))
	->description(JText::translate('VRMANAGESPDAY9_HELP') . ' ' . JText::translate('VRMANAGESPDAY9_HELP_TK'))
	->options(JHtml::fetch('vrehtml.admin.tkmenus', $blank = false, $group = true));
?>

<!-- DELIVERY AREAS - Select -->

<?php
echo $this->formFactory->createField()
	->type('groupedlist')
	->name('delivery_areas')
	->value($specialday->group == 2 ? $specialday->delivery_areas : [])
	->id('vr-takeaway-areas')
	->multiple(true)
	->label(JText::translate('VRMENUTAKEAWAYDELIVERYAREAS'))
	->description(JText::translate('VRMANAGESPDAYAREAS_HELP'))
	->options(JHtml::fetch('vrehtml.admin.tkareas', $blank = false, $group = true));
?>

<?php
JText::script('VRFILTERSELECTMENU');
JText::script('VRMANAGESPDAYAREAS_SELECT');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vr-takeaway-menus').select2({
				placeholder: Joomla.JText._('VRFILTERSELECTMENU'),
				allowClear: true,
				width: 400,
			});

			$('#vr-takeaway-areas').select2({
				placeholder: Joomla.JText._('VRMANAGESPDAYAREAS_SELECT'),
				allowClear: true,
				width: 400,
			});

			$('select[name="delivery_service"], #askminorder').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			$('#askminorder').on('change', function() {
				const value = parseInt($(this).val());

				const input = $('input[name="minorder"]').attr('min', value);

				if (value) {
					input.val(<?php echo VREFactory::getConfig()->getFloat('mincostperorder', 1); ?>);
					$('.ask-minorder-child').show();
				} else {
					$('.ask-minorder-child').hide();
					input.val(0);
				}
			});
		});
	})(jQuery);
</script>