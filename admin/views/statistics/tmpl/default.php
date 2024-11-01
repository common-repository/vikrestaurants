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

JHtml::fetch('formbehavior.chosen');
JHtml::fetch('vrehtml.assets.chartjs');
JHtml::fetch('vrehtml.assets.fontawesome');

$vik = VREApplication::getInstance();

?>

<script type="text/javascript">
	
	/**
	 * Prepare any chart to be responsive.
	 */
	Chart.defaults.global.responsive = true;

	/**
	 * Keep a reference of the widget that was clicked
	 * to update its configuration.
	 *
	 * @var integer
	 */
	var SELECTED_WIDGET = null;

	/**
	 * A lookup of preflights to be used before refreshing
	 * the contents of the widgets.
	 *
	 * If needed, a widget can register its own callback
	 * to be executed before the AJAX request is started.
	 *
	 * The property name MUST BE equals to the ID of 
	 * the widget that is registering its callback.
	 *
	 * @var object
	 */
	var WIDGET_PREFLIGHTS = {};

	/**
	 * A lookup of callbacks to be used when refreshing
	 * the contents of the widgets.
	 *
	 * If needed, a widget can register its own callback
	 * to be executed once the AJAX request is completed.
	 *
	 * The property name MUST BE equals to the ID of 
	 * the widget that is registering its callback.
	 *
	 * @var object
	 */
	var WIDGET_CALLBACKS = {};

</script>

<!-- LAYOUT CUSTOM STYLE -->

<style>
	<?php if ($this->layout == 'floating'): ?>
		/* change body background when the layout is FLOATING */
		body {
			background-color: #f3f2f6;
		}
	<?php endif; ?>
</style>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<!-- WIDGETS -->

	<div class="row-fluid" id="statistics-wrapper" style="margin-top: 2px;">

		<?php foreach ($this->dashboard as $position => $widgets): ?>
			
			<div class="dashboard-widgets-container <?php echo $this->escape($this->layout); ?>" data-position="<?php echo $this->escape($position); ?>">

				<?php
				foreach ($widgets as $i => $widget)
				{
					$id = $widget->getID();

					// get widget description
					$help = $widget->getDescription();

					if ($help)
					{
						// create popover with the widget description
						$help = $vik->createPopover([
							'title'   => $widget->getTitle(),
							'content' => $help,
						]);
					}

					// widen or shorten the widget
					switch ($widget->getSize())
					{
						// force EXTRA SMALL width : (100% / N) - (100% / (N * 2))
						case 'extra-small':
							$width = 'width: calc((100% / ' . count($widgets) . ') - (100% / ' . (count($widgets) * 2) . '));';
							break;

						// force SMALL width : (100% / N) - (100% / (N * 4))
						case 'small':
							$width = 'width: calc((100% / ' . count($widgets) . ') - (100% / ' . (count($widgets) * 4) . '));';
							break;

						// force NORMAL width : (100% / N)
						case 'normal':
							$width = 'width: calc(100% / ' . count($widgets) . ');';
							break;

						// force LARGE width : (100% / N) + (100% / (N * 4))
						case 'large':
							$width = 'width: calc((100% / ' . count($widgets) . ') + (100% / ' . (count($widgets) * 4) . '));';
							break;

						// force EXTRA LARGE width : (100% / N) + (100% / (N * 2))
						case 'extra-large':
							$width = 'width: calc((100% / ' . count($widgets) . ') + (100% / ' . (count($widgets) * 2) . '));';
							break;

						// fallback to flex basis to take the remaining space
						default:
							$width = 'flex: 1;';
					}
					?>
					<div
						class="dashboard-widget"
						id="widget-<?php echo $this->escape($id); ?>"
						data-widget="<?php echo $this->escape($widget->getName()); ?>"
						style="<?php echo $this->escape($width); ?>"
					>

						<div class="widget-wrapper">
							<div class="widget-head">
								<h3><?php echo $widget->getTitle() . $help; ?></h3>

								<a href="javascript:void(0)" onclick="openWidgetConfiguration('<?php echo $id; ?>');" class="widget-config-btn">
									<i class="fas fa-ellipsis-h"></i>
								</a>
							</div>

							<div class="widget-body">
								<?php echo $widget->display(); ?>
							</div>

							<div class="widget-error-box" style="display: none;">
								<?php
								echo $this->formFactory->createField()
									->type('alert')
									->style('error')
									->text(JText::translate('VRE_AJAX_GENERIC_ERROR'))
									->hidden(true);
								?>
							</div>
						</div>

					</div>
					<?php
				}
				?>

			</div>

		<?php endforeach; ?>

	</div>
	
	<input type="hidden" name="group" value="<?php echo $this->escape($this->group); ?>" />
	<input type="hidden" name="location" value="statistics" />
	<input type="hidden" name="view" value="statistics" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
