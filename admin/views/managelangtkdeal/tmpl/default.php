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
JHtml::fetch('vrehtml.scripts.selectflags', '#vre-lang-sel');

$vik = VREApplication::getInstance();

$deflang = VikRestaurants::getDefaultLanguage();

$editor = $vik->getEditor();

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- TRANSLATION -->

		<div class="span6">

			<!-- DETAILS -->

			<div class="row-fluid">
				<div class="span12">
					<?php echo $vik->openFieldset(JText::translate('JDETAILS')); ?>
					
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
					<input type="text" value="<?php echo $this->escape($this->deal->name); ?>" size="48" readonly tabindex="-1" />
				<?php echo $vik->closeControl(); ?>
				
				<!-- DESCRIPTION - Textarea -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG3')); ?>
					<textarea class="full-width" style="height:300px;resize:vertical;" readonly tabindex="-1"><?php echo $this->deal->description; ?></textarea>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id_deal" value="<?php echo $this->deal->id; ?>" />	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
JText::script('VRE_SAVE_TRX_DEF_LANG');
?>

<script>

	(function($) {
		'use strict';

		$(function() {
			Joomla.submitbutton = (task) => {
				let selectedLanguage = $('#vre-lang-sel').val();

				if (task.indexOf('save') !== -1 && selectedLanguage == '<?php echo $deflang; ?>') {
					// saving translation with default language, ask for confirmation
					let r = confirm(Joomla.JText._('VRE_SAVE_TRX_DEF_LANG').replace(/%s/, selectedLanguage));

					if (!r) {
						return false;
					}
				}

				Joomla.submitform(task, document.adminForm);
			}
		});
	})(jQuery);

</script>
