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

$vik = VREApplication::getInstance();

$editor = $vik->getEditor();

foreach ($this->groups as $group)
{
	?>
	<div class="row-fluid">

		<!-- TRANSLATION -->

		<div class="span6">
			<?php echo $vik->openFieldset(JText::translate('VRTOPPING'), 'form-vertical'); ?>

				<!-- NAME - Text -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
					<div class="input-append translation-hint">
						<input type="text" name="group_name[]" value="<?php echo $this->escape($group->lang_name); ?>" data-id="group-<?php echo $group->id; ?>" size="48" />

						<button type="button" class="btn"><i class="fas fa-globe-americas"></i></button>
					</div>
				<?php echo $vik->closeControl(); ?>

				<!-- DESCRIPTION - Editor -->

				<?php
				echo $vik->openControl(JText::translate('VRMANAGELANG3'));
				echo $editor->display('group_description[' . $group->id . ']', $group->lang_description, '100%', 550, 70, 20);
				echo $vik->closeControl();
				?>

				<input type="hidden" name="group_lang_id[]" value="<?php echo (int) $group->lang_id; ?>" />

			<?php echo $vik->closeFieldset(); ?>
		</div>

		<!-- ORIGINAL -->

		<div class="span6">
			<?php echo $vik->openFieldset(JText::translate('VRE_LANG_ORIGINAL')); ?>
			
				<!-- NAME - Text -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
					<input type="text" value="<?php echo $this->escape($group->title); ?>" data-link="group-<?php echo $group->id; ?>" size="48" readonly tabindex="-1" />
				<?php echo $vik->closeControl(); ?>

				<!-- DESCRIPTION - Textarea -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG3')); ?>
					<textarea class="full-width" style="height:300px;resize:vertical;" readonly tabindex="-1"><?php echo $group->description; ?></textarea>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

		<input type="hidden" name="group_id[]" value="<?php echo (int) $group->id; ?>" />

	</div>
	<?php
}
