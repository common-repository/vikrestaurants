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

JHtml::fetch('vrehtml.assets.select2');

$closure = $this->closure;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewRoomclosure". The event method receives the
 * view instance as argument.
 * It is also possible to use "onDisplayViewRoomclosureSidebar"
 * to include any additional fieldsets within the right sidebar.
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView();
$sidebarForms = $this->onDisplayView('Sidebar');

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">
	
	<?php echo $vik->openCard(); ?>

		<!-- LEFT SIDE -->
	
		<div class="<?php echo ($sidebarForms ? 'span8' : 'span12'); ?> full-width">

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset($sidebarForms ? JText::translate('VRRESERVATIONSTATUSCLOSURE') : '');
					echo $this->loadTemplate('closure');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewRoomclosure","key":"closure","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Closure" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['closure']))
					{
						echo $forms['closure'];

						// unset details form to avoid displaying it twice
						unset($forms['closure']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewRoomclosure","type":"fieldset"} -->

			<?php foreach ($forms as $formName => $formHtml): ?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset(JText::translate($formName));
						echo $formHtml;
						echo $vik->closeFieldset();
						?>
					</div>
				</div>
			<?php endforeach; ?>

		</div>

		<!-- RIGHT SIDE -->
	
		<?php if ($sidebarForms): ?>
			<div class="span4 full-width">

				<?php foreach ($sidebarForms as $formName => $formHtml): ?>
					<div class="row-fluid">
						<div class="span12">
							<?php
							echo $vik->openFieldset(JText::translate($formName), 'form-vertical');
							echo $formHtml;
							echo $vik->closeFieldset();
							?>
						</div>
					</div>
				<?php endforeach; ?>

			</div>
		<?php endif; ?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewRoomclosureSidebar","type":"fieldset"} -->

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $closure->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<script>

	(function($) {
		'use strict';

		let validator;

		$(function() {
			validator = new VikFormValidator('#adminForm');

			Joomla.submitbutton = (task) => {
				if (task.indexOf('save') === -1 || validator.validate()) {
					Joomla.submitform(task, document.adminForm);
				}
			}
		});
	})(jQuery);

</script>
