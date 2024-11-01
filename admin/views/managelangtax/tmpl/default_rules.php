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

foreach ($this->rules as $rule)
{
	?>
	<div class="row-fluid">

		<!-- TRANSLATION -->

		<div class="span6">
			<?php echo $vik->openFieldset(JText::translate('VRMANAGECUSTOMF11')); ?>

				<!-- NAME - Text -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
					<input type="text" name="rule_name[]" value="<?php echo $this->escape($rule->lang_name); ?>" size="48" />
				<?php echo $vik->closeControl(); ?>

				<!-- BREAKDOWN -->

				<?php
				echo $vik->openControl(JText::translate('VRETAXBREAKDOWN'));

				foreach ($rule->breakdown as $bd)
				{
					$lang_bd = isset($rule->lang_breakdown[$bd->id]) ? $rule->lang_breakdown[$bd->id] : '';
					?>
					<div style="margin-bottom: 10px;">
						<input type="text" name="rule_breakdown[<?php echo $rule->id; ?>][<?php echo $bd->id; ?>]" value="<?php echo $this->escape($lang_bd); ?>" size="48" />
					</div>
					<?php
				}

				echo $vik->closeControl();
				?>

				<input type="hidden" name="rule_lang_id[]" value="<?php echo $rule->lang_id; ?>" />

			<?php echo $vik->closeFieldset(); ?>
		</div>

		<!-- ORIGINAL -->

		<div class="span6">
			<?php echo $vik->openFieldset(JText::translate('VRE_LANG_ORIGINAL')); ?>
			
				<!-- NAME - Text -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
					<input type="text" value="<?php echo $this->escape($rule->name); ?>" size="48" readonly tabindex="-1" />
				<?php echo $vik->closeControl(); ?>

				<!-- BREAKDOWN -->

				<?php
				echo $vik->openControl(JText::translate('VRETAXBREAKDOWN'));

				foreach ($rule->breakdown as $bd)
				{
					?>
					<div style="margin-bottom: 10px;">
						<input type="text" value="<?php echo $this->escape($bd->name); ?>" size="48" readonly tabindex="-1" />
					</div>
					<?php
				}

				echo $vik->closeControl();
				?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

		<input type="hidden" name="rule_id[]" value="<?php echo (int) $rule->id; ?>" />

	</div>
	<?php
}
