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

$currency = VREFactory::getCurrency();

$itemLayout = new JLayoutFile('blocks.card');

?>

<!-- FILTERS -->

<div class="btn-toolbar" style="display: inline-block; width: 100%;">

	<div class="btn-group pull-left input-append hide-with-size-320">
		<?php
		/**
		 * NOTE: use a class instead of an ID and a name so that the inspector won't consider it
		 * while checking whether something has changed in case the user decided to close the page.
		 */
		?>
		<input type="text" class="search-items-text" size="32" placeholder="<?php echo $this->escape(JText::translate('JSEARCH_FILTER_SUBMIT')); ?>" />

		<button type="button" class="btn" id="search-items-btn">
			<i class="fas fa-search"></i>
		</button>
	</div>

	<div class="btn-group pull-left hide-with-size-860">
		<button type="button" class="btn" id="clear-items-btn">
			<?php echo JText::translate('JSEARCH_FILTER_CLEAR'); ?>
		</button>
	</div>

	<div class="btn-group pull-right">
		<button type="button" class="btn btn-success" id="create-item-btn">
			<i class="fas fa-plus-circle"></i>
			<span class="hidden-phone"><?php echo JText::translate('VRCREATENEWPROD'); ?></span>
		</button>
	</div>

</div>

<!-- NO RESULTS -->

<?php
echo $this->formFactory->createField()
    ->type('alert')
    ->text(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'))
    ->style('warning')
    ->hiddenLabel(true)
    ->control([
    	'visible' => $this->allProducts ? false : true,
    	'class'   => 'no-prod-results-alert',
    ]);
?>

<!-- PRODUCTS -->

<div class="vre-cards-container cards-reservation-items" id="cards-reservation-items">

	<?php
	foreach ($this->allProducts as $item)
	{
		?>
		<div
			class="vre-card-fieldset up-to-3"
			id="reservation-item-fieldset-<?php echo (int) $item->id; ?>"
			data-name="<?php echo $this->escape((string) $item->name); ?>"
			data-description="<?php echo $this->escape(strip_tags((string) $item->description)); ?>"
		>

			<?php
			$displayData = [];

			// fetch card ID
			$displayData['id'] = 'reservation-item-card-' . $item->id;

			// if (empty($item->image) || !is_file(VREMEDIA . DIRECTORY_SEPARATOR . $item->image))
			// {
			//     // use default item image
			//     $displayData['image'] = VREASSETS_ADMIN_URI . 'images/product-placeholder.png';
			// }
			// else
			// {
			//     // use item image URI
			//     $displayData['image'] = VREMEDIA_URI . $item->image;
			// }

			// fetch primary text
			$displayData['primary'] = $item->name;

			// fetch secondary text
			$displayData['secondary'] = '<span class="badge badge-info">' . $currency->format($item->price) . '</span>';

			// fetch edit button
			$displayData['edit']     = 'loadReservationItemForm({id_product: ' . $item->id . '});';
			$displayData['editText'] = JText::translate('VRADD');

			// render layout
			echo $itemLayout->render($displayData);
			?>
		</div>
		<?php
	}
	?>

</div>

<script>
	(function($) {
		'use strict';

		const filterItems = () => {
			const search = $('.search-items-text').val().toLowerCase();

			let matches = 0;

			$('.vre-card-fieldset').each(function() {
				if (isMatchingItem(this, search)) {
					$(this).show();
					matches++;
				} else {
					$(this).hide();
				}
			});

			if (matches) {
				$('.no-prod-results-alert').hide();
			} else {
				$('.no-prod-results-alert').show();
			}
		}

		const isMatchingItem = (box, search) => {
			if (!search.length) {
				// no active search
				return true;
			}

			// search by product name
			let itemName = ('' + $(box).attr('data-name')).trim().toLowerCase();

			if (itemName.indexOf(search) !== -1) {
				return true;
			}

			// search by description
			let itemDesc = ('' + $(box).attr('data-description')).trim().toLowerCase();

			if (itemDesc.indexOf(search) !== -1) {
				return true;
			}

			return false;
		}

		$(function() {
			// apply search filters
			$('.search-items-text').on('change', filterItems);
			$('#search-items-btn').on('click', filterItems);

			// reset search filters
			$('#clear-items-btn').on('click', () => {
				$('.search-items-text').val('').trigger('change');
			});

			// open form to create a new item
			$('#create-item-btn').on('click', () => {
				loadReservationItemForm({});
			});
		});
	})(jQuery);
</script>