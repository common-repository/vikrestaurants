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
JHtml::fetch('vrehtml.assets.fontawesome');

$table = $this->table;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewTable". The event method receives the
 * view instance as argument.
 *
 * @since 1.8
 */
$forms = $this->onDisplayView();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- LEFT SIDE -->
	
		<div class="span8 full-width">

			<!-- TABLE -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGERESERVATION5'));
					echo $this->loadTemplate('table');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTable","key":"table","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Table" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['table']))
					{
						echo $forms['table'];

						// unset details form to avoid displaying it twice
						unset($forms['table']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- CLUSTER -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGETABLE13'));
					echo $this->loadTemplate('cluster');
					?>

					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTable","key":"cluster","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed below
					 * the "Cluster" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['cluster']))
					{
						echo $forms['cluster'];

						// unset details form to avoid displaying it twice
						unset($forms['cluster']);
					}
					
					echo $vik->closeFieldset();
					?>
				</div>
			</div>

		</div>

		<!-- RIGHT SIDE -->
	
		<div class="span4 full-width">

			<!-- BOOKING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRE_BOOKING_FIELDSET'), 'form-vertical');
					echo $this->loadTemplate('booking');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTable","key":"booking","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Booking" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['booking']))
					{
						echo $forms['booking'];

						// unset details form to avoid displaying it twice
						unset($forms['booking']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- PUBLISHING -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('JGLOBAL_FIELDSET_PUBLISHING'), 'form-vertical');
					echo $this->loadTemplate('publishing');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewTable","key":"publishing","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Publishing" fieldset (right-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['publishing']))
					{
						echo $forms['publishing'];

						// unset details form to avoid displaying it twice
						unset($forms['publishing']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewTable","type":"fieldset"} -->

			<?php
			// iterate forms to be displayed within the sidebar panel
			foreach ($forms as $formName => $formHtml)
			{
				$title = JText::translate($formName);
				?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset($title, 'form-vertical');
						echo $formHtml;
						echo $vik->closeFieldset();
						?>
					</div>
				</div>
				<?php
			}
			?>

			<!-- QR CODE -->

			<?php if (VREFactory::getConfig()->getBool('orderfood')): ?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset(JText::translate('VRE_QRCODE_FIELDSET'), 'form-vertical');

						if ($this->table->id)
						{
							echo $this->loadTemplate('qrcode');
						}
						else
						{
							echo $vik->alert(JText::translate('VRQRCODE_TABLE_NEW'), 'warning');
						}
						?>
							
						<!-- Define role to detect the supported hook -->
						<!-- {"rule":"customizer","event":"onDisplayViewTable","key":"qrcode","type":"field"} -->

						<?php	
						/**
						 * Look for any additional fields to be pushed within
						 * the "QR Code" fieldset (right-side).
						 *
						 * @since 1.9
						 */
						if (isset($forms['qrcode']))
						{
							echo $forms['qrcode'];

							// unset details form to avoid displaying it twice
							unset($forms['qrcode']);
						}

						echo $vik->closeFieldset();
						?>
					</div>
				</div>
			<?php endif; ?>

		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $table->id; ?>" />
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
