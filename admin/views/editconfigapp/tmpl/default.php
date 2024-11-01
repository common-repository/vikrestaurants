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
JHtml::fetch('vrehtml.assets.select2');
JHtml::fetch('vrehtml.assets.toast', 'bottom-right');

$params = $this->params;

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigapp". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView();

// recover selected tab from the browser cookie
$this->selectedTab = JFactory::getApplication()->input->cookie->get('vikrestaurants_configapp_tab', 1, 'uint');

$tabs = $custTabs = [];

// build default tabs: API, SMS, Customizer, Backup
$tabs[] = JText::translate('VRCONFIGFIELDSETAPIFR');
$tabs[] = JText::translate('VRECONFIGTABNAME4');
$tabs[] = JText::translate('VRECONFIGTABCUSTOMIZER');
$tabs[] = JText::translate('VRECONFIGTABBACKUP');
$tabs[] = JText::translate('VRECONFIGTABCODEHUB');

/**
 * Iterate all form items to be displayed as custom tabs within the nav bar.
 *
 * @since 1.9
 */
foreach ($forms as $tabName => $tabForms)
{
	// include tab
	$custTabs[] = JText::translate($tabName);
}

// make sure the selected tab is still available
if ($this->selectedTab > count($tabs) + count($custTabs))
{
	// reset to first tab
	$this->selectedTab = 1;
}
?>

<div class="configuration-panel">

	<div id="configuration-navbar">
		<ul>
			<?php
			foreach (array_merge($tabs, $custTabs) as $i => $tab)
			{
				$key = $i + 1;
				?>
				<li id="vretabli<?php echo $key; ?>" class="vretabli<?php echo ($this->selectedTab == $key ? ' vreconfigtabactive' : ''); ?>" data-id="<?php echo $key; ?>">
					<a href="javascript: void(0);"><?php echo $tab; ?></a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>

	<div id="configuration-body">

		<form name="adminForm" id="adminForm" action="index.php" method="post" enctype="multipart/form-data">
			
			<?php
			// display default tab panes
			echo $this->loadTemplate('api');
			echo $this->loadTemplate('sms');
			echo $this->loadTemplate('customizer');
			echo $this->loadTemplate('backup');
			echo $this->loadTemplate('codehub');
			
			$i = 0;

			/**
			 * Iterate all form items to be displayed as new panels of custom tabs.
			 *
			 * @since 1.9
			 */
			foreach ($forms as $formName => $formHtml)
			{
				// sanitize form name
				$key = count($tabs) + (++$i);

				?>
				<div id="vretabview<?php echo $key; ?>" class="vretabview" style="<?php echo ($this->selectedTab != $key ? 'display: none;' : ''); ?>">
					<?php echo $formHtml; ?>
				</div>
				<?php
			}
			?>

			<?php echo JHtml::fetch('form.token'); ?>
			
			<input type="hidden" name="option" value="com_vikrestaurants" />
			<input type="hidden" name="task" value=""/>
		</form>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigapp","type":"tab"} -->

<!-- SCRIPT -->

<?php
echo JLayoutHelper::render('configuration.script', ['suffix' => 'app']);
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			$('select.short').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 100,
			});

			$('select.small-medium').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 150,
			});

			$('select.medium').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 200,
			});

			$('select.medium-large').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 250,
			});

			$('select.large').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 300,
			});

			if (!w.configObserver) {
				// create form observer only if none else already created it
				w.configObserver = new VikFormObserver('#adminForm');
			}

			// freeze form on page load
			w.configObserver.freeze();

			$(w).on('beforeunload', (event) => {
				// attach the message to the event property
				if (w.configObserver.isChanged()) {
					// The translated message is meant to work only
					// for internal purposes as almost all the browsers 
					// uses their own localised strings.
					let dialogText = 'Do you want to leave the page? Your changes will be lost if you don\'t save them.';

					event.returnValue = dialogText;

					// return the message to trigger the browser prompt
					return dialogText;
				}
			});

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') !== -1) {
					// ignore changes on save
					w.configObserver.unchanged();
				}

				Joomla.submitform(task, document.adminForm);
			}
		});
	})(jQuery, window);	
</script>