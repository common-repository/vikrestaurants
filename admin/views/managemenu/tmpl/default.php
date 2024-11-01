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
JHtml::fetch('vrehtml.assets.fontawesome');
JHtml::fetch('vrehtml.assets.fancybox');

$menu = $this->menu;

$vik = VREApplication::getInstance();

// always use default tab while creating a new record
$active_tab = $menu->id ? $this->getActiveTab('menu_details', $menu->id) : 'menu_details';

// Obtain media manager modal before displaying the first field.
// In this way, we can display the modal outside the bootstrap panels.
$mediaManagerModal = JHtml::fetch('vrehtml.mediamanager.modal');

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewMenu". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$this->forms = $this->onDisplayView();

?>

<form name="adminForm" id="adminForm" action="index.php" method="post">

	<?php echo $vik->bootStartTabSet('menu', array('active' => $active_tab, 'cookie' => $this->getCookieTab($menu->id)->name)); ?>

		<!-- DETAILS -->

		<?php
		echo $vik->bootAddTab('menu', 'menu_details', JText::translate('JDETAILS'));
		echo $this->loadTemplate('details');
		echo $vik->bootEndTab();
		?>

		<!-- SECTIONS -->

		<?php
		echo $vik->bootAddTab('menu', 'menu_sections', JText::translate('VRMANAGEMENU20'), ['badge' => count($menu->sections)]);
		echo $this->loadTemplate('sections');
		echo $vik->bootEndTab();
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewMenu","type":"tab"} -->

		<?php
		/**
		 * Iterate remaining forms to be displayed within
		 * the nav bar as custom sections.
		 *
		 * @since 1.8
		 */
		foreach ($this->forms as $formName => $formHtml)
		{
			$title = JText::translate($formName);

			// fetch form key
			$key = strtolower(preg_replace("/[^a-zA-Z0-9_]/", '', $title));

			if (!preg_match("/^menu_/", $key))
			{
				// keep same notation for fieldset IDs
				$key = 'menu_' . $key;
			}

			echo $vik->bootAddTab('menu', $key, $title);
			echo $formHtml;
			echo $vik->bootEndTab();
		}
		?>

	<?php echo $vik->bootEndTabSet(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $menu->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage menu sections
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'menu-section-inspector',
	array(
		'title'       => JText::translate('VRE_ADD_SECTION'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 600,
	),
	$this->loadTemplate('sections_modal')
);

// display products selection modal
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-products',
	array(
		'title'       => JText::translate('VRMANAGEMENU23'),
		'closeButton' => true,
		'keyboard'    => true, 
		'bodyHeight'  => 80,
		'footer'	  => '<button type="button" class="btn btn-success" id="save-section-products">' . JText::translate('JAPPLY') . '</button>',
	),
	$this->loadTemplate('sections_products_modal')
);

// display media manager modal
echo $mediaManagerModal;
?>

<script>
	(function($, w) {
		'use strict';

		w.vrOpenJModal = (id, url, jqmodal) => {
			<?php echo $vik->bootOpenModalJS(); ?>
		}

		w.vrCloseJModal = (id) => {
			<?php echo $vik->bootDismissModalJS(); ?>
		}

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