// render inspector to manage widgets configuration
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'widget-config-inspector',
	[
		'title'       => JText::translate('VRMENUCONFIG'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => '<button type="button" class="btn btn-success" id="widget-save-config" data-role="save">' . JText::translate('JAPPLY') . '</button>',
		'width'       => 400,
	],
	$this->loadTemplate('widget_config')
);
?>

<script>
	/**
	 * A pool containing the active AJAX requests for each
	 * widget, so that we can abort an existing request
	 * before launching a new one.
	 *
	 * @var object
	 */
	var CHARTS_REQUESTS_POOL = {};

	jQuery(function() {

		// fill the form before showing the inspector
		jQuery('#widget-config-inspector').on('inspector.show', function() {
			setupWidgetConfig(SELECTED_WIDGET);
		});

		// refresh widget
		jQuery('#widget-save-config').on('click', function() {
			// refresh the contents displayed within the widget
			updateWidgetContents(SELECTED_WIDGET);

			// dismiss inspector
			jQuery('#widget-config-inspector').inspector('dismiss');
		});

		<?php
		// iterate dashboard widgets
		foreach ($this->dashboard as $widgets)
		{
			// iterate position widgets
			foreach ($widgets as $widget)
			{
				// load widget contents once the page is ready
				?>
				updateWidgetContents('<?php echo $widget->getID(); ?>');
				<?php
			}
		}
		?>

	});

	function openWidgetConfiguration(widget) {
		SELECTED_WIDGET = widget;

		// open inspector
		vreOpenInspector('widget-config-inspector');
	}

	function updateWidgetContents(id, config) {
		if (typeof config === 'undefined') {
			// get widget configuration if not specified
			config = getWidgetConfig(id);
		}

		// abort any existing request already made for this widget
		if (CHARTS_REQUESTS_POOL.hasOwnProperty(id)) {
			CHARTS_REQUESTS_POOL[id].abort();
		}

		// keep a reference to the widget
		var box = jQuery('#widget-' + id);

		// get widget class
		var widget = box.data('widget');

		// prepare request data
		Object.assign(config, {
			id:     id,
			widget: widget,
			group:  '<?php echo $this->group; ?>',
		});

		// hide generic error message
		jQuery(box).find('.widget-error-box').hide();
		// show widget body
		jQuery(box).find('.widget-body').show();

		if (WIDGET_PREFLIGHTS.hasOwnProperty(id)) {
			// let the widget prepares the contents without
			// waiting for the request completion
			WIDGET_PREFLIGHTS[id](box, config);
		}

		// make request to load widget dataset
		var xhr = UIAjax.do(
			'<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=statistics.loadwidgetdata&tmpl=component'); ?>',
			config,
			(resp) => {
				// delete request from pool
				delete CHARTS_REQUESTS_POOL[id];

				// check if the widget registered its own update method
				if (WIDGET_CALLBACKS.hasOwnProperty(id)) {
					// let the widget callback finalizes the update
					WIDGET_CALLBACKS[id](box, resp, config);
				} else {
					// replace widget body
					jQuery(box).find('.widget-body').html(resp);
				}
			},
			(error) => {
				// delete request from pool
				delete CHARTS_REQUESTS_POOL[id];

				// hide widget body
				jQuery(box).find('.widget-body').hide();
				// show generic error message
				jQuery(box).find('.widget-error-box').show();
			}
		);

		// update request pool
		CHARTS_REQUESTS_POOL[id] = xhr;
	}
</script>