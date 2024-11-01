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

?>

<div class="inspector-form" id="inspector-tkentry-var-form">

	<div class="inspector-fieldset">

		<h3><?php echo JText::translate('JDETAILS'); ?></h3>
	
		<!-- OPTION NAME - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('option_name')
			->required(true)
			->label(JText::translate('VRMANAGETKMENU4'));
		?>

		<!-- OPTION ALIAS - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('option_alias')
			->label(JText::translate('JFIELD_ALIAS_LABEL'));
		?>

		<!-- OPTION INC PRICE - Number -->

		<?php
		echo $this->formFactory->createField()
			->type('number')
			->id('option_inc_price')
			->label(JText::translate('VRMANAGETKMENU5'))
			->description(JText::translate('VRE_PRODUCT_INC_PRICE_SHORT'))
			->step('any')
			->render(new E4J\VikRestaurants\Form\Renderers\InputGroupFieldRenderer([
				'before' => VREFactory::getCurrency()->getSymbol(),
			]));
		?>

		<!-- PUBLISHED - Checkbox -->

		<?php
		echo $this->formFactory->createField()
			->type('checkbox')
			->id('option_published')
			->label(JText::translate('VRMANAGETKMENU12'));
		?>

	</div>

	<?php if (VREFactory::getConfig()->getBool('tkenablestock')): ?>

		<div class="inspector-fieldset">

			<h3><?php echo JText::translate('VRMANAGECONFIGTKSECTION2'); ?></h3>

			<!-- STOCK ENABLED - Checkbox -->

			<?php
			echo $this->formFactory->createField()
				->type('checkbox')
				->id('option_stock_enabled')
				->label(JText::translate('VRMANAGETKSTOCK5'))
				->description(JText::translate('VRMANAGETKSTOCK5_HELP'));
			?>

			<!-- ITEMS IN STOCK - Number -->

			<?php
			echo $this->formFactory->createField()
				->type('number')
				->id('option_items_in_stock')
				->label(JText::translate('VRMANAGETKSTOCK3'))
				->description(JText::translate('VRMANAGETKSTOCK3_HELP'))
				->min(0)
				->step(1);
			?>

			<!-- NOTIFY BELOW - Number -->

			<?php
			echo $this->formFactory->createField()
				->type('number')
				->id('option_notify_below')
				->label(JText::translate('VRMANAGETKSTOCK4'))
				->description(JText::translate('VRMANAGETKSTOCK4_HELP'))
				->min(0)
				->step(1);
			?>

		</div>

	<?php endif; ?>

	<input type="hidden" id="option_id" class="field" value="" />

</div>

<script>

	(function($, w) {
		'use strict';

		w.fillTkentryVariationForm = (data) => {
			// update name
			if (data.name === undefined) {
				data.name = '';
			}

			$('#option_name').val(data.name);

			optionValidator.unsetInvalid($('#option_name'));

			// update alias
			if (data.alias === undefined) {
				data.alias = '';
			}

			$('#option_alias').val(data.alias);

			// update price
			if (data.inc_price === undefined) {
				data.inc_price = 0.0;
			}

			$('#option_inc_price').val(data.inc_price);

			// update published
			if (data.published === undefined) {
				data.published = true;
			} else if (typeof data.published === 'string') {
				data.published = parseInt(data.published);
			}

			$('#option_published').prop('checked', data.published);

			// update stock enabled
			if (data.stock_enabled === undefined) {
				data.stock_enabled = true;
			} else if (typeof data.stock_enabled === 'string') {
				data.stock_enabled = parseInt(data.stock_enabled);
			}

			$('#option_stock_enabled').prop('checked', data.stock_enabled).trigger('change');

			// update items in stock
			if (data.items_in_stock === undefined) {
				data.items_in_stock = 9999;
			}

			$('#option_items_in_stock').val(data.items_in_stock);

			// update notify below
			if (data.notify_below === undefined) {
				data.notify_below = 5;
			}

			$('#option_notify_below').val(data.notify_below);
			
			// update ID
			$('#option_id').val(data.id);
		}

		w.getTkentryVariationData = () => {
			var data = {};

			// set ID
			data.id = $('#option_id').val();

			// set name
			data.name = $('#option_name').val();

			// set alias
			data.alias = $('#option_alias').val();

			// set price
			data.inc_price = $('#option_inc_price').val();

			// set published
			data.published = $('#option_published').is(':checked') ? 1 : 0;

			// set stock enabled
			data.stock_enabled = $('#option_stock_enabled').is(':checked') ? 1 : 0;

			// set items in stock
			data.items_in_stock = $('#option_items_in_stock').val();

			// set notify below
			data.notify_below = $('#option_notify_below').val();

			return data;
		}

		$(function() {
			w.optionValidator = new VikFormValidator('#inspector-tkentry-var-form');

			$('#option_stock_enabled').on('change', function() {
				let checked;

				if ($(this).attr('type') == 'checkbox') {
					checked = $(this).is(':checked');
				} else {
					checked = parseInt($(this).val());
				}

				$('#option_items_in_stock, #option_notify_below').prop('readonly', checked ? false : true);
			});
		});
	})(jQuery, window);
</script>
