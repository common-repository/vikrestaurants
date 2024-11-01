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

// get services configuration
$is_delivery = $step->isDelivery();
$is_pickup   = $step->isPickup();

if (!$step->isCompleted())
{
	?>
	<div class="wizard-form">

		<!-- DELIVERY - Checkbox -->

		<?php
		$yes = $vik->initRadioElement('', '', $is_delivery );
		$no  = $vik->initRadioElement('', '', !$is_delivery);

		echo $vik->openControl(JText::translate('VRTKORDERDELIVERYOPTION'));
		echo $vik->radioYesNo("wizard[{$id}][delivery]", $yes, $no);	
		echo $vik->closeControl();
		?>

		<!-- PICKUP - Checkbox -->

		<?php
		$yes = $vik->initRadioElement('', '', $is_pickup);
		$no  = $vik->initRadioElement('', '', !$is_pickup);

		echo $vik->openControl(JText::translate('VRTKORDERPICKUPOPTION'));
		echo $vik->radioYesNo("wizard[{$id}][pickup]", $yes, $no);
		echo $vik->closeControl();
		?>

	</div>

	<?php
	JText::script('VRE_WIZARD_STEP_TKSERVICES_WARN');
	?>

	<script>

		VREWizard.addPreflight('<?php echo $id; ?>', function(role, step) {
			if (role != 'process') {
				return true;
			}

			// make sure at least one option has been selected
			var is = jQuery('input[name="wizard[<?php echo $id; ?>][delivery]"]').is(':checked')
				|| jQuery('input[name="wizard[<?php echo $id; ?>][pickup]"]').is(':checked');

			if (!is) {
				// raise warning
				alert(Joomla.JText._('VRE_WIZARD_STEP_TKSERVICES_WARN'));

				// prevent request
				return false;
			}

			return true;
		});

	</script>
	<?php
}
else
{
	?>
	<ul class="wizard-step-summary">
		<li>
			<i class="fas fa-<?php echo $is_delivery ? 'check-circle ok' : 'dot-circle no'; ?> medium"></i>
			<b><?php echo JText::translate('VRTKORDERDELIVERYOPTION'); ?></b>
		</li>
		<li>
			<i class="fas fa-<?php echo $is_pickup ? 'check-circle ok' : 'dot-circle no'; ?> medium"></i>
			<b><?php echo JText::translate('VRTKORDERPICKUPOPTION'); ?></b>
		</li>
	</ul>
	<?php
}
?>
