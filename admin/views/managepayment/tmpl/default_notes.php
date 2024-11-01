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

$payment = $this->payment;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewPaymentNotes".
 * The event method receives the view instance as argument.
 *
 * @since 1.9
 */
$notesForms = $this->onDisplayView('Notes');

?>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewPaymentNotes","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed within
 * the Notes tab (at the beginning).
 *
 * @since 1.9
 */
foreach ($notesForms as $formName => $formHtml)
{
	$title = JText::translate($formName);
	?>
	<div class="row-fluid">
		<div class="span12">
			<?php
			echo $vik->openFieldset($title);
			echo $formHtml;
			echo $vik->closeFieldset();
			?>
		</div>
	</div>
	<?php
}
?>

<div class="row-fluid">

	<!-- LEFT SIDE -->

	<div class="span6">

		<!-- NOTES BEFORE PURCHASE -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGEPAYMENT11'));

				echo $this->formFactory->createField()
					->type('editor')
					->name('prenote')
					->value($payment->prenote)
					->hiddenLabel(true);
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewPayment","key":"prenote","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Notes before purchase" fieldset (left-side).
				 *
				 * NOTE: retrieved from "onDisplayViewPayment" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['prenote']))
				{
					echo $this->forms['prenote'];

					// unset details form to avoid displaying it twice
					unset($this->forms['prenote']);
				}

				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

	<!-- RIGHT SIDE -->

	<div class="span6">

		<!-- NOTES AFTER PURCHASE -->

		<div class="row-fluid">
			<div class="span12">
				<?php
				echo $vik->openFieldset(JText::translate('VRMANAGEPAYMENT7'));

				echo $this->formFactory->createField()
					->type('editor')
					->name('note')
					->value($payment->note)
					->hiddenLabel(true);
				?>

				<!-- Define role to detect the supported hook -->
				<!-- {"rule":"customizer","event":"onDisplayViewPayment","key":"note","type":"field"} -->
				
				<?php
				/**
				 * Look for any additional fields to be pushed within
				 * the "Notes after purchase" fieldset (right-side).
				 *
				 * NOTE: retrieved from "onDisplayViewPayment" hook.
				 *
				 * @since 1.8
				 */
				if (isset($this->forms['note']))
				{
					echo $this->forms['note'];

					// unset details form to avoid displaying it twice
					unset($this->forms['note']);
				}

				echo $vik->closeFieldset();
				?>
			</div>
		</div>

	</div>

</div>
