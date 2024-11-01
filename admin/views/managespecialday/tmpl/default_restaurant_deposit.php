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

<!-- ASK FOR DEPOSIT - Select -->

<?php
$askDepositThresholdField = $this->formFactory->createField()
	->type('number')
	->name('askdeposit')
	->value($specialday->askdeposit)
	->min(min(2, $specialday->askdeposit))
	->step(1)
	->hiddenLabel(true)
	->control([
		'class' => 'ask-deposit-child',
		'style' => $specialday->askdeposit < 2 ? 'display: none;' : '',
	]);

echo $this->formFactory->createField()
	->type('select')
	->id('vr-askdeposit-sel')
	->value(min(2, $specialday->askdeposit))
	->class('small-medium')
	->label(JText::translate('VRMANAGECONFIG89'))
	->description(JText::translate('VRMANAGECONFIG89_HELP'))
	->options([
		JHtml::fetch('select.option', 0, JText::translate('VRCONFIGLOGINREQ1')),
		JHtml::fetch('select.option', 1, JText::translate('VRTKCONFIGOVERLAYOPT2')),
		JHtml::fetch('select.option', 2, JText::translate('VRPEOPLEALLOPT2')),
	])
	->render(function($data, $input) use ($askDepositThresholdField) {
		?>
		<div class="multi-field width-50">
			<?php
			// display the select first
			echo $input;

			// then display the deposit threshold (people)
			echo $askDepositThresholdField->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer(
				strtolower(JText::translate('VRORDERPEOPLE'))
			));
			?>
		</div>
		<?php
	});
?>

<!-- RESERVATION DEPOSIT - Number -->

<?php
echo $this->formFactory->createField()
	->type('number')
	->name('depositcost')
	->value($specialday->depositcost)
	->label(JText::translate('VRMANAGECONFIG18'))
	->description(JText::translate('VRMANAGECONFIG18_DESC'))
	->min(0)
	->step('any')
	->control([
		'class' => 'vr-deposit-child',
		'style' => $specialday->askdeposit < 1 ? 'display: none;' : '',
	])
	->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
		'before' => VREFactory::getCurrency()->getSymbol(),
	]));
?>

<!-- COST PER PERSON - Checkbox -->

<?php
echo $this->formFactory->createField()
	->type('checkbox')
	->name('perpersoncost')
	->checked($specialday->perpersoncost)
	->label(JText::translate('VRMANAGECONFIG19'))
	->description(JText::translate('VRMANAGECONFIG19_DESC'))
	->control([
		'class' => 'vr-deposit-child',
		'style' => $specialday->askdeposit < 1 ? 'display: none;' : '',
	]);
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#vr-askdeposit-sel').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			$('#vr-askdeposit-sel').on('change', function() {
				const value = parseInt($(this).val());

				$('input[name="askdeposit"]').attr('min', value).val(value);

				if (value > 0) {
					$('.vr-deposit-child').show();
				} else {
					$('.vr-deposit-child').hide();
				}

				if (value > 1) {
					$('.ask-deposit-child').show();
				} else {
					$('.ask-deposit-child').hide();
				}
			});
		});
	})(jQuery);
</script>
