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

$status = $this->status;

?>

<!-- RESTAURANT - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'    => 'checkbox',
	'name'    => 'restaurant',
	'checked' => $status->restaurant,
	'label'   => JText::translate('VRMENUTITLEHEADER1'),
]);
?>

<!-- TAKE-AWAY - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'    => 'checkbox',
	'name'    => 'takeaway',
	'checked' => $status->takeaway,
	'label'   => JText::translate('VRMENUTITLEHEADER5'),
]);
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			// Observe validator instance and wait until it is ready.
			// Workaround needed to avoid the issue that occurs on WordPress.
			onInstanceReady(() => {
				if (typeof w.validator === 'undefined') {
					return false;
				}

				return w.validator;
			}).then((validator) => {
				// extend form validation by checking whether the user
				// selected at least one of the available groups
				validator.addCallback((form) => {
					// get all group fields
					const fields = $('input[name="restaurant"]')
						.add($('input[name="takeaway"]'));

					let checked = false;

					// look for a group selection
					fields.each(function() {
						if ($(this).is(':checkbox')) {
							checked = checked || $(this).is(':checked');
						} else {
							checked = checked || ($(this).val() == 1);
						}
					});

					if (!checked) {
						// no selected groups, mark as invalid
						form.setInvalid(fields);

						return false;
					}

					// at least one selected, mark as valid
					form.unsetInvalid(fields);

					return true;
				});
			});
		});
	})(jQuery, window);
</script>