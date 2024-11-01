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

<div class="inspector-form" id="inspector-product-var-form">

	<div class="inspector-fieldset">
		<h3><?php echo JText::translate('VRMAPDETAILSBUTTON'); ?></h3>
	
		<!-- OPTION NAME - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->id('option_name')
			->required(true)
			->label(JText::translate('VRMANAGETKMENU4'));
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

		<!-- ID - Hidden -->

		<?php echo $this->formFactory->createField()->type('hidden')->id('option_id'); ?>

	</div>

</div>

<script>
	(function($, w) {
		'use strict';

		w.fillProductOptionForm = (data) => {
			// update name
			if (data.name === undefined) {
				data.name = '';
			}

			$('#option_name').val(data.name);

			optionValidator.unsetInvalid($('#option_name'));

			// update price
			if (data.inc_price === undefined) {
				data.inc_price = 0.0;
			}

			$('#option_inc_price').val(data.inc_price);
			
			// update ID
			$('#option_id').val(data.id);
		}

		w.getProductOptionData = () => {
			let data = {};

			// set ID
			data.id = $('#option_id').val();

			// set name
			data.name = $('#option_name').val();

			// set price
			data.inc_price = $('#option_inc_price').val();

			return data;
		}

		$(function() {
			w.optionValidator = new VikFormValidator('#inspector-product-var-form');
		});
	})(jQuery, window);
</script>