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

$params = $this->params;

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigappBackupSettings". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('BackupSettings');

?>

<div class="config-fieldset">

	<div class="config-fieldset-body">
		
		<!-- TYPE - Select -->

		<?php

		$options = [];

		foreach ($this->backupExportTypes as $type => $handler)
		{
			$options[] = JHtml::fetch('select.option', $type, $handler->getName());	
		}

		echo $this->formFactory->createField()
			->type('select')
			->name('backuptype')
			->value($params['backuptype'] ?? 'full')
			->class('medium-large')
			->label(JText::translate('VRE_BACKUP_CONFIG_TYPE_LABEL'))
			->description(JText::translate('VRE_BACKUP_CONFIG_TYPE_DESC'))
			->options($options);

		foreach ($this->backupExportTypes as $type => $handler)
		{
			echo $this->formFactory->createField()
				->type('alert')
				->style('info')
				->text($handler->getDescription())
				->control([
					'visible' => ($params['backuptype'] ?? 'full') == $type,
					'id'      => 'backup_export_type_' . $type,
				]);
		}
		?>

		<!-- FOLDER - Text -->

		<?php
		// get saved path
		$path = rtrim($params['backupfolder'] ?? '', DIRECTORY_SEPARATOR);

		// get system temporary path
		$tmp_path = rtrim(JFactory::getApplication()->get('tmp_path', ''), DIRECTORY_SEPARATOR);

		if (!$path)
		{
			$path = $tmp_path;
		}

		echo $this->formFactory->createField()
			->type('text')
			->name('backupfolder')
			->value($path)
			->label(JText::translate('VRE_BACKUP_CONFIG_FOLDER_LABEL'))
			->description(JText::translate('VRE_BACKUP_CONFIG_FOLDER_DESC'));

		// check whether the specified path is equals to the temporary path
		if ($path === $tmp_path)
		{
			// inform the administrator that it is not safe to use the temporary path to store the back-up of the system
			echo $this->formFactory->createField()
				->type('alert')
				->style('warning')
				->text(JText::sprintf('VRE_BACKUP_CONFIG_FOLDER_WARN', $tmp_path . DIRECTORY_SEPARATOR . VikRestaurants::generateSerialCode(8)));	
		}
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigappBackupSettings","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Backup > Settings > Details fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['basic']))
		{
			echo $forms['basic'];

			// unset details form to avoid displaying it twice
			unset($forms['basic']);
		}
		?>

		<!-- BACK-UP MANAGEMENT - Button -->

		<?php
		echo $this->formFactory->createField()
			->type('link')
			->href('index.php?option=com_vikrestaurants&view=backups')
			->text(JText::translate('VRE_BACKUP_LIST_BUTTON'))
			->id('backup-btn');
		?>

	</div>

</div>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigappBackupSettings","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Backup > Settings tab.
 *
 * @since 1.9
 */
foreach ($forms as $formTitle => $formHtml)
{
	?>
	<div class="config-fieldset">
		
		<div class="config-fieldset-head">
			<h3><?php echo JText::translate($formTitle); ?></h3>
		</div>

		<div class="config-fieldset-body">
			<?php echo $formHtml; ?>
		</div>
		
	</div>
	<?php
}

JText::script('VRE_CONFIRM_MESSAGE_UNSAVE');
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			// change description according to the selected backup type
			$('select[name="backuptype"]').on('change', function() {
				const type = $(this).val();

				$('#adminForm *[id^="backup_export_type_"]').hide();
				$('#backup_export_type_' + type).show();
			});

			$('#backup-btn').on('click', function(event) {
				if (!w.configObserver.isChanged()) {
					// nothing has changed, go ahead
					return true;
				}

				// ask for a confirmation
				if (!confirm(Joomla.JText._('VRE_CONFIRM_MESSAGE_UNSAVE'))) {
					// do not leave the page
					event.preventDefault();
					event.stopPropagation();
					return false;
				}
			});
		});
	})(jQuery, window);
</script>