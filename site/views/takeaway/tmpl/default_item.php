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
 * Template file used to display a product and its options.
 *
 * @since 1.8
 */

$item = $this->forItem;

$config = VREFactory::getConfig();

$currency = VREFactory::getCurrency();

// get maximum number of characters to display for the description
$max_desc_len = $config->getUint('tkproddesclength');

// save item description in a variable
$article = $item->description;

// prepare description to properly interpret included plugins
VREApplication::getInstance()->onContentPrepare($article);

/**
 * Checks whether the article supports an intro text.
 *
 * @since 1.8.3
 */
if (empty($article->introtext))
{
	// checks whether the plain text of the description
	// exceeds the maximum number of characters
	if (strlen(strip_tags($article->text)) > $max_desc_len)
	{
		// get only the first N characters of the plain text, in
		// order to avoid truncating HTML tags
		$article->introtext = mb_substr(strip_tags($article->text), 0, $max_desc_len, 'UTF-8') . '...';
	}
}

$use_overlay = $config->getUint('tkuseoverlay');

?>

<div class="vrtksingleitemdiv">

	<div class="vrtkitemleftdiv">

		<?php
		// check if we should display the product image
		if ($item->image && $config->getBool('tkshowimages') && is_file(VREMEDIA . DIRECTORY_SEPARATOR . $item->image)): ?>
			<div class="vrtkitemimagediv-outer">
				<div class="vrtkitemimagediv">
					<a href="javascript: void(0);" class="vremodal" onClick="vreOpenGallery(this);">
						<?php
						echo JHtml::fetch('vrehtml.media.display', $item->image, [
							'small'     => true,
							'alt'       => $item->name,
							'data-menu' => $this->forMenu->id,
							'data-prod' => $item->id,
						]);
						?>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<div class="vrtkiteminfodiv">

			<div class="vrtkitemtitle">

				<span class="vrtkitemnamesp">
					<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayitem&takeaway_item=' . $item->id . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>">
						<?php echo $item->name; ?>
					</a>
				</span>

				<?php if ($item->attributes): ?>
					<span class="vrtkitemattributes">
						<?php
						foreach ($item->attributes as $attr)
						{
							echo JHtml::fetch('vrehtml.media.display', $attr->icon, [
								'alt'   => $attr->name,
								'title' => $attr->name,
							]);
						}
						?>
					</span>
				<?php endif; ?>

			</div>

			<div class="vrtkitemdescsp" id="vrtkitemshortdescsp<?php echo (int) $item->id; ?>">
				<?php
				if ($article->introtext)
				{
					// use intro
					echo $article->introtext;

					?>
					<a href="javascript: void(0);" onClick="showMoreDesc(<?php echo (int) $item->id ?>);">
						<strong><?php echo JText::translate('VRTAKEAWAYMOREBUTTON'); ?></strong>
					</a>
					<?php
				}
				else
				{
					// use full text
					echo $article->text;
				}
				?>
			</div>

			<?php if ($article->introtext): ?>
				<div class="vrtkitemdescsp" id="vrtkitemlongdescsp<?php echo (int) $item->id; ?>" style="display: none;">
					<?php echo $article->text; ?>
					<a href="javascript:void(0)" onClick="showLessDesc(<?php echo (int) $item->id ?>);">
						<strong><?php echo JText::translate('VRTAKEAWAYLESSBUTTON'); ?></strong>
					</a>
				</div>
			<?php endif; ?>

		</div>

	</div>
	
	<div id="vrtkitemoptions<?php echo (int) $item->id; ?>" class="vrtkitemvardiv">
		
		<?php
		// display layout with multiple variations
		if (count($item->options))
		{
			foreach ($item->options as $option)
			{
				$price = $item->price + $option->price;

				// checks whether this variation is discounted
				$discountedPrice = $this->dealsHandler->discountItem([
					'id'        => $item->id,
					'id_option' => $option->id,
					'price'     => $price,
				], $this->discountDeals);
				?>
				
				<div class="vrtksinglevar">

					<span class="vrtkvarnamesp"><?php echo $option->name; ?></span>

					<div class="vrtkvarfloatrdiv">

						<?php if ($discountedPrice < $price): ?>
							<span class="vrtk-itemprice-stroke">
								<s><?php echo $currency->format($price); ?></s>
							</span>
						<?php endif; ?>

						<span class="vrtkvarpricesp">
							<?php echo $currency->format($discountedPrice); ?>
						</span>

						<?php if ($this->forMenu->isActive): ?>
							<div class="vrtkvaraddbuttondiv">
								<?php if ($use_overlay == 2 || ($use_overlay == 1 && VikRestaurants::hasItemToppings($item->id, $option->id))): ?>
									<button type="button" class="vrtkvaraddbutton" onClick="vrOpenOverlay('vrnewitemoverlay', '<?php echo $this->escape(addslashes($item->name . ' - ' . $option->name)); ?>', <?php echo $item->id; ?>, <?php echo $option->id; ?>, -1);">
										<i class="fas fa-plus-square"></i>
									</button>
								<?php else: ?>
									<button type="button" class="vrtkvaraddbutton" onClick="vrInsertTakeAwayItem(<?php echo $item->id; ?>, <?php echo $option->id; ?>);">
										<i class="fas fa-plus-square"></i>
									</button>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

				</div>
				<?php
			}
		}
		// display layout without variations
		else
		{
			// checks whether this product is discounted
			$discountedPrice = $this->dealsHandler->discountItem([
				'id'    => $item->id,
				'price' => $item->price,
			], $this->discountDeals);
			?>
			
			<div class="vrtksinglevar">

				<span class="vrtkvarnamesp">&nbsp;</span>

				<div class="vrtkvarfloatrdiv">

					<?php if ($discountedPrice < $item->price): ?>
						<span class="vrtk-itemprice-stroke">
							<s><?php echo $currency->format($item->price); ?></s>
						</span>
					<?php endif; ?>

					<span class="vrtkvarpricesp">
						<?php echo $currency->format($discountedPrice); ?>
					</span>

					<?php if ($this->forMenu->isActive): ?>
						<div class="vrtkvaraddbuttondiv">
							<?php if ($use_overlay == 2 || ($use_overlay == 1 && VikRestaurants::hasItemToppings($item->id))): ?>
								<button type="button" class="vrtkvaraddbutton" onClick="vrOpenOverlay('vrnewitemoverlay', '<?php echo $this->escape(addslashes($item->name)); ?>', <?php echo $item->id; ?>, 0, -1);">
									<i class="fas fa-plus-square"></i>
								</button>
							<?php else: ?>
								<button type="button" class="vrtkvaraddbutton" onClick="vrInsertTakeAwayItem(<?php echo $item->id; ?>, 0);">
									<i class="fas fa-plus-square"></i>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

			</div>
			<?php
		}
		?>
										
	</div>

</div>
