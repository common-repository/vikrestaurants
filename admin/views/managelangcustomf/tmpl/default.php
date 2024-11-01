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

						<?php
						echo $vik->openControl(JText::translate('VRMANAGELANG2'));

						if ($this->field->type != 'separator')
						{
							?>
							<input type="text" name="name" value="<?php echo $this->escape($this->translation->name); ?>" size="48" />
							<?php
						}
						else
						{
							?>
							<textarea name="name" class="full-width" style="height: 150px; resize: vertical;"><?php echo $this->translation->name; ?></textarea>
							<?php
						}

						echo $vik->closeControl();
						?>

						<!-- CHOOSE - Mixed -->

						<?php
						if ($this->field->type == 'select')
						{
							if ($this->field->choose)
							{
								$options = (array) json_decode($this->field->choose, true);
							}
							else
							{
								$options = [];
							}

							if (!empty($this->translation->choose))
							{
								$lang_options = (array) json_decode($this->translation->choose, true);
							}
							else
							{
								$lang_options = array();
							}

							echo $vik->openControl(JText::translate('VRCUSTOMFTYPEOPTION4'));

							foreach ($options as $k => $opt)
							{
								$lang_opt = !empty($lang_options[$k]) ? $lang_options[$k] : '';
								?>
								<div style="margin-bottom: 10px;">
									<input type="text" name="choose[<?php echo $k; ?>]" placeholder="<?php echo $this->escape($opt); ?>" value="<?php echo $this->escape($lang_opt); ?>" size="40" />
								</div>
								<?php
							}
							echo $vik->closeControl();
						}
						else if ($this->field->type == 'checkbox')
						{
							echo $vik->openControl(JText::translate('VRMANAGECUSTOMF5'));
							?>
								<input type="text" name="poplink" value="<?php echo $this->translation->poplink; ?>" size="48" />
							<?php
							echo $vik->closeControl();
						}
						else if ($this->field->type == 'separator')
						{
							echo $vik->openControl(JText::translate('VRSUFFIXCLASS'));
							?>
								<input type="text" name="choose" value="<?php echo $this->translation->choose; ?>" size="48" />
							<?php
							echo $vik->closeControl();
						}
						else if ($this->field->rule == 'phone')
						{
							echo $vik->openControl(JText::translate('VRMANAGECUSTOMF10'));

							$options = JHtml::fetch('vrehtml.admin.countries');
							?>
							<select name="choose" id="vr-countrylang-sel">
								<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $this->translation->choose ?: $this->field->choose); ?>
							</select>
							<?php
							echo $vik->closeControl();
						}
						?>
					
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
					<input type="text" value="<?php echo $this->escape(JText::translate($this->field->name)); ?>" size="48" readonly tabindex="-1" />
				<?php echo $vik->closeControl(); ?>

				<!-- CHOOSE - Mixed -->

				<?php
				if ($this->field->type == 'select')
				{
					if ($this->field->choose)
					{
						$options = (array) json_decode($this->field->choose, true);
					}
					else
					{
						$options = [];
					}

					echo $vik->openControl(JText::translate('VRCUSTOMFTYPEOPTION4'));

					foreach ($options as $opt)
					{
						?>
						<div style="margin-bottom: 10px;">
							<input type="text" value="<?php echo $this->escape($opt); ?>" size="48" readonly tabindex="-1" />
						</div>
						<?php
					}
					echo $vik->closeControl();
				}
				else if ($this->field->type == 'checkbox')
				{
					echo $vik->openControl(JText::translate('VRMANAGECUSTOMF5'));
					?>
						<input type="text" value="<?php echo $this->escape($this->field->poplink); ?>" size="48" readonly tabindex="-1" />
					<?php
					echo $vik->closeControl();
				}
				else if ($this->field->type == 'separator')
				{
					echo $vik->openControl(JText::translate('VRSUFFIXCLASS'));
					?>
						<input type="text" value="<?php echo $this->escape($this->field->choose); ?>" size="48" readonly tabindex="-1" />
					<?php
					echo $vik->closeControl();
				}
				else if ($this->field->rule == 'phone')
				{
					echo $vik->openControl(JText::translate('VRMANAGECUSTOMF10'));
					?>
						<input type="text" value="<?php echo $this->escape($this->field->choose); ?>" size="48" readonly tabindex="-1" />
					<?php
					echo $vik->closeControl();
				}
				?>
				
				<!-- DESCRIPTION - Textarea -->

				<?php echo $vik->openControl(JText::translate('VRMANAGELANG3')); ?>
					<textarea class="full-width" style="height:300px;resize:vertical;" readonly tabindex="-1"><?php echo $this->field->description; ?></textarea>
				<?php echo $vik->closeControl(); ?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id_customf" value="<?php echo $this->field->id; ?>" />	
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
			$('#vr-countrylang-sel').select2({
				allowClear: false,
				width: '90%',
			});

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
