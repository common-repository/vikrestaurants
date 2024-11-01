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

/**
 * Layout variables
 * -----------------
 * @var  WizardStep  $step  The wizard step instance.
 */
extract($displayData);

$vik = VREApplication::getInstance();

$id = $step->getID();

if (!$step->isCompleted())
{
	// get Google API Key configuration
	$apikey = $step->getGoogleAK();

	if (!$apikey)
	{
		echo $vik->alert(JText::translate('VRE_WIZARD_STEP_TKAREAS_WARN'));
		?>
		<div class="wizard-form">

			<!-- GOOGLE API KEY - Text -->

			<div class="controls">
				<input type="text" name="wizard[<?php echo $id; ?>][googleapikey]" value="" class="required form-control" placeholder="<?php echo $this->escape(JText::translate('VRMANAGECONFIG55')); ?>" />
			</div>

		</div>

		<script>

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

				return true;
			});

		</script>
		<?php
	}
}
else
{
	?>
	<ul class="wizard-step-summary">
		<?php
		$areas = $step->getAreas();

		// display at most 4 delivery areas
		for ($i = 0; $i < min(array(4, count($areas))); $i++)
		{
			?>
			<li>
				<i class="fas fa-<?php echo $areas[$i]->published ? 'check-circle ok' : 'dot-circle no'; ?> medium"></i>
				<b><?php echo $areas[$i]->name; ?></b>
			</li>
			<?php
		}

		// count remaining delivery areas
		$remaining = count($areas) - 5;

		if ($remaining > 0)
		{
			?>
			<li><?php echo JText::plural('VRWIZARDOTHER_N_ITEMS', $remaining); ?></li>
			<?php
		}
		?>
	</ul>
	<?php
}
?>
