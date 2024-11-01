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

?>

<form name="adminForm" action="index.php" method="post" id="adminForm">

	<?php echo $vik->openCard(); ?>

		<!-- TRANSLATION -->

		<div class="span6">

			<!-- DETAILS -->

			<div class="row-fluid">
				<div class="span12">
					<?php echo $vik->openFieldset(JText::translate('VRMANAGERESERVATION12')); ?>
					
						<!-- LANGUAGE - Dropdown -->

						<?php
						echo $this->formFactory->createField([
							'type'    => 'select',
							'name'    => 'tag',
							'value'   => $this->translation->tag,
							'label'   => JText::translate('VRMANAGELANG4'),
							'id'      => 'vre-lang-sel',
							'options' => JHtml::fetch('contentlanguage.existing'),
						]);
						?>
						
						<!-- NAME - Text -->

						<?php
						echo $this->formFactory->createField([
							'type'    => 'text',
							'name'    => 'name',
							'value'   => $this->translation->name,
							'label'   => JText::translate('VRMANAGELANG2'),
							'options' => JHtml::fetch('contentlanguage.existing'),
						]);
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

					echo $this->formFactory->createField([
						'type'  => 'editor',
						'name'  => 'description',
						'value' => $this->translation->description,
						// display only the field
						'hiddenLabel' => true,
					]);

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
				echo $this->formFactory->createField([
					'label' => JText::translate('VRMANAGELANG4'),
				])->render(function($data) use ($deflang) {
					return JHtml::fetch('vrehtml.site.flag', $deflang);
				});
				?>
				
				<!-- NAME - Text -->

				<?php
				echo $this->formFactory->createField([
					'type'     => 'text',
					'value'    => $this->status->name,
					'label'    => JText::translate('VRMANAGELANG2'),
					'readonly' => true,
					'tabindex' => -1,
				]);
				?>
				
				<!-- DESCRIPTION - Textarea -->

				<?php
				echo $this->formFactory->createField([
					'type'     => 'textarea',
					'value'    => $this->status->description,
					'label'    => JText::translate('VRMANAGELANG3'),
					'class'    => 'full-width',
					'readonly' => true,
					'tabindex' => -1,
					'height'   => 300,
					'style'    => 'resize: vertical;',
				]);
				?>
				
			<?php echo $vik->closeFieldset(); ?>
		</div>

	<?php echo $vik->closeCard(); ?>

	<?php echo JHtml::fetch('form.token'); ?>
	
	<input type="hidden" name="id_status_code" value="<?php echo $this->status->id; ?>" />	
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
