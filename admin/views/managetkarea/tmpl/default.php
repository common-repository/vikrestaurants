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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('vrehtml.assets.fontawesome');
JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.googlemaps');

$vik = VREApplication::getInstance();

// always use default tab while creating a new record
$active_tab = $this->area->id ? $this->getActiveTab('tkarea_details', $this->area->id) : 'tkarea_details';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTkarea". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$this->forms = $this->onDisplayView();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<?php echo $vik->bootStartTabSet('tkarea', ['active' => $active_tab, 'cookie' => $this->getCookieTab($this->area->id)->name]); ?>

		<!-- AREA -->
			
		<?php
		echo $vik->bootAddTab('tkarea', 'tkarea_details', JText::translate('JDETAILS'));
		echo $this->loadTemplate('details');
		echo $vik->bootEndTab();
		?>

		<!-- PARAMETERS -->

		<?php
		echo $vik->bootAddTab('tkarea', 'tkarea_params', JText::translate('VRMANAGEPAYMENT8'));
		echo $this->loadTemplate('params');
		echo $vik->bootEndTab();
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkarea","type":"tab"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the nav bar as custom sections.
		 *
		 * @since 1.9
		 */
		foreach ($this->forms as $formName => $formHtml)
		{
			$title = JText::translate($formName);

			// fetch form key
			$key = strtolower(preg_replace("/[^a-zA-Z0-9_]/", '', $title));

			if (!preg_match("/^tkarea_/", $key))
			{
				// keep same notation for fieldset IDs
				$key = 'tkarea_' . $key;
			}

			echo $vik->bootAddTab('tkarea', $key, $title);
			echo $formHtml;
			echo $vik->bootEndTab();
		}
		?>

	<?php echo $vik->bootEndTabSet(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $this->area->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
	
</form>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			w.validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || w.validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery, window);
</script>