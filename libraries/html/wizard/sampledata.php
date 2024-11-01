<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.wizard
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Layout variables
 * -----------------
 * @var  VREWizardStep  $step  The wizard step instance.
 */
extract($displayData);

if ($step->isCompleted())
{
	return;
}

$id = $step->getID();

// load supported sample data packages
$sampledata = $step->getSampleData();

$vik = VREApplication::getInstance();

if ($sampledata)
{
	JText::script('VRSAVE');

	?>
	<p>
		<?php _e('Select one of the available sample data and proceed with the installation.', 'vikrestaurants'); ?>
	</p>

	<div class="wizard-form">

		<?php echo $vik->openControl(__('Sample Data')); ?>
			<select name="wizard[<?php echo $id; ?>][sampledata]" class="required">
				<?php
				$options = array();
				$options[] = JHtml::fetch('select.option', '', JText::translate('JGLOBAL_SELECT_AN_OPTION'));

				foreach ($sampledata as $sd)
				{
					$options[] = JHtml::fetch('select.option', $sd->id, $sd->title);
				}

				echo JHtml::fetch('select.options', $options);
				?>
			</select>
		<?php echo $vik->closeControl(); ?>

	</div>

	<script>
		jQuery(document).ready(function() {
			VikRenderer.chosen('[data-id="<?php echo $id; ?>"] select');
		});

		VREWizard.addPreflight('<?php echo $id; ?>', function(role, step) {
			if (role != 'process') {
				return true;
			}

			// create form validator
			var validator = new VikFormValidator(step);

			// validate form
			if (!validator.validate()) {
				// prevent request
				return false;
			}

			// change text button to inform the user that the step is processing the request
			jQuery(step).find('button[data-role="process"]').text(wp.i18n.__('Saving'));

			// retrieve form data
			let data = VREWizard.getFormData('<?php echo $id; ?>', true);

			// inject argument to reload all steps
			data.push({name: 'reload_all', value: true});

			return jQuery.param(data);
		});

		VREWizard.addPostflight('<?php echo $id; ?>', function(role, step) {
			// restore text button on both success and failure
			jQuery(step).find('button[data-role="process"]').text(Joomla.JText._('VRSAVE'));
		});
	</script>
	<?php

	echo $vik->alert(__('Notice that the installation of the sample data might restore the database to the factory settings. So, if you already created some records, you might lose them.', 'vikrestaurants'));
}
else
{
	// no available sample data
	echo $vik->alert(__('There are no sample data available for your version. Please try to update the plugin to the latest version.', 'vikrestaurants'), 'error');
	?>
	<script>
		jQuery(document).ready(function() {
			// disable save button
			jQuery('[data-id="<?php echo $id; ?>"] button[data-role="process"]').hide();
		});
	</script>
	<?php
}
