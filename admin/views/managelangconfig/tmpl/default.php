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

switch ($this->setting->param)
{
	case 'symbpos':
		$input   = 'select';
		$options = array(
			JHtml::fetch('select.option', '1', JText::translate('VRCONFIGSYMBPOSITION1')),
			JHtml::fetch('select.option', '2', JText::translate('VRCONFIGSYMBPOSITION2')),
		);
		break;

	case 'currdecimalsep':
	case 'currthousandssep':
		$input = 'textshort';
		break;

	case 'tknote':
		$input = 'editor';
		break;

	case 'smstmplcust':
	case 'smstmpltkcust':
		$input = 'textarea';
		break;

	default:
		$input = 'text';
}

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- TRANSLATION -->

		<div class="span6">

			<div class="row-fluid">

				<!-- DETAILS -->

				<div class="span12">
					<?php echo $vik->openFieldset(JText::translate('VRE_CONFIG_SETTING')); ?>
					
						<!-- LANGUAGE - Dropdown -->

						<?php
						$elements = JHtml::fetch('contentlanguage.existing');
						
						echo $vik->openControl(JText::translate('VRMANAGELANG4')); ?>
							<select name="tag" id="vre-lang-sel">
								<?php echo JHtml::fetch('select.options', $elements, 'value', 'text', $this->translation->tag); ?>
							</select>
						<?php echo $vik->closeControl(); ?>
						
						<!-- SETTING - Text -->

						<?php
						if ($input != 'editor')
						{
							echo $vik->openControl(JText::translate('VRE_CONFIG_SETTING'));
						}

						$setting = isset($this->translation->setting) ? $this->translation->setting : '';

						if ($input == 'editor')
						{
							echo $vik->getEditor()->display('setting', $setting, '100%', 550, 70, 20);
						}
						else if ($input == 'select')
						{
							?>
							<select name="setting" id="setting-dropdown">
								<?php echo JHtml::fetch('select.options', $options, 'value', 'text', $setting); ?>
							</select>
							<?php
						}
						else if ($input == 'textshort')
						{
							?>
							<input type="text" name="setting" value="<?php echo $this->escape($setting); ?>" size="12" />
							<?php
						}
						else if ($input == 'textarea')
						{
							?>
							<textarea name="setting" class="full-width" style="height: 300px; resize: vertical;"><?php echo $setting; ?></textarea>
							<?php
						}
						else
						{
							?>
							<input type="text" name="setting" value="<?php echo $this->escape($setting); ?>" size="48" />
							<?php
						}

						if ($input != 'editor')
						{
							echo $vik->closeControl();
						}
						?>
					
						<input type="hidden" name="id" value="<?php echo (int) $this->translation->id; ?>" />
						
					<?php echo $vik->closeFieldset(); ?>
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
				
				<!-- SETTING - Text -->

				<?php
				echo $vik->openControl(JText::translate('VRE_CONFIG_SETTING'));

				if ($input == 'select')
				{
					foreach ($options as $opt)
					{
						if ($opt->value == $this->setting->setting)
						{
							// overwrite setting
							$this->setting->setting = $opt->text;
							// let the default input is used to display the option
						}
					}
				}

				if ($input == 'editor' || $input == 'textarea')
				{
					?>
					<textarea class="full-width" style="height:300px;resize:vertical;" readonly tabindex="-1"><?php echo $this->setting->setting; ?></textarea>
					<?php
				}
				else if ($input == 'textshort')
				{
					?>
					<input type="text" value="<?php echo $this->escape($this->setting->setting); ?>" size="12" readonly tabindex="-1" />
					<?php
				}
				else
				{
					?>
					<input type="text" value="<?php echo $this->escape($this->setting->setting); ?>" size="48" readonly tabindex="-1" />
					<?php
				}

				echo $vik->closeControl();
				?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="param" value="<?php echo $this->escape($this->setting->param); ?>" />	
	<input type="hidden" name="return" value="<?php echo $this->escape($this->return); ?>" />	
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

			$('#setting-dropdown').select2({
				minimumResultsForSearch: -1,
				allowClear: false,
				width: 250,
			});
		});
	})(jQuery);
</script>