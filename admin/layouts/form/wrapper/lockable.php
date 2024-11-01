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
 * @var  string   $input  The default rendered input.
 * @var  string   $id     The form field ID attribute.
 * @var  string   $value  The field saved value.
 */
extract($displayData);

?>

<div class="input-append">
	<?php echo $input; ?>

	<button type="button" class="btn" id="<?php echo $id; ?>-lock-btn">
		<i class="fas fa-<?php echo $value ? 'lock' : 'unlock'; ?>"></i>
	</button>
</div>

<script>
	(function($) {
		'use strict';

		$(function() {
			const input = $('#<?php echo $id; ?>');

			<?php if ($value): ?>
				// force read-only status
				input.prop('readonly', true);
			<?php endif; ?>

			$('#<?php echo $id; ?>-lock-btn').on('click', function() {
				if (input.prop('readonly')) {
					input.prop('readonly', false);

					$(this).find('i').removeClass('fa-lock');
					$(this).find('i').addClass('fa-unlock');
				} else {
					input.prop('readonly', true);

					$(this).find('i').removeClass('fa-unlock');
					$(this).find('i').addClass('fa-lock');
				}
			})
		});
	})(jQuery);	
</script>