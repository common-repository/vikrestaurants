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
JHtml::fetch('vrehtml.assets.fontawesome');

$currency = VREFactory::getCurrency();

?>

<div class="vrmenuprevcont" style="padding: 10px;">

	<?php foreach ($this->sections as $s): ?>
		<div class="vrmenuprevblock" id="vrsec<?php echo (int) $s->id; ?>">
			<div class="vrmenuprevsection">
				<span class="vrmenuprevsectionname"><?php echo $s->name; ?></span>
				<span class="vrmenuprevsectionright">
					<span class="vrmenuprevsectionimg">
						<?php echo JHtml::fetch('vrehtml.admin.imagestatus', $s->image); ?>
					</span>

					<span class="vrmenuprevsectionpubl">
						<?php
						$title = '';

						if ($s->published)
						{
							$sfx = 'check-circle ok';
						}
						else if ($s->orderdishes)
						{
							$sfx   = 'minus-circle warn hasTooltip';
							$title = JText::translate('VRMANAGEMENU34_DESC_SHORT');
						}
						else
						{
							$sfx = 'dots-circle no';
						}
						?>
						<i class="fas fa-<?php echo $sfx; ?> big hidden-phone" title="<?php echo $this->escape($title); ?>"></i>
						<i class="fas fa-<?php echo $sfx; ?> mobile-only"></i>
					</span>
				</span>
			</div>
			
			<?php if (count($s->products)): ?>	
				<div class="vrmenuprevproducts" id="vrmenuprevproducts<?php echo (int) $s->id; ?>">

					<?php foreach ($s->products as $p): ?>
						<div class="vrmenuprevprod" id="vrprod<?php echo $p->idAssoc; ?>">
							<span class="vrmenuprevprodname"><?php echo $p->name; ?></span>
							<span class="vrmenuprevprodprice">
								<?php
								if ($p->charge != 0)
								{
									?><del><?php echo $currency->format($p->price); ?></del><?php
								}
								?>
								<span><?php echo $currency->format($p->price + $p->charge); ?></span>
							</span>
							<span class="vrmenuprevprodright">
								<span class="vrmenuprevprodimg">
									<?php echo JHtml::fetch('vrehtml.admin.imagestatus', $p->image); ?>
								</span>

								<span class="vrmenuprevprodpubl">
									<i class="fas fa-<?php echo $p->published ? 'check-circle ok' : 'dot-circle no'; ?> big hidden-phone"></i>
									<i class="fas fa-<?php echo $p->published ? 'check-circle ok' : 'dot-circle no'; ?> mobile-only"></i>
								</span>
							</span>
						</div>
					<?php endforeach; ?>
				 
				</div>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>

</div>
		
<script>
	(function($) {
		'use strict';

		$(function() {
			$('.vrmenuprevsectionname').on('click', function() {
				const next = $(this).parent().next('.vrmenuprevproducts');

				if (next.is(':visible')) {
					next.slideUp();
				} else {
					next.slideDown();
				}
			});
		});
	})(jQuery);
</script>