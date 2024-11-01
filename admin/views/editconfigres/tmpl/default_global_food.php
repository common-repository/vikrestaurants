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

$params = $this->params;

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigresGlobalFood". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalFood');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- CHOOSABLE MENUS - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'choosemenu',
			'checked'     => $params['choosemenu'],
			'label'       => JText::translate('VRMANAGECONFIG39'),
			'description' => JText::translate('VRMANAGECONFIG39_DESC'),
		]);
		?>

		<!-- DISHES ORDERING - Select -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'select',
			'name'        => 'orderfood',
			'value'       => $params['orderfood'],
			'class'       => 'medium',
			'label'       => JText::translate('VRMANAGECONFIG94'),
			'description' => JText::translate('VRMANAGECONFIG94_HELP'),
			'options'     => [
				JHtml::fetch('select.option', 0, JText::translate('VRTKCONFIGOVERLAYOPT0')),
				JHtml::fetch('select.option', 1, JText::translate('VROPTIONATREST')),
				JHtml::fetch('select.option', 2, JText::translate('VRTKCONFIGOVERLAYOPT2')),
			],
		]);
		?>

		<!-- EDIT FOOD - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'editfood',
			'checked'     => $params['editfood'],
			'label'       => JText::translate('VRMANAGECONFIG95'),
			'description' => JText::translate('VRMANAGECONFIG95_HELP'),
			'control'     => [
				'class' => 'order-food-child',
				'style' => $params['orderfood'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- SERVING NUMBER - Checkbox -->

		<?php
		echo $this->formFactory->createField([
			'type'        => 'checkbox',
			'name'        => 'servingnumber',
			'checked'     => $params['servingnumber'],
			'label'       => JText::translate('VRMANAGECONFIG98'),
			'description' => JText::translate('VRMANAGECONFIG98_HELP'),
			'control'     => [
				'class' => 'order-food-child',
				'style' => $params['orderfood'] ? '' : 'display: none;',
			],
		]);
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalFood","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Food > Details fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['basic']))
		{
			echo $forms['basic'];

			// unset details form to avoid displaying it twice
			unset($forms['basic']);
		}
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalFood","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Food tab.
 *
 * @since 1.9
 */
foreach ($forms as $formTitle => $formHtml)
{
	?>
	<div class="config-fieldset">
		
		<div class="config-fieldset-head">
			<h3><?php echo JText::translate($formTitle); ?></h3>
		</div>

		<div class="config-fieldset-body">
			<?php echo $formHtml; ?>
		</div>
		
	</div>
	<?php
}
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('select[name="orderfood"]').on('change', function() {
				if ($(this).val() != 0) {
					$('.order-food-child').show();
				} else {
					$('.order-food-child').hide();
				}
			});
		})
	})(jQuery);
</script>