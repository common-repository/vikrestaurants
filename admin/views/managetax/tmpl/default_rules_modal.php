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

$vik = VREApplication::getInstance();

?>

<div class="inspector-form" id="inspector-tax-rule-form">

	<?php echo $vik->bootStartTabSet('taxrule', ['active' => 'taxrule_details']); ?>

		<!-- DETAILS -->

		<?php echo $vik->bootAddTab('taxrule', 'taxrule_details', JText::translate('VRINVOICEFIELDSET2')); ?>

			<div class="inspector-fieldset">

				<!-- NAME - Text -->

				<?php
				echo $this->formFactory->createField()
					->type('text')
					->id('rule_name')
					->required(true)
					->label(JText::translate('VRMANAGETABLE1'));
				?>

				<!-- MODIFIER - Select -->

				<?php
				echo $this->formFactory->createField()
					->type('select')
					->id('rule_apply')
					->label(JText::translate('VRETAXAPPLY'))
					->options([
						JHtml::fetch('select.option', 1, JText::translate('VRETAXAPPLY_OPT1')),
						JHtml::fetch('select.option', 2, JText::translate('VRETAXAPPLY_OPT2')),
					]);
				?>

				<!-- OPERATION - Select -->

				<?php
				$options = [];

				// get list of supported math operators
				foreach (E4J\VikRestaurants\Taxing\TaxesFactory::getMathOperators() as $value => $text)
				{
					$options[] = JHtml::fetch('select.option', $value, $text);
				}

				echo $this->formFactory->createField()
					->type('select')
					->id('rule_operator')
					->label(JText::translate('VRETAXMATHOP'))
					->options($options);
				?>

				<!-- AMOUNT - Number -->

				<?php
				echo $this->formFactory->createField()
					->type('number')
					->id('rule_amount')
					->label(JText::translate('VRMANAGETKORDDISC5'))
					->min(0)
					->step('any');
				?>

				<!-- TAX CAP - Number -->

				<?php
				echo $this->formFactory->createField()
					->type('number')
					->id('rule_cap')
					->label(JText::translate('VRETAXCAP'))
					->description(JText::translate('VRETAXCAP_HELP'))
					->min(0)
					->step('any')
					->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
						'before' => VREFactory::getCurrency()->getSymbol()
					]));
				?>

			</div>

		<?php echo $vik->bootEndTab(); ?>

		<!-- BREAKDOWN -->

		<?php echo $vik->bootAddTab('taxrule', 'taxrule_breakdown', JText::translate('VRETAXBREAKDOWN')); ?>

			<div style="display:none;" id="tax-bd-repeat">

				<div class="inspector-repeatable-head">
					<span class="tax-bd-summary">
						<i class="fas fa-ellipsis-v big hndl" style="margin-right: 4px;"></i>

						<span class="badge badge-info bd-name"></span>
						<span class="badge badge-important bd-amount"></span>
					</span>

					<span>
						<a href="javascript: void(0);" class="tax-rule-edit-bd no-underline">
							<i class="fas fa-pen-square big ok"></i>
						</a>

						<a href="javascript: void(0);" class="tax-rule-trash-bd no-underline">
							<i class="fas fa-minus-square big no"></i>
						</a>
					</span>
				</div>

				<div class="inspector-repeatable-body">

					<!-- NAME - Text -->

					<?php
					echo $this->formFactory->createField()
						->type('text')
						->class('rule_breakdown_name')
						->label(JText::translate('VRETAXBDLABEL'))
						->placeholder(JText::translate('VRETAXBDPLACEHOLDER'));
					?>

					<!-- AMOUNT - Number -->

					<?php
					echo $this->formFactory->createField()
						->type('number')
						->class('rule_breakdown_amount')
						->label(JText::translate('VRMANAGETKORDDISC5'))
						->step('any')
						->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer('%'));
					?>

					<input type="hidden" class="rule_breakdown_id" />

				</div>

			</div>

			<div class="inspector-repeatable-container" id="tax-bd-pool">
				
			</div>

			<!-- ADD TIME - Button -->

			<?php
			echo $this->formFactory->createField()
				->type('button')
				->id('add-tax-breakdown')
				->text(JText::translate('VRADD'))
				->hidden(true);
			?>

		<?php echo $vik->bootEndTab(); ?>

	<?php echo $vik->bootEndTabSet(); ?>

	<input type="hidden" id="rule_id" value="" />

</div>

<?php
JText::script('VRSYSTEMCONFIRMATIONMSG');
?>

