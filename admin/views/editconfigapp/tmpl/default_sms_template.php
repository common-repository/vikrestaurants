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
 * called "onDisplayViewConfigappSmsTemplates". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('SmsTemplates');

if ($params['enablerestaurant'])
{
	// display sms templates for the restaurant
	echo $this->loadTemplate('sms_template_restaurant');
}

if ($params['enabletakeaway'])
{
	// display sms templates for the take-away
	echo $this->loadTemplate('sms_template_takeaway');
}
?>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigappSmsTemplates","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the SMS > Templates tab.
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
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('button.sms-put-tag').on('click', function() {
				const area = $(this).closest('.config-fieldset-body').find('textarea');

				if (!area) {
					return false;
				}

				let cont  = $(this).text().trim();
				let start = area.get(0).selectionStart;
				let end   = area.get(0).selectionEnd;

				area.val(area.val().substring(0, start) + cont + area.val().substring(end));
				area.get(0).selectionStart = area.get(0).selectionEnd = start + cont.length;
				area.focus();1
			});
		});
	})(jQuery);
</script>