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

$varLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-cards-container cards-tkentry-variations" id="cards-tkentry-variations">

	<?php
	foreach ($this->entry->options as $i => $option)
	{
		?>
		<div class="vre-card-fieldset up-to-3" id="tkentry-var-fieldset-<?php echo (int) $i; ?>">

			<?php
			$displayData = array();

			// fetch card ID
			$displayData['id'] = 'tkentry-var-card-' . $i;

			// fetch card class
			if ($option->published)
			{
				$displayData['class'] = 'published';
			}

			// fetch primary text
			$displayData['primary']  = $option->name;

			// fetch secondary text
			$displayData['secondary'] = '<span class="badge badge-info option-cost">' . $currency->format($option->inc_price) . '</span>';

			// fetch badge
			$displayData['badge'] = '<i class="fas fa-' . ($option->published ? 'check-circle' : 'dot-circle') . '"></i>';

			// fetch edit button
			$displayData['edit'] = 'vreOpenTkentryVariationCard(' . $i . ');';

			// render layout
			echo $varLayout->render($displayData);
			?>
			
			<input type="hidden" name="option_json[]" value="<?php echo $this->escape(json_encode($option)); ?>" />

		</div>
		<?php
	}

	/**
	 * Preserve the deleted variations in case of failure.
	 * 
	 * @since 1.9
	 */
	if (!empty($this->entry->deleted_options))
	{
		foreach ($this->entry->deleted_options as $deleted)
		{
			?><input type="hidden" name="option_deleted[]" value="<?php echo (int) $deleted; ?>" /><?php
		}
	}
	?>

	<div class="vre-card-fieldset up-to-3 add add-tkentry-var">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="tkentry-var-struct">
	
	<?php
	// create entry option structure for new items
	$displayData = array();
	$displayData['id']        = 'tkentry-var-card-{id}';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';
	$displayData['badge']     = '<i class="fas fa-check-circle"></i>';
	$displayData['edit']      = true;

	echo $varLayout->render($displayData);
	?>

</div>

<?php
JText::script('VRE_ADD_VARIATION');
JText::script('VRE_EDIT_VARIATION');
?>

<script>
	(function($, w) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo count($this->entry->options); ?>;
		let SELECTED_OPTION = null;

		w.getSupportedTkentryVariations = () => {
			let variations = [];

			$('input[name="option_json[]"]').each(function() {
				variations.push(JSON.parse($(this).val()));
			});

			return variations;
		}

		$(function() {
			// open inspector for new variations
			$('.vre-card-fieldset.add-tkentry-var').on('click', () => {
				vreOpenTkentryVariationCard();
			});

			$('#cards-tkentry-variations').sortable({
				// exclude "add" box
				items: '.vre-card-fieldset:not(.add)',
				// hide "add" box when sorting starts
				start: function() {
					$('.vre-card-fieldset.add-tkentry-var').hide();
				},
				// show "add" box again when sorting stops
				stop: function() {
					$('.vre-card-fieldset.add-tkentry-var').show();
				},
			});

			// fill the form before showing the inspector
			$('#tkentry-var-inspector').on('inspector.show', function() {
				let data = [];

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					data = fieldset.find('input[name="option_json[]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				if (data.id === undefined) {
					// creating new record, hide delete button
					$('#tkentry-var-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#tkentry-var-inspector [data-role="delete"]').show();
				}

				// fill the form with the retrieved data
				fillTkentryVariationForm(data);
			});

			$('#tkentry-var-inspector').on('inspector.save', function() {
				// validate form
				if (!optionValidator.validate()) {
					return false;
				}

				// get updated product variation data
				const data = getTkentryVariationData();

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddTkentryVariationCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="option_json[]"]').val(JSON.stringify(data));

				// refresh details shown in card
				vreRefreshTkentryVariationCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			$('#tkentry-var-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// get existing record
				let json = fieldset.find('input[name="option_json[]"]').val();

				try {
					json = JSON.parse(json);
				} catch (err) {
					json = {};
				}

				if (json.id) {
					// commit record delete
					$('#adminForm').append('<input type="hidden" name="option_deleted[]" value="' + json.id + '" />');
				}

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});
		});

		w.vreOpenTkentryVariationCard = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRE_ADD_VARIATION');
				SELECTED_OPTION = null;
			} else {
				title = Joomla.JText._('VRE_EDIT_VARIATION');
				SELECTED_OPTION = 'tkentry-var-fieldset-' + index;
			}
			
			// open inspector
			vreOpenInspector('tkentry-var-inspector', {title: title});
		}

		const vreAddTkentryVariationCard = (data) => {
			let index = OPTIONS_COUNT++;

			SELECTED_OPTION = 'tkentry-var-fieldset-' + index;

			let html = $('#tkentry-var-struct').clone().html();
			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-3" id="' + SELECTED_OPTION + '">' + html + '</div>'
			).insertBefore('.vre-card-fieldset.add-tkentry-var');

			// get created fieldset
			const fieldset = $('#' + SELECTED_OPTION);

			fieldset.vrecard('edit', 'vreOpenTkentryVariationCard(' + index + ')');

			// create input to hold JSON data
			const input = $('<input type="hidden" name="option_json[]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshTkentryVariationCard = (elem, data) => {
			// update primary text
			elem.vrecard('primary', data.name);

			// update secondary text
			elem.vrecard('secondary', $('<span class="badge badge-info option-cost"></span>').html(Currency.getInstance().format(data.inc_price)));

			if (data.published == 1) {
				elem.find('.vre-card').addClass('published');
			} else {
				elem.find('.vre-card').removeClass('published');
			}

			// update variation badge
			elem.vrecard('badge', '<i class="fas ' + (data.published == 1 ? 'fa-check-circle' : 'fa-dot-circle') + '"></i>');
		}
	})(jQuery, window);
</script>
