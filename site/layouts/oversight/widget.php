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

/**
 * Layout variables
 * -----------------
 * @var  string    $widget  The name of the widget to instantiate.
 * @var  string    $group   The group to which the widget belongs.
 * @var  mixed     $config  An optional configuration array for the widget.
 * @var  int       $timer   The timeout interval in seconds.
 * @var  int|null  $itemid  An optional menu item ID.
 */
extract($displayData);

VRELoader::import('library.statistics.factory');

// instantiate specified widget
$widget = VREStatisticsFactory::getInstance($widget, $group);

if (empty($config))
{
	// use empty object for configuration
	$config = new stdClass;
}

echo $this->sublayout('script', $displayData);

?>

<script>
	(function(w) {
		'use strict';

		w.registerDashboardWidget(
			<?php echo $widget->getID(); ?>,
			'<?php echo addslashes($group); ?>',
			<?php echo json_encode($config); ?>
		);
	})(window);
</script>

<div class="row-fluid" style="margin-top:2px;">

	<div class="dashboard-widgets-container" data-position="center">

		<div
			class="dashboard-widget"
			id="widget-<?php echo $widget->getID(); ?>"
			data-widget="<?php echo $widget->getName(); ?>"
			style="flex:1;"
		>

			<div class="widget-wrapper">
				<div class="widget-body">
					<?php echo $widget->display(); ?>
				</div>

				<div class="widget-error-box" style="display: none;">
					<?php echo JText::translate('VRE_AJAX_GENERIC_ERROR'); ?>
				</div>
			</div>

		</div>

	</div>

</div>
