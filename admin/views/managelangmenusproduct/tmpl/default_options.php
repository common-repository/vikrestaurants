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

foreach ($this->variations as $var)
{
	?>
	<div class="row-fluid">

		<!-- TRANSLATION -->

		<div class="span6">
			<?php echo $vik->openFieldset(JText::translate('VRMANAGETKSTOCK2')); ?>

				<!-- NAME - Text -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
					<div class="input-append translation-hint">
						<input type="text" name="option_name[]" value="<?php echo $this->escape($var->lang_name); ?>" data-id="option-<?php echo $var->id; ?>" size="48" />

						<button type="button" class="btn"><i class="fas fa-globe-americas"></i></button>
					</div>
				<?php echo $vik->closeControl(); ?>

				<input type="hidden" name="option_lang_id[]" value="<?php echo $var->lang_id; ?>" />

			<?php echo $vik->closeFieldset(); ?>
		</div>

		<!-- ORIGINAL -->

		<div class="span6">
			<?php echo $vik->openFieldset(JText::translate('VRE_LANG_ORIGINAL')); ?>
			
				<!-- NAME - Text -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
					<input type="text" value="<?php echo $this->escape($var->name); ?>" data-link="option-<?php echo $var->id; ?>" size="48" readonly tabindex="-1" />
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

		<input type="hidden" name="option_id[]" value="<?php echo (int) $var->id; ?>" />

	</div>
	<?php
}
