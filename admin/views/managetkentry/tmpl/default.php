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
$active_tab = $this->entry->id ? $this->getActiveTab('tkentry_details', $this->entry->id) : 'tkentry_details';

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTkentry". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$this->forms = $this->onDisplayView();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	
	<?php echo $vik->bootStartTabSet('tkentry', ['active' => $active_tab, 'cookie' => $this->getCookieTab($this->entry->id)->name]); ?>

		<!-- ENTRY -->
			
		<?php
		echo $vik->bootAddTab('tkentry', 'tkentry_details', JText::translate('JDETAILS'));
		echo $this->loadTemplate('details');
		echo $vik->bootEndTab();
		?>

		<!-- VARIATIONS -->

		<?php
		echo $vik->bootAddTab('tkentry', 'tkentry_variations', JText::translate('VRMANAGETKENTRYFIELDSET2'), ['badge' => count($this->entry->options)]);
		echo $vik->alert(JText::translate('VRE_EDIT_SORT_DRAG_DROP'), 'info');
		echo $this->loadTemplate('variations');
		echo $vik->bootEndTab();
		?>

		<!-- TOPPINGS -->

		<?php
		echo $vik->bootAddTab('tkentry', 'tkentry_toppings', JText::translate('VRMANAGETKENTRYFIELDSET3'), ['badge' => count($this->entry->groups)]);
		echo $vik->alert(JText::translate('VRE_EDIT_SORT_DRAG_DROP'), 'info');
		echo $this->loadTemplate('toppings');
		echo $vik->bootEndTab();
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewTkentry","type":"tab"} -->

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

			if (!preg_match("/^tkentry_/", $key))
			{
				// keep same notation for fieldset IDs
				$key = 'tkentry_' . $key;
			}

			echo $vik->bootAddTab('tkentry', $key, $title);
			echo $formHtml;
			echo $vik->bootEndTab();
		}
		?>

	<?php echo $vik->bootEndTabSet(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $this->entry->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
	
</form>

<?php
$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage entry variations
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'tkentry-var-inspector',
	array(
		'title'       => JText::translate('VRE_ADD_VARIATION'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
	),
	$this->loadTemplate('variations_modal')
);

$footer  = '<button type="button" class="btn btn-success" data-role="save">' . JText::translate('JAPPLY') . '</button>';
$footer .= '<button type="button" class="btn btn-danger" data-role="delete" style="float:right;">' . JText::translate('VRDELETE') . '</button>';

// render inspector to manage entry toppings groups
echo JHtml::fetch(
	'vrehtml.inspector.render',
	'tkentry-group-inspector',
	array(
		'title'       => JText::translate('VRE_ADD_TOPPING_GROUP'),
		'closeButton' => true,
		'keyboard'    => false,
		'footer'      => $footer,
		'width'       => 600,
	),
	$this->loadTemplate('toppings_modal')
);
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
