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

$apiKey = VREFactory::getConfig()->get('googleapikey');

JHtml::fetch('vrehtml.assets.googlemaps', $apiKey, 'places');

$origin = $this->origin;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewOrigin". The event method receives the
 * view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">
	
	<?php echo $vik->openCard(); ?>

		<!-- LEFT -->

		<div class="span7 full-width">

			<!-- ORIGIN -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMAPDETAILSBUTTON'));
					echo $this->loadTemplate('details');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewOrigin","key":"origin","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Origin" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['origin']))
					{
						echo $forms['origin'];

						// unset details form to avoid displaying it twice
						unset($forms['origin']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>

			<!-- DESCRIPTION -->

			<div class="row-fluid">
				<div class="span12">
					<?php
					echo $vik->openFieldset(JText::translate('VRMANAGELANG3'));
					echo $this->formFactory->createField()
						->type('textarea')
						->name('description')
						->value($origin->description)
						->hiddenLabel(true)
						->description(JText::translate('VRE_ORIGIN_DESCRIPTION_SCOPE'))
						->height(180)
						->style('resize: vertical;');
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewOrigin","key":"description","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Description" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['description']))
					{
						echo $forms['description'];

						// unset details form to avoid displaying it twice
						unset($forms['description']);
					}

					echo $vik->closeFieldset();
					?>
				</div>
			</div>
			
		</div>

		<!-- RIGHT -->

		<div class="span5 full-width">

			<!-- MAP -->

			<?php if ($apiKey): ?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset(JText::translate('VRTKAREAFIELDSET4'));
						echo $this->loadTemplate('map');
						?>

						<!-- Define role to detect the supported hook -->
						<!-- {"rule":"customizer","event":"onDisplayViewOrigin","key":"map","type":"field"} -->

						<?php	
						/**
						 * Look for any additional fields to be pushed within
						 * the "Map" fieldset (left-side).
						 *
						 * @since 1.9
						 */
						if (isset($forms['map']))
						{
							echo $forms['map'];

							// unset details form to avoid displaying it twice
							unset($forms['map']);
						}

						echo $vik->closeFieldset();
						?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Define role to detect the supported hook -->
			<!-- {"rule":"customizer","event":"onDisplayViewOrigin","type":"fieldset"} -->

			<?php
			// iterate forms to be displayed within the sidebar panel
			foreach ($forms as $formName => $formHtml)
			{
				$title = JText::translate($formName);
				?>
				<div class="row-fluid">
					<div class="span12">
						<?php
						echo $vik->openFieldset($title);
						echo $formHtml;
						echo $vik->closeFieldset();
						?>
					</div>
				</div>
				<?php
			}
			?>

		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $origin->id; ?>" />
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