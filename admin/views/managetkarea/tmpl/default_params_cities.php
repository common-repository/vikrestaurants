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

$itemLayout = new JLayoutFile('blocks.card');

?>

<div class="vre-cards-container cards-tkarea-cities" id="cards-tkarea-cities">

	<?php
	if ($this->area->type === 'cities')
	{
		foreach ($this->area->content as $i => $city)
		{
			?>
			<div class="vre-card-fieldset up-to-3" id="tkarea-cities-fieldset-<?php echo (int) $i; ?>">

				<?php
				$displayData = array();

				// fetch card ID
				$displayData['id'] = 'tkarea-cities-card-' . $i;

				// fetch card class
				if (!empty($city->published))
				{
					$displayData['class'] = 'published';
				}

				// fetch primary text
				$displayData['primary'] = $city->name;

				// fetch badge
				$displayData['badge'] = '<i class="fas fa-' . (!empty($city->published) ? 'check-circle' : 'dot-circle') . '"></i>';

				// fetch edit button
				$displayData['edit'] = 'vreOpenTkareaCityCard(' . $i . ');';

				// render layout
				echo $itemLayout->render($displayData);
				?>
				
				<input type="hidden" name="content[cities][]" value="<?php echo $this->escape(json_encode($city)); ?>" />

			</div>
			<?php
		}
	}
	?>

	<div class="vre-card-fieldset up-to-3 add add-tkarea-cities">
		<div class="vre-card compress">
			<i class="fas fa-plus"></i>
		</div>
	</div>

</div>

<div style="display:none;" id="tkarea-cities-struct">
	
	<?php
	// create structure for new items
	$displayData = array();
	$displayData['id']        = 'tkarea-cities-card-{id}';
	$displayData['primary']   = '';
	$displayData['secondary'] = '';
	$displayData['badge']     = '<i class="fas fa-check-circle"></i>';
	$displayData['edit']      = true;

	echo $itemLayout->render($displayData);
	?>

</div>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage delivery area cities
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'tkarea-cities-inspector',
	array(
		'title'       => JText::translate('VRTKAREACITYADD'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
	),
	$this->loadTemplate('params_cities_modal')
);

JText::script('VRTKAREACITYADD');
JText::script('VRTKAREACITYEDIT');
?>

<script>
	(function($, w) {
		'use strict';

		let OPTIONS_COUNT   = <?php echo $this->area->type === 'cities' ? count($this->area->content) : 0; ?>;
		let SELECTED_OPTION = null;

		$(function() {
			// open inspector for new cities
			$('.vre-card-fieldset.add-tkarea-cities').on('click', () => {
				vreOpenTkareaCityCard();
			});

			// fill the form before showing the inspector
			$('#tkarea-cities-inspector').on('inspector.show', function() {
				let data = {};

				// fetch JSON data
				if (SELECTED_OPTION) {
					const fieldset = $('#' + SELECTED_OPTION);

					data = fieldset.find('input[name="content[cities][]"]').val();

					try {
						data = JSON.parse(data);
					} catch (err) {
						data = {};
					}
				}

				if (data.name === undefined) {
					// creating new record, hide delete button
					$('#tkarea-cities-inspector [data-role="delete"]').hide();
				} else {
					// editing existing record, show delete button
					$('#tkarea-cities-inspector [data-role="delete"]').show();
				}

				// fill the form with the retrieved data
				fillTkareaCityForm(data);
			});

			$('#tkarea-cities-inspector').on('inspector.save', function() {
				// get updated city data
				const data = getTkareaCityData();

				if (!data.name) {
					return false;
				}

				let fieldset;

				if (SELECTED_OPTION) {
					fieldset = $('#' + SELECTED_OPTION);
				} else {
					fieldset = vreAddTkareaCityCard(data);
				}

				if (fieldset.length == 0) {
					// an error occurred, abort
					return false;
				}

				// save JSON data
				fieldset.find('input[name="content[cities][]"]').val(JSON.stringify(data));

				// refresh details shown in card
				vreRefreshTkareaCityCard(fieldset, data);

				// auto-close on save
				$(this).inspector('dismiss');
			});

			$('#tkarea-cities-inspector').on('inspector.delete', function() {
				const fieldset = $('#' + SELECTED_OPTION);

				if (fieldset.length == 0) {
					// record not found
					return false;
				}

				// auto delete fieldset
				fieldset.remove();

				// auto-close on delete
				$(this).inspector('dismiss');
			});
		});

		w.vreOpenTkareaCityCard = (index) => {
			let title;

			if (typeof index === 'undefined') {
				title = Joomla.JText._('VRTKAREACITYADD');
				SELECTED_OPTION = null;
			} else {
				title = Joomla.JText._('VRTKAREACITYEDIT');
				SELECTED_OPTION = 'tkarea-cities-fieldset-' + index;
			}
			
			// open inspector
			vreOpenInspector('tkarea-cities-inspector', {title: title});
		}

		const vreAddTkareaCityCard = (data) => {
			let index = OPTIONS_COUNT++;

			let optionIdAttribute = 'tkarea-cities-fieldset-' + index;

			let html = $('#tkarea-cities-struct').clone().html();
			html = html.replace(/{id}/, index);

			$(
				'<div class="vre-card-fieldset up-to-3" id="' + optionIdAttribute + '">' + html + '</div>'
			).insertBefore('.vre-card-fieldset.add-tkarea-cities');

			// get created fieldset
			const fieldset = $('#' + optionIdAttribute);

			fieldset.vrecard('edit', 'vreOpenTkareaCityCard(' + index + ')');

			// create input to hold JSON data
			const input = $('<input type="hidden" name="content[cities][]" />').val(JSON.stringify(data));

			// append input to fieldset
			fieldset.append(input);

			return fieldset;
		}

		const vreRefreshTkareaCityCard = (elem, data) => {
			// update primary text
			elem.vrecard('primary', data.name);

			if (data.published == 1) {
				elem.find('.vre-card').addClass('published');
			} else {
				elem.find('.vre-card').removeClass('published');
			}

			// update published badge
			elem.vrecard('badge', '<i class="fas ' + (data.published == 1 ? 'fa-check-circle' : 'fa-dot-circle') + '"></i>');
		}
	})(jQuery, window);
</script>