<script>
	(function($, w) {
		'use strict';

		w.fillTaxRuleForm = (data) => {
			// update name
			if (data.name === undefined) {
				data.name = '';
			}

			$('#rule_name').val(data.name);

			w.ruleValidator.unsetInvalid($('#rule_name'));

			// update modifier
			if (data.apply === undefined) {
				data.apply = 1;
			}

			$('#rule_apply').select2('val', data.apply);

			// update operator
			if (data.operator === undefined) {
				data.operator = 'Add';
			}

			$('#rule_operator').select2('val', data.operator);

			// update amount
			if (data.amount === undefined) {
				data.amount = 0.0;
			}

			$('#rule_amount').val(data.amount);

			// update cap
			if (data.cap === undefined) {
				data.cap = 0.0;
			}

			$('#rule_cap').val(data.cap);

			// populate breakdown
			if (data.breakdown === undefined) {
				data.breakdown = [];
			}

			$('#tax-bd-pool').html('');

			data.breakdown.forEach((bd) => {
				addTaxBreakdown(bd);
			});
			
			// update ID
			$('#rule_id').val(data.id);
		}

		w.getTaxRuleData = () => {
			var data = {};

			// set ID
			data.id = $('#rule_id').val();

			// set name
			data.name = $('#rule_name').val();

			// set modifier
			data.apply = $('#rule_apply').val();

			// set operator
			data.operator = $('#rule_operator').val();

			// set amount
			data.amount = parseFloat($('#rule_amount').val());
			data.amount = isNaN(data.amount) ? 0.0 : data.amount;

			// set cap
			data.cap = parseFloat($('#rule_cap').val());
			data.cap = isNaN(data.cap) ? 0.0 : data.cap;

			// set breakdown
			data.breakdown = [];

			// iterate forms
			$('#tax-bd-pool .inspector-repeatable').each(function() {
				let tmp = {};

				// retrieve breakdown ID
				tmp.id = parseInt($(this).find('input.rule_breakdown_id').val());

				// retrieve breakdown name
				tmp.name = $(this).find('input.rule_breakdown_name').val();

				// retrieve breakdown amount
				tmp.amount = parseFloat($(this).find('input.rule_breakdown_amount').val());
				tmp.amount = isNaN(tmp.amount) ? 0.0 : tmp.amount;

				// register breakdown only if not empty
				if (tmp.name.length || tmp.amount != 0) {
					data.breakdown.push(tmp);
				}
			});

			return data;
		}

		const addTaxBreakdown = (data) => {
			if (typeof data !== 'object') {
				data = {};
			}

			let form = $('#inspector-tax-rule-form');

			// get repeatable form of the inspector
			var repeatable = $(form).find('#tax-bd-repeat');
			// clone the form
			var clone = $('<div class="inspector-repeatable"></div>')
				.append(repeatable.clone().html());

			let nameInput = clone.find('input.rule_breakdown_name');

			// set up breakdown name/label
			if (typeof data.name !== 'undefined') {
				nameInput.val(data.name);

				// auto-collapse existing blocks
				clone.addClass('collapsed');
			}

			let amountInput = clone.find('input.rule_breakdown_amount');

			// set up breakdown amount
			if (typeof data.amount !== 'undefined') {
				amountInput.val(data.amount);
			}

			let idInput = clone.find('input.rule_breakdown_id');

			// set up breakdown ID
			idInput.val(data.id || getIncrementalBreakdownID());

			// refresh head every time something changes
			$(nameInput).add(amountInput).on('change', () => {
				let amount = parseFloat($(amountInput).val());

				if (isNaN(amount) || amount <= 0) {
					$(amountInput).val(Math.max(1, $('#rule_amount').val()));
				}

				vreRefreshSummaryBreakdown(clone);
			});

			// set up summary head
			vreRefreshSummaryBreakdown(clone);

			// handle delete button
			clone.find('.tax-rule-trash-bd').on('click', () => {
				if (confirm(Joomla.JText._('VRSYSTEMCONFIRMATIONMSG'))) {
					clone.remove();
				}
			});

			// handle edit button
			clone.find('.tax-rule-edit-bd').on('click', () => {
				clone.toggleClass('collapsed');
			});

			// append the clone to the document
			$('#tax-bd-pool').append(clone);

			// start by focusing "name" input
			nameInput.focus();
		}

		const vreRefreshSummaryBreakdown = (block) => {
			// extract name from block
			let name = block.find('input.rule_breakdown_name').val();

			// extract amount from block
			let amount = parseFloat(block.find('input.rule_breakdown_amount').val());
			amount = isNaN(amount) ? 0 : amount;

			// set badge within block head
			block.find('.tax-bd-summary').find('.bd-name').text(name);
			block.find('.tax-bd-summary').find('.bd-amount').text(amount + '%');
		}

		const getIncrementalBreakdownID = () => {
			let max = 0;

			$('#tax-bd-pool input.rule_breakdown_id').each(function() {
				max = Math.max(max, parseInt($(this).val()));
			});

			return max + 1;
		}

		$(function() {
			w.ruleValidator = new VikFormValidator('#inspector-tax-rule-form');

			$('#rule_apply, #rule_operator').select2({
				minimumResultsForSeach: -1,
				allowClear: false,
				width: '100%',
			});

			$('#add-tax-breakdown').on('click', () => {
				addTaxBreakdown();
			});

			$('#tax-bd-pool').sortable({
				items:  '.inspector-repeatable',
				revert: false,
				axis:   'y',
				handle: '.hndl',
				cursor: 'move',
			});
		});
	})(jQuery, window);
</script>
