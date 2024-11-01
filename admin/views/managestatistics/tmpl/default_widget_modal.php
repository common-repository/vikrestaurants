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

<div class="inspector-form" id="inspector-widget-form">

	<div class="inspector-fieldset">

		<!-- WIDGET NAME - Text -->

		<?php
		echo $this->formFactory->createField()
			->type('text')
			->name('widget_name')
			->label(JText::translate('VRE_WIDGET_NAME'))
			->description(JText::translate('VRE_WIDGET_NAME_DESC'));
		?>

		<!-- WIDGET CLASS - Select -->

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('widget_class')
			->required(true)
			->label(JText::translate('VRE_WIDGET_CLASS'))
			->description(JText::translate('VRE_WIDGET_CLASS_DESC'))
			->options(array_merge(
				[
					JHtml::fetch('select.option', '', ''),
				],
				array_values(array_map(function($widget)
				{
					return JHtml::fetch('select.option', $widget->getName(), $widget->getTitle());
				}, $this->supported))
			));
		?>

		<!-- WIDGET POSITION - Select -->

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('widget_position')
			->required(true)
			->label(JText::translate('VRE_WIDGET_POSITION'))
			->description(JText::translate('VRE_WIDGET_POSITION_DESC'))
			->options(array_merge(
				[
					JHtml::fetch('select.option', '', ''),
				],
				array_map(function($position)
				{
					return JHtml::fetch('select.option', $position, $position);
				}, $this->positions)
			));
		?>

		<!-- WIDGET SIZE - Select -->

		<?php
		echo $this->formFactory->createField()
			->type('select')
			->name('widget_size')
			->label(JText::translate('VRE_WIDGET_SIZE'))
			->description(JText::translate('VRE_WIDGET_SIZE_DESC'))
			->options([
				JHtml::fetch('select.option',            '',                                          ''),
				JHtml::fetch('select.option', 'extra-small', JText::translate('VRE_WIDGET_SIZE_OPT_EXTRA_SMALL')),
				JHtml::fetch('select.option',       'small',       JText::translate('VRE_WIDGET_SIZE_OPT_SMALL')),
				JHtml::fetch('select.option',      'normal',      JText::translate('VRE_WIDGET_SIZE_OPT_NORMAL')),
				JHtml::fetch('select.option',       'large',       JText::translate('VRE_WIDGET_SIZE_OPT_LARGE')),
				JHtml::fetch('select.option', 'extra-large', JText::translate('VRE_WIDGET_SIZE_OPT_EXTRA_LARGE')),
			]);
		?>

	</div>

	<?php foreach ($this->supported as $widget): ?>
		<div 
			class="inspector-fieldset widget-desc"
			data-name="<?php echo $this->escape($widget->getName()); ?>"
			data-title="<?php echo $this->escape($widget->getTitle()); ?>"
			style="display:none;"
		>
			<?php
			// show widget description, if any
			$desc = $widget->getDescription();

			if ($desc)
			{
				echo $this->formFactory->createField()
					->type('alert')
					->style('info')
					->text($desc)
					->hiddenLabel(true);
			}
			?>
		</div>
	<?php endforeach; ?>

	<input type="hidden" name="widget_id" value="0" />

</div>

<?php
JText::script('VRE_WIDGET_SELECT_CLASS');
JText::script('VRE_WIDGET_SELECT_POSITION');
JText::script('VRE_WIDGET_SIZE_OPT_DEFAULT');
?>

<script>
	(function($, w) {
		'use strict';

		w.setupWidgetData = (data) => {
			// fill ID
			$('#inspector-widget-form input[name="widget_id"]').val(data.id ? data.id : 0);

			// fill name
			$('#inspector-widget-form input[name="widget_name"]').val(data.name || '');

			// fill widget class
			data.widget = data.widget || data.class;

			$('#inspector-widget-form select[name="widget_class"]').select2('val', data.widget ? data.widget : '').trigger('change');

			// fill widget position
			$('#inspector-widget-form select[name="widget_position"]').select2('val', data.position ? data.position : '');

			// fill widget size
			$('#inspector-widget-form select[name="widget_size"]').select2('val', data.size ? data.size : '');
		}

		w.getWidgetData = () => {
			let data = {};

			// extract widget data
			$('#inspector-widget-form')
				.find('input,select')
					.filter('[name^="widget_"]')
						.each(function() {
							var name  = $(this).attr('name').replace(/^widget_/, '');
							var value = $(this).val();

							data[name] = value;
						});

			// replicate CLASS in WIDGET property
			data.widget = data.class;

			return data;
		}

		w.getDefaultWidget = (widgetName) => {
			// get widget
			const widget = $('#inspector-widget-form .widget-desc[data-name="' + widgetName + '"]');

			let data = {
				name:        widget.data('name'),
				title:       widget.data('title'),
				description: widget.html(),
			};

			return data;
		}

		w.addPositionOption = (position) => {
			$('#inspector-widget-form select[name="widget_position"]').append(
				$('<option></option>').val(position).text(position)
			);
		}

		$(function() {
			w.widgetValidator = new VikFormValidator('#inspector-widget-form');

			$('#inspector-widget-form select[name="widget_class"]').select2({
				placeholder: Joomla.JText._('VRE_WIDGET_SELECT_CLASS'),
				allowClear: false,
			});

			$('#inspector-widget-form select[name="widget_position"]').select2({
				placeholder: Joomla.JText._('VRE_WIDGET_SELECT_POSITION'),
				allowClear: false,
			});

			$('#inspector-widget-form select[name="widget_size"]').select2({
				minimumResultsForSearch: -1,
				placeholder: Joomla.JText._('VRE_WIDGET_SIZE_OPT_DEFAULT'),
				allowClear: true,
			});

			$('#inspector-widget-form select[name="widget_class"]').on('change', function() {
				// hide all descriptions
				$('#inspector-widget-form .widget-desc').hide();

				// get selected widget
				const widget = $('#inspector-widget-form .widget-desc[data-name="' + $(this).val() + '"]');

				// get name input
				const nameInput = $('#inspector-widget-form input[name="widget_name"]');

				// set up placeholder
				nameInput.attr('placeholder', widget.data('title'));

				if (nameInput.val() == widget.data('title')) {
					// specified title is equals to the default one, unset it
					nameInput.val('');
				}

				// show description of selected widget
				widget.show();
			});
		});
	})(jQuery, window);
</script>