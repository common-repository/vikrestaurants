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

$currency = VREFactory::getCurrency();

$cardLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-products-modal" style="padding: 10px;">

	<div class="btn-toolbar" style="height: 32px;">

		<div class="btn-group pull-left input-append hide-with-size-320">
			<input type="text" id="prodkeysearch" size="32" value="" placeholder="<?php echo JText::translate('JSEARCH_FILTER_SUBMIT'); ?>" autocomplete="off" />

			<button type="button" class="btn" onclick="jQuery('#prodkeysearch').trigger('change');">
				<i class="icon-search"></i>
			</button>
		</div>

		<div class="btn-group pull-left hide-with-size-390">
			<button type="button" class="btn" onclick="jQuery('#prodkeysearch').val('').trigger('change');"><?php echo JText::translate('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="btn-group pull-left hide-with-size-540">
			<button type="button" class="btn" onclick="vreToggleSelectedProducts(this);" id="prod-selection-count">
				<i class="fas fa-dot-circle"></i>&nbsp;&nbsp;
				<span class="text-box"></span>
			</button>
		</div>

		<div class="btn-group pull-right hide-with-size-860">
			<button type="button" class="btn" onclick="vreSelectAllProducts(1);"><?php echo JText::translate('JGLOBAL_SELECTION_ALL'); ?></button>
			<button type="button" class="btn" onclick="vreSelectAllProducts(0);"><?php echo JText::translate('JGLOBAL_SELECTION_NONE'); ?></button>
		</div>

	</div>

	<?php
	$attrs = array();
	$attrs['id'] = 'no-prod-results-alert';

	if ($this->products)
	{
		$attrs['style'] = 'display:none';
	}

	echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'), 'warning', false, $attrs);
	?>

	<div class="vre-products-gallery vre-cards-container">

		<?php
		foreach ($this->products as $prod)
		{
			if (empty($prod->image) || !is_file(VREMEDIA . DIRECTORY_SEPARATOR . $prod->image))
			{
				$image = VREASSETS_ADMIN_URI . 'images/product-placeholder.png';
			}
			else
			{
				$image = VREMEDIA_URI . $prod->image;
			}

			$description = strip_tags($prod->description);

			$displayData = array();

			// fetch image
			$displayData['image'] = $image;

			// fetch primary block
			$displayData['primary']  = '<strong class="product-name">' . $prod->name . '</strong>';
			$displayData['primary'] .= '<span class="product-price">' . $currency->format($prod->price) . '</span>';

			// fetch secondary block
			if ($description)
			{
				$displayData['secondary'] = $description;
			}

			?>
			<div class="vre-product-block vre-card-fieldset"
				data-id="<?php echo $prod->id; ?>"
				data-name="<?php echo $this->escape($prod->name); ?>"
				data-price="<?php echo $prod->price; ?>"
				data-description="<?php echo $this->escape($description); ?>"
				data-selected="0"
			>
				
				<?php echo $cardLayout->render($displayData); ?>

			</div>
			<?php
		}
		?>

	</div>

</div>

<?php
JText::script('VRE_DEF_N_SELECTED');
JText::script('VRE_DEF_N_SELECTED_1');
JText::script('VRE_DEF_N_SELECTED_0');
?>

<script>
	(function($, w) {
		'use strict';

		w.vreSelectAllProducts = (is) => {
			$('.vre-product-block:visible').each(function() {
				$(this).attr('data-selected', is);
			});

			vreUpdateProductSelectionCount();
		}

		w.vreToggleSelectedProducts = (btn) => {
			if ($(btn).hasClass('active')) {
				$(btn).removeClass('active');

				$(btn).find('i').attr('class', 'fas fa-dot-circle');
			} else {
				$(btn).addClass('active');

				$(btn).find('i').attr('class', 'fas fa-check-circle');
			}

			$('#prodkeysearch').trigger('change');
		}

		w.vreGetSelectedProducts = () => {
			let products = [];

			$('.vre-product-block[data-selected="1"]').each(function() {
				products.push({
					id: $(this).data('id'),
					name: $(this).data('name'),
					price: $(this).data('price'),
					description: $(this).data('description'),
				});
			});

			return products;
		}

		w.vreInitProductsLayout = (products) => {
			$('.vre-product-block').each(function() {
				const id = parseInt($(this).data('id'));

				$(this).attr('data-selected', products.indexOf(id) === -1 ? 0 : 1);
			});

			vreUpdateProductSelectionCount();
		}

		const vreMatchingProdBox = (box, search) => {
			// search by product name
			const prod_name = ('' + $(box).data('name')).trim().toLowerCase();

			if (prod_name.indexOf(search) !== -1) {
				return true;
			}

			// search by description
			const prod_desc = ('' + $(box).data('description')).trim().toLowerCase();

			if (prod_desc.indexOf(search) !== -1) {
				return true;
			}

			return false;
		}

		const vreUpdateProductSelectionCount = () => {
			let count = $('.vre-product-block[data-selected="1"]').length;
			let text  = '';

			switch (count) {
				case 0:
					text = Joomla.JText._('VRE_DEF_N_SELECTED_0');
					break;

				case 1:
					text = Joomla.JText._('VRE_DEF_N_SELECTED_1');
					break;

				default:
					text = Joomla.JText._('VRE_DEF_N_SELECTED').replace(/%d/, count);
			}

			$('#prod-selection-count .text-box').text(text);

			if ($('#prod-selection-count').hasClass('active')) {
				$('#prodkeysearch').trigger('change');
			}
		}

		$(function() {
			$('.vre-product-block').on('click', function() {
				const checked = parseInt($(this).attr('data-selected'));

				$(this).attr('data-selected', (checked + 1) % 2);

				vreUpdateProductSelectionCount();
			});

			$('#prodkeysearch').on('change', function() {
				const search = $(this).val().toLowerCase();

				let showSelected = $('#prod-selection-count').hasClass('active');
				let at_least_one = false;

				$('.vre-product-block').each(function() {
					if ((!search.length || vreMatchingProdBox(this, search))
						// and make sure the box is selected when viewing selected records only
						&& (!showSelected || $(this).attr('data-selected') == 1)) {
						$(this).show();
						at_least_one = true;
					} else {
						$(this).hide();
					}
				});

				if (at_least_one) {
					$('#no-prod-results-alert').hide();
				} else {
					$('#no-prod-results-alert').show();
				}
			});
		});
	})(jQuery, window);
</script>