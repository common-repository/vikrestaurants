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

$vik = VREApplication::getInstance();

// always use default tab while creating a new record
$active_tab = $this->menu->id ? $this->getActiveTab('tkmenu_details', $this->menu->id) : 'tkmenu_details';

// Obtain media manager modal before displaying the inspector.
// In this way, we can display the modal outside the bootstrap panels.
$mediaManagerModal = JHtml::fetch('vrehtml.mediamanager.modal');

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTkmenu". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$this->forms = $this->onDisplayView();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<?php echo $vik->bootStartTabSet('tkmenu', ['active' => $active_tab, 'cookie' => $this->getCookieTab($this->menu->id)->name]); ?>

		<!-- MENU -->
			
		<?php
		echo $vik->bootAddTab('tkmenu', 'tkmenu_details', JText::translate('JDETAILS'));
		echo $this->loadTemplate('details');
		echo $vik->bootEndTab();
		?>

		<!-- ENTRIES -->

		<?php
		echo $vik->bootAddTab('tkmenu', 'tkmenu_entries', JText::translate('VRMENUMENUSPRODUCTS'), ['badge' => count($this->menu->products)]);
		echo $this->loadTemplate('entries');
		echo $vik->bootEndTab();
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkmenu","type":"tab"} -->

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

			if (!preg_match("/^tkmenu_/", $key))
			{
				// keep same notation for fieldset IDs
				$key = 'tkmenu_' . $key;
			}

			echo $vik->bootAddTab('tkmenu', $key, $title);
			echo $formHtml;
			echo $vik->bootEndTab();
		}
		?>

	<?php echo $vik->bootEndTabSet(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $this->menu->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
	
</form>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage menu entries
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'tkmenu-product-inspector',
	array(
		'title'       => JText::translate('VRE_ADD_PRODUCT'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 600,
	),
	$this->loadTemplate('entry_modal')
);

echo $mediaManagerModal;
?>

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
