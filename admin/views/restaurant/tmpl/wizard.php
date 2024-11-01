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

JHtml::fetch('formbehavior.chosen');

$vik = VREApplication::getInstance();

$vik->addScript(VREASSETS_ADMIN_URI . 'js/wizard.js');
$vik->addStyleSheet(VREASSETS_ADMIN_URI . 'css/wizard.css');
$vik->addStyleSheet(VREASSETS_ADMIN_URI . 'css/percentage-circle.css');

$layout = new JLayoutFile('wizard.step');

// calculate overall progress
$progress = $this->wizard->getProgress();

?>

<form action="index.php?option=com_vikrestaurants" method="post" name="adminForm" id="adminForm">

	<!-- Wizard -->
	<div class="vre-wizard" id="vre-wizard">

		<!-- Wizard toolbar -->
		<div class="vre-wizard-toolbar">

			<!-- Wizard progress -->
			<div class="vre-wizard-progress" id="vre-wizard-progress" style="margin-bottom: 0;">

			</div>

			<!-- Wizard description -->
			<div class="vre-wizard-text">
				<?php echo $vik->alert(JText::translate('VRWIZARDWHAT'), 'info', false, array('style' => 'margin: 0')); ?>
			</div>

		</div>

		<!-- Wizard steps container -->
		<div class="vre-wizard-steps">

			<?php
			// scan all the supported/active steps
			foreach ($this->wizard as $step)
			{
				?>
				<!-- Wizard step -->
				<div class="wizard-step-outer" data-id="<?php echo $step->getID(); ?>" style="<?php echo $step->isVisible() ? '' : 'display:none;'; ?>">
					<?php
					// display the step by using an apposite layout
					echo $layout->render(array('step' => $step));
					?>
				</div>
				<?php
			}
			?>

		</div>

	</div>
	
	<input type="hidden" name="view" value="restaurant" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_vikrestaurants" />
</form>

<?php
JText::script('VRWIZARDBTNDONE_DESC');
JText::script('VRSYSTEMCONNECTIONERR');
?>

<script>
	(function($, w) {
		'use strict';

		w.vreDismissWizard = () => {
			UIAjax.do('<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=wizard.done'); ?>');
		}

		$(function() {
			// delegate click event to all buttons with a specific role
			$('#vre-wizard').on('click', '[data-role]', function(event) {
				// executes wizard step according to the button role
				VREWizard.execute(this).then((data) => {
					// update progress
					$('#vre-wizard-progress').percentageCircle('progress', data.progress);

					if (data.progress == 100) {
						// auto-dismiss wizard on completion
						vreDismissWizard();
					}
				}).catch((error) => {
					if (error === false) {
						// suppress error
						return false;
					}

					if (!error) {
						// use default connection lost error
						error = Joomla.JText._('VRSYSTEMCONNECTIONERR');
					}

					// use default system alert to display error
					alert(error);
				});
			});

			// render progress circle
			$('#vre-wizard-progress').percentageCircle({
				progress: <?php echo $progress; ?>,
				size: 'small',
				color: '<?php echo $progress == 100 ? 'green' : null; ?>',
			});

			// set green color on complete
			$('#vre-wizard-progress').on('complete', function() {
				$(this).percentageCircle('color', 'green');
			});

			Joomla.submitbutton = function(task) {
				if (task == 'wizard.done') {
					// ask for a confirmation
					if (!confirm(Joomla.JText._('VRWIZARDBTNDONE_DESC'))) {
						return false;
					}
				}

				// submit form
				Joomla.submitform(task, document.adminForm);
			}
		});

		<?php if ($progress == 100): ?>
			// wizard completed send AJAX request to dismiss the wizard
			vreDismissWizard();
		<?php endif; ?>
	})(jQuery, window);
</script>