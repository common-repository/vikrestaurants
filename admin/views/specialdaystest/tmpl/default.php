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

JHtml::fetch('bootstrap.tooltip', '.hasTooltip');
JHtml::fetch('formbehavior.chosen');
JHtml::fetch('vrehtml.assets.fontawesome');

$vik = VREApplication::getInstance();

?>

<div class="special-days-test" style="padding: 10px;">

	<form action="index.php" method="post" name="adminForm" id="adminForm">

		<!-- MAIN -->

		<div class="row-fluid">

			<!-- SEARCH -->

			<div class="span5 full-width">
				<?php echo $vik->openEmptyFieldset('use-margin-for-alignment'); ?>

					<!-- GROUP - Select -->
					<?php
					$groups = JHtml::fetch('vrehtml.admin.groups', array('restaurant', 'takeaway'));

					echo $vik->openControl(JText::translate('VRMANAGESPDAY16')); ?>
						<select name="group" id="vr-group-sel">
							<?php echo JHtml::fetch('select.options', $groups, 'value', 'text', $this->args['group'], true); ?>
						</select>
					<?php echo $vik->closeControl(); ?>

					<!-- DATE - Calendar -->
					<?php
					echo $vik->openControl(JText::translate('VRMANAGESPDAY2'));
					echo $vik->calendar($this->args['date'], 'date', 'vrdate');
					echo $vik->closeControl();
					?>

					<!-- SUBMIT - Button -->
					<?php echo $vik->openControl(''); ?>
						<button type="button" class="btn" id="sd-test-button"><?php echo JText::translate('VRTESTSPECIALDAYS'); ?></button>
					<?php echo $vik->closeControl(); ?>

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

			<!-- RESULT -->

			<div class="span7" id="sd-test-wrapper" style="display: none;">
				<?php echo $vik->openEmptyFieldset('sd-test-response'); ?>

					<!-- test response go here -->

				<?php echo $vik->closeEmptyFieldset(); ?>
			</div>

		</div>

	</form>

</div>

<?php
JText::script('VRSYSTEMCONNECTIONERR');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			const validator = new VikFormValidator('#adminForm');

			// render select with chosen
			VikRenderer.chosen('#adminForm');

			// register submit click event
			$('#sd-test-button').on('click', function() {
				// hide table on submit
				$('#sd-test-wrapper').hide();

				// validate form
				if (!validator.validate()) {
					return false;
				}

				// disable submit button
				$(this).attr('disabled', true);

				UIAjax.do(
					'index.php?option=com_vikrestaurants&task=specialday.test',
					$('#adminForm').serialize(),
					(resp) => {
						// append received HTML
						$('.sd-test-response').html(resp);

						// render tooltip
						$('.sd-test-response .hasTooltip').tooltip({container: 'body'});

						// show test table
						$('#sd-test-wrapper').show();

						// enable submit button
						$(this).attr('disabled', false);
					},
					(error) => {
						// display error
						if (!error.responseText) {
							// use default connection lost error
							error.responseText = Joomla.JText._('VRSYSTEMCONNECTIONERR');
						}

						// alert error message
						alert(error.responseText);

						// enable submit button
						$(this).attr('disabled', false);
					}
				);
			});
		});
	})(jQuery);
</script>