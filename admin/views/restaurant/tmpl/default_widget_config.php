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

$layout = new JLayoutFile('form.fields');

?>

<div class="inspector-form" id="inspector-config-form">

	<?php
	foreach ($this->dashboard as $group => $dashboard)
	{
		// iterate all positions
		foreach ($dashboard as $widgets)
		{
			// iterate position widgets
			foreach ($widgets as $widget)
			{
				?>
				<div
					class="inspector-fieldset"
					data-id="<?php echo $this->escape($widget->getID()); ?>"
					data-widget="<?php echo $this->escape($widget->getName()); ?>"
					data-group="<?php echo $this->escape($group); ?>"
					style="display: none;">

					<h3><?php echo $widget->getTitle(); ?></h3>

					<?php
					// get widget description
					$desc = $widget->getDescription();

					if ($desc)
					{
						// display description before the configuration form
						echo $this->formFactory->createField()
							->type('alert')
							->style('info')
							->text($desc)
							->hiddenLabel(true);
					}

					// prepare layout data
					$data = [
						'fields' => $widget->getForm(),
						'params' => $widget->getParams(),
						'prefix' => $widget->getName() . '_' . $widget->getID() . '_',
					];

					// display widget configuration
					echo $layout->render($data);
					?>

				</div>
				<?php
			}
		}
	}
	?>

</div>

<script>
	(function($, w) {
		'use strict';

		w.setupWidgetConfig = (id) => {
			$('.inspector-fieldset').hide();
			$('.inspector-fieldset[data-id="' + id + '"]').show();
		}

		w.getWidgetConfig = (id) => {
			let config = {};

			const widget = $('.inspector-fieldset[data-id="' + id + '"]').data('widget');

			$('.inspector-fieldset[data-id="' + id + '"]')
				.find('input,select')
					.filter('[name^="' + widget + '_"]')
						.each(function() {
							let name = $(this).attr('name').replace(new RegExp('^' + widget + '_' + id + '_'), '');

							if ($(this).is(':checkbox')) {
								config[name] = $(this).is(':checked') ? 1 : 0;
							} else {
								config[name] = $(this).val();
							}
						});

			return config;
		}

		w.getWidgetGroup = (id) => {
			return $('.inspector-fieldset[data-id="' + id + '"]').data('group');
		}

		$(function() {
			VikRenderer.chosen('.inspector-form', '100%');
		});
	})(jQuery, window);
</script>