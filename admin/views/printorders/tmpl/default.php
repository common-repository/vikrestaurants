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

?>

<div class="vr-printer-layout">

	<?php if (strlen((string) $this->text['header'])): ?>
		<div class="vr-printer-header"><?php echo $this->text['header']; ?></div>
	<?php endif; ?>

	<div class="vr-print-orders-container">
		<?php
		foreach ($this->rows as $i => $r)
		{	
			// save order as class property for being used in sub-layout
			$this->orderDetails = $r;

			if ($this->type == 'takeaway')
			{
				// display take-away order details
				echo $this->loadTemplate('takeaway');
			}
			else
			{
				// display restaurant reservation details
				echo $this->loadTemplate('restaurant');	
			}	
		}
		?>
	</div>

	<?php if (strlen((string) $this->text['footer'])): ?>
		<div class="vr-printer-footer"><?php echo $this->text['footer']; ?></div>
	<?php endif; ?>

</div>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			w.print();
		});
	})(jQuery, window);
</script>