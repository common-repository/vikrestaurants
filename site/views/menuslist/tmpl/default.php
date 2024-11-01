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

// refresh working shifts every time the date changes
JHtml::fetch('vrehtml.sitescripts.datepicker', '#vrcalendar:input');
JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.fontawesome');

$menus = $this->menus;

$desc_maximum_length = 180;

?>

<div class="vrmenuslistform" id="vrmenuslistform">

	<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=menuslist' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post">
		
		<?php if ($this->showSearchForm): ?>
			<div class="vrmenusfieldsdiv">

				<div class="vrmenufielddatediv">
					<label for="vrcalendar"><?php echo JText::translate('VRORDERDATETIME'); ?>:</label>
					
					<div class="vre-calendar-wrapper">
						<input type="text" name="date" value="<?php echo $this->escape($this->filters['date']); ?>" id="vrcalendar" class="vre-calendar" />
					</div>
				</div>

				<button type="submit" class="vre-btn primary"><?php echo JText::translate('VRMENUSEARCH'); ?></button>
			</div>
		<?php endif; ?>
		
		<div class="vrmenuslistcont">
			
			<?php
			if (count($menus) == 0 && strlen($this->filters['date']))
			{
				?>
				<div class="vrmenusondatenoaverr">
					<?php echo JText::translate('VRMENUSEARCHNOAVERR'); ?>
				</div>
				<?php
			}
			else if (count($menus))
			{
				// show menus blocks 
				foreach ($menus as $m)
				{	
					if (empty($m->image) || !is_file(VREMEDIA_SMALL . DIRECTORY_SEPARATOR . $m->image))
					{
						// use default image if not specified
						$m->image = 'menu_default_icon.jpg';   
					}
	
					// prepare description to properly interpret included plugins
					VREApplication::getInstance()->onContentPrepare($m->description);
					
					$desc = $m->description->text;

					// trim description if longer than the maximum limit
					if (strlen(strip_tags($desc)) > $desc_maximum_length)
					{
						$desc = mb_substr(strip_tags($desc), 0, $desc_maximum_length, 'UTF-8') . "...";
					}
					
					// fetch menu details URL
					$url = 'index.php?option=com_vikrestaurants&view=menudetails&id=' . $m->id;

					if ($this->showSearchForm)
					{
						$url .= '&date=' . $this->filters['date'];

						if ($this->filters['shift'])
						{
							$url .= '&shift=' . $this->filters['shift'];
						}
					}

					$url = JRoute::rewrite($url, false);

					$ws = array_filter(explode(',', $m->working_shifts));
					?>
					<div class="vrmenublock">

						<div class="vrmenublock-menu">

							<div class="vrmenublockimage">
								<a href="<?php echo $url; ?>">
									<?php
									echo JHtml::fetch('vrehtml.media.display', $m->image, [
										'alt'   => $m->name,
										'title' => $m->name,
									]);
									?>
								</a>
							</div>

							<div class="vrmenublockname">
								<a href="<?php echo $url; ?>"><?php echo $m->name; ?></a>
							</div>

							<div class="vrmenublockdesc"><?php echo $desc; ?></div>

							<?php if ($ws): ?>
								<div class="vrmenublockshifts">
									<?php
									foreach ($ws as $s)
									{
										// get time of shift
										$tmp = JHtml::fetch('vikrestaurants.timeofshift', $s);

										if ($tmp->showlabel && $tmp->label)
										{
											$label = $tmp->label;
										}
										else
										{
											$label = $tmp->fromtime . ' - ' . $tmp->totime;
										}
										?>
										<span class="vrmenublockworksh">
											<span class="vrmenublockworkshname"><?php echo $label; ?></span>
											<span class="vrmenublockworkshtime"><?php echo $tmp->fromtime . ' - ' . $tmp->totime; ?></span>
										</span>
										<?php
									}
									?>
								</div>
							<?php endif; ?>

						</div>

					</div>

					<?php
				}
			}
			?>

		</div>
			
		<input type="hidden" name="option" value="com_vikrestaurants" />
		<input type="hidden" name="view" value="menuslist" />
		
	</form>

</div>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('.vrmenublockworkshname').hover(function() {
				$('.vrmenublockworkshname').removeClass('vrmenublockworkhighlight');
				$('.vrmenublockworkshtime').removeClass('vrmenublockworkexploded');

				$(this).addClass('vrmenublockworkhighlight');

				$(this).siblings().each(function() {
					$(this).addClass('vrmenublockworkexploded');
				});
			}, function(){
				$('.vrmenublockworkshname').removeClass('vrmenublockworkhighlight');
				$('.vrmenublockworkshtime').removeClass('vrmenublockworkexploded');
			});
		});
	})(jQuery);
</script>