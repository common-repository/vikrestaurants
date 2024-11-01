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

$category = $this->category;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewCouponcategory".
 *
 * @since 1.9
 */
$forms = $this->onDisplayView();

$detailsFieldset = $forms['category'] ?? '';
unset($forms['category']);

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- LEFT SIDE -->
	
		<div class="<?php echo ($forms ? 'span8' : 'span12'); ?> full-width">

			<!-- COUPON -->

			<div class="row-fluid">
				<div class="span12">
					<?php echo $vik->openFieldset(JText::translate('JDETAILS')); ?>

					<!-- NAME - Text -->

					<?php
					echo $this->formFactory->createField()
						->type('text')
						->name('name')
						->value($category->name)
						->required(true)
						->class('input-xxlarge input-large-text')
						->label(JText::translate('VRMANAGELANG2'));
					?>
						
					<!-- Define role to detect the supported hook -->
					<!-- {"rule":"customizer","event":"onDisplayViewCouponcategory","key":"category","type":"field"} -->

					<?php	
					/**
					 * Look for any additional fields to be pushed within
					 * the "Details" fieldset (left-side).
					 *
					 * @since 1.9
					 */
					if (isset($forms['category']))
					{
						echo $forms['category'];

						// unset details form to avoid displaying it twice
						unset($forms['category']);
					}
					?>

					<!-- DESCRIPTION - Editor -->

					<?php
					echo $this->formFactory->createField()
						->type('editor')
						->name('description')
						->value($category->description)
						->label(JText::translate('VRMANAGELANG3'));
					?>

					<?php echo $vik->closeFieldset(); ?>
				</div>
			</div>

		</div>

		<!-- RIGHT SIDE -->

		<?php if ($forms): ?>
			<div class="span4 full-width">

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewCouponcategory","type":"fieldset"} -->

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

			</div>
		<?php endif; ?>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id" value="<?php echo (int) $category->id; ?>" />
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