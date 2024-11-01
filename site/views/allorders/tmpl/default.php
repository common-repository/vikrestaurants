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

JHtml::fetch('vrehtml.sitescripts.animate');

?>
	
<div class="vr-allorders-userhead">

	<div class="vr-allorders-userleft">
		<h2><?php echo JText::sprintf('VRALLORDERSTITLE', $this->user->name); ?></h2>
	</div>

	<div class="vr-allorders-userright">
		<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&task=userprofile.logout' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vre-btn secondary">
			<?php echo JText::translate('VRLOGOUT'); ?>
		</a>
	</div>
</div>

<div class="vr-allorders-switch-tabs">
	<?php if ($this->isRestaurantEnabled): ?>
		<div class="switch-box <?php echo ($this->activeTab == 'restaurant' ? 'active' : ''); ?>">
			<a href="javascript:void(0)" onClick="switchOrderTab(this, 'restaurant');">
				<?php echo JText::translate('VRALLORDERSRESTAURANTHEAD'); ?>
			</a>
		</div>
	<?php endif; ?>

	<?php if ($this->isTakeAwayEnabled): ?>
		<div class="switch-box <?php echo ($this->activeTab == 'takeaway' ? 'active' : ''); ?>">
			<a href="javascript:void(0)" onClick="switchOrderTab(this, 'takeaway');">
				<?php echo JText::translate('VRALLORDERSTAKEAWAYHEAD'); ?>
			</a>
		</div>
	<?php endif; ?>
</div>

<?php if ($this->isRestaurantEnabled): ?>
	<div class="vr-allorders-wrapper" id="vrboxwrapper-restaurant" style="<?php echo ($this->activeTab == 'restaurant'  ? '' : 'display:none;'); ?>">
		<?php
		// display restaurant reservations by using a sub-template
		echo $this->loadTemplate('restaurant');
		?>
	</div>
<?php endif; ?>

<?php if ($this->isTakeAwayEnabled): ?>
	<div class="vr-allorders-wrapper" id="vrboxwrapper-takeaway" style="<?php echo ($this->activeTab == 'takeaway' ? '' : 'display:none;'); ?>">
		<?php
		// display take-away orders by using a sub-template
		echo $this->loadTemplate('takeaway');
		?>
	</div>
<?php endif; ?>

<script>
	(function($) {
		'use strict';

		window.switchOrderTab = (link, tab) => {
			$('.vr-allorders-switch-tabs .switch-box').removeClass('active');
			$(link).parent().addClass('active');

			$('.vr-allorders-wrapper').hide();
			$('#vrboxwrapper-' + tab).show();

			document.cookie = 'vre.allorders.activetab=' + tab + '; path=/';
		}
	})(jQuery);
</script>