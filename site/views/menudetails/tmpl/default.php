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
JHtml::fetch('vrehtml.assets.fancybox');
JHtml::fetch('vrehtml.assets.fontawesome');

$menu = $this->menu;

$all_sections_opt = new stdClass;
$all_sections_opt->id       = 0;
$all_sections_opt->name     = JText::translate('VRMENUDETAILSALLSECTIONS');
$all_sections_opt->selected = true;

$sections = [$all_sections_opt];

foreach ($menu->sections as $s)
{
	if ($s->highlight)
	{
		$opt = new stdClass;
		$opt->id       = $s->id;
		$opt->name     = $s->name;
		$opt->selected = false;
		
		// copy section in head bar
		$sections[] = $opt;
	}
}

$currency = VREFactory::getCurrency();

$last_section_highlighted = -1;

$galleryIndex = 0;

?>

<div class="vrmenu-detailsmain">
	
	<?php if ($this->isPrintable): ?>
		<div class="vrmenu-print-btn">
			<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=menudetails&tmpl=component&id=' . $menu->id . ($this->itemid ? '&Itemid=' . $this->itemid : ''), false); ?>" target="_blank">
				<i class="fas fa-print"></i>
			</a>
		</div>
	<?php endif; ?>

	<!-- MENU DETAILS -->
	
	<div class="vrmenu-detailshead" >
		<h3><?php echo $menu->name; ?></h3>
		
		<div class="vrmenu-detailsheadsub">
			<?php if ($menu->image): ?>
				<div class="vrmenu-detailsheadsubimage">
					<a href="javascript:void(0)" onClick="vreOpenGallery(this);" data-index="<?php echo $galleryIndex++; ?>" class="vremodal">
						<?php
						echo JHtml::fetch('vrehtml.media.display', $menu->image, [
							'alt' => $menu->name,
						]);
						?>
					</a>
				</div>
			<?php endif; ?>

			<?php if ($menu->description): ?>
				<div class="vrmenu-detailsheadsubdesc">
					<?php
					// prepare description to properly interpret included plugins
					VREApplication::getInstance()->onContentPrepare($menu->description);

					echo $menu->description->text;
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- SECTIONS BAR -->
	
	<?php
	/**
	 * Show section bar only in case there is at least
	 * an highlighted section, otherwise only the "ALL"
	 * option would be displayed.
	 *
	 * @since 1.8
	 */
	if (count($sections) > 1): ?>
		<div class="vrmenu-sectionsbar">
			<?php foreach ($sections as $s): ?>
				<span class="vrmenu-sectionsp">
					<a href="javascript: void(0);" class="vrmenu-sectionlink <?php echo ($s->selected ? 'vrmenu-sectionlight' : ''); ?>" onClick="vrFadeSection(<?php echo $s->id; ?>);" id="vrmenuseclink<?php echo $s->id; ?>">
						<?php echo $s->name; ?>
					</a>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- MENU SECTIONS -->
	
	<div class="vrmenu-detailslist">
		
		<?php
		foreach ($menu->sections as $s)
		{
			if ($s->highlight)
			{
				$last_section_highlighted = $s->id;
			}
			?>

			<!-- SECTION -->
			
			<div class="vrmenu-detailssection <?php echo 'vrmenusubsection' . $last_section_highlighted; ?>" id="vrmenusection<?php echo $s->id; ?>">
				<h3><?php echo $s->name; ?></h3>

				<div class="vrmenu-detailssectionsub">
					<?php if ($s->image): ?>
						<div class="vrmenu-detailssectionsubimage">
							<a href="javascript:void(0)" onClick="vreOpenGallery(this);" data-index="<?php echo $galleryIndex++; ?>" class="vremodal">
								<?php
								echo JHtml::fetch('vrehtml.media.display', $s->image, [
									'alt' => $s->name,
								]);
								?>
							</a>
						</div>
					<?php endif; ?>

					<?php if ($s->description): ?>
						<div class="vrmenu-detailssectionsubdesc">
							<?php
							// prepare description to properly interpret included plugins
							VREApplication::getInstance()->onContentPrepare($s->description);

							echo $s->description->text;
							?>
						</div>
					<?php endif; ?>
				</div>

				<!-- SECTION PRODUCTS -->
				
				<?php if (count($s->products)): ?>

					<div class="vrmenu-detailsprodlist">

						<?php foreach ($s->products as $p): ?>

							<!-- PRODUCT -->

							<div class="vrmenu-detailsprod">
								<div class="vrmenu-detailsprodsub">

									<div class="vrmenu-detailsprodsubleft">

										<?php if($p->image): ?>
											<div class="vrmenu-detailsprodsubimage">
												<a href="javascript:void(0)" onClick="vreOpenGallery(this);" data-index="<?php echo $galleryIndex++; ?>" class="vremodal">
													<?php
													echo JHtml::fetch('vrehtml.media.display', $p->image, [
														'alt' => $p->name,
													]);
													?>
												</a>
											</div>
										<?php endif; ?>

										<div class="vr-menudetailsprodsubnamedesc">
											<h3><?php echo $p->name; ?></h3>

											<?php if ($p->description): ?>
												<div class="vrmenu-detailsprodsubdesc">
													<?php
													// prepare description to properly interpret included plugins
													VREApplication::getInstance()->onContentPrepare($p->description);

													echo $p->description->text;
													?>
												</div>
											<?php endif; ?>
										</div>

									</div>

									<!-- OPTIONS -->

									<div class="vrmenu-detailsprodsubright">

										<?php if (count($p->options)): ?>

											<div class="vrmenu-detailsprod-optionslist">

												<?php foreach ($p->options as $o): ?>

													<!-- OPTION -->

													<div class="vrmenu-detailsprod-option">

														<div class="option-name"><?php echo $o->name; ?></div>

														<?php if ($p->price + $o->price > 0): ?>
															<div class="option-price">
																<?php echo $currency->format($p->price + $o->price); ?>
															</div>
														<?php endif; ?>

													</div>

												<?php endforeach; ?>

											</div>

										<?php elseif ($p->price > 0): ?>

											<div class="vrmenu-detailsprodsubprice">
												<span class="vrmenu-detailsprodsubpricesp">
													<?php echo $currency->format($p->price); ?>
												</span>
											</div>

										<?php endif; ?>

									</div>

								</div>
							</div>
						
						<?php endforeach; ?>
											 
					</div>
				
				<?php endif;?>

			</div>
			
			<?php
		}
		?>
	</div>
	 
</div>

<script>
	(function($) {
		'use strict';

		let GALLERY_DATA = [];

		window.vreOpenGallery = (elem) => {
			let index = parseInt($(elem).data('index'));

			const instance = $.fancybox.open(GALLERY_DATA);

			if (index > 0) {
				// jump to selected image ('0' turns off the animation)
				instance.jumpTo(index, 0);
			}
		}
		
		window.vrFadeSection = (id_section) => {
			$('.vrmenu-sectionlink').removeClass('vrmenu-sectionlight');
			
			$('#vrmenuseclink' + id_section).addClass('vrmenu-sectionlight');
			
			if (id_section == 0) {
				$('.vrmenu-detailssection').fadeIn('fast');
			} else {
				$('.vrmenu-detailssection').hide();
				$('#vrmenusection' + id_section).fadeIn('fast');
				$('.vrmenusubsection' + id_section).fadeIn('fast');
			}
		}

		$(function() {
			// prepare gallery data
			$('.vrmenu-detailsmain .vremodal img').each(function() {
				let src = $(this).attr('src');

				GALLERY_DATA.push({
					src:  src,
					type: 'image',
					opts: {
						caption: $(this).attr('data-caption') || $(this).attr('alt'),
						thumb:   src.replace(/\/media\//, '/media@small/'),
					},
				});
			});

			<?php if ($id_section = JFactory::getApplication()->input->getUint('id_section')): ?>
				vrFadeSection(<?php echo $id_section; ?>);
			<?php endif; ?>
		});
	})(jQuery);	
</script>