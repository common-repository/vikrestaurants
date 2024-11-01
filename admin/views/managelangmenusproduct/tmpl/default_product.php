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

$deflang = VikRestaurants::getDefaultLanguage();

$editor = $vik->getEditor();

?>

<div class="row-fluid">

	<!-- TRANSLATION -->

	<div class="span6">

		<!-- DETAILS -->

		<div class="row-fluid">
			<div class="span12">
				<?php echo $vik->openFieldset(JText::translate('VRMANAGETKSTOCK1')); ?>
				
					<!-- LANGUAGE - Dropdown -->

					<?php
					$elements = JHtml::fetch('contentlanguage.existing');
					
					echo $vik->openControl(JText::translate('VRMANAGELANG4')); ?>
						<select name="tag" id="vre-lang-sel">
							<?php echo JHtml::fetch('select.options', $elements, 'value', 'text', $this->translation->tag); ?>
						</select>
					<?php echo $vik->closeControl(); ?>
					
					<!-- NAME - Text -->

					<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
						<input type="text" name="name" value="<?php echo $this->escape($this->translation->name); ?>" size="48" />
					<?php echo $vik->closeControl(); ?>
				
					<input type="hidden" name="id" value="<?php echo (int) $this->translation->id; ?>" />
					
				<?php echo $vik->closeFieldset(); ?>
			</div>
		</div>

		<!-- DESCRIPTION -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGELANG3'));
				echo $editor->display('description', $this->translation->description, '100%', 550, 70, 20);
				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

	<!-- ORIGINAL -->

	<div class="span6">
		<?php echo $vik->openFieldset(JText::translate('VRE_LANG_ORIGINAL')); ?>
		
			<!-- LANGUAGE - HTML -->

			<?php
			echo $vik->openControl(JText::translate('VRMANAGELANG4'));
			echo JHtml::fetch('vrehtml.site.flag', $deflang);
			echo $vik->closeControl();
			?>
			
			<!-- NAME - Text -->

			<?php echo $vik->openControl(JText::translate('VRMANAGELANG2')); ?>
				<input type="text" value="<?php echo $this->escape($this->product->name); ?>" size="48" readonly tabindex="-1" />
			<?php echo $vik->closeControl(); ?>

			<!-- DESCRIPTION - Textarea -->

			<?php echo $vik->openControl(JText::translate('VRMANAGELANG3')); ?>
				<textarea class="full-width" style="height:300px;resize:vertical;" readonly tabindex="-1"><?php echo $this->product->description; ?></textarea>
			<?php echo $vik->closeControl(); ?>
			
		<?php echo $vik->closeFieldset(); ?>
	</div>

</div>
