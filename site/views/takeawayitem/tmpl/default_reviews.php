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
 * Template file used to display a short list of reviews
 * left for the selected product.
 *
 * @since 1.8
 */

?>

<div class="vr-reviews-quickwrapper">

	<h3><?php echo JText::translate('VRREVIEWSTITLE'); ?></h3>

	<?php if ($this->reviewsStats !== null): ?>
		<div class="rv-reviews-quickstats">

			<div class="rv-top">

				<div class="rv-average-stars">
					<?php
					/**
					 * Displays the rating stars.
					 * It is possible to change the $image argument to false
					 * to use FontAwesome 4 instead of the images.
					 * For FontAwesome 5, $image have to be set to "5.0".
					 */
					echo JHtml::fetch('vikrestaurants.rating', $this->reviewsStats->halfRating, $image = true);
					?>
				</div>

				<div class="rv-count-reviews">
					<?php echo JText::sprintf('VRREVIEWSCOUNT', $this->reviewsStats->count); ?>
				</div>

				<?php
				// checks whether the current user is allowed to leave a review for this product
				if (VikRestaurants::canLeaveTakeAwayReview($this->item->id)): ?>
					<div class="rv-submit-review">
						<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=revslist&id_tk_prod=' . $this->item->id . '&submit_rev=1' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vre-btn secondary">
							<?php echo JText::translate('VRREVIEWLEAVEBUTTON'); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<div class="rv-average-ratings">
				<?php
				echo JText::sprintf(
					'VRREVIEWSAVG', 
					floatval(number_format($this->reviewsStats->rating, 2))
				);
				?>
			</div>

			<?php if ($this->reviewsStats->count > 0): ?>
				<div class="rv-see-all">
					<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=revslist&id_tk_prod=' . $this->item->id . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" class="vre-btn secondary">
						<?php echo JText::sprintf('VRREVIEWSEEALLBUTTON', $this->reviewsStats->count); ?>
					</a>
				</div>
			<?php endif; ?>

		</div>
	<?php endif; ?>

	<div class="vr-reviews-quicklist">

		<?php
		if (!count($this->reviews))
		{
			?>
			<div class="no-review"><?php echo JText::translate('VRREVIEWSNOLEFT'); ?></div>
			<?php
		}
		else
		{
			// load layout used to display each review block
			$layout = new JLayoutFile('blocks.review');

			/**
			 * The preview of the ratings displays a short list
			 * of the most rated reviews. In case of same rating,
			 * the most recent will be shown first.
			 */
			foreach ($this->reviews as $review)
			{
				/**
				 * The review block is displayed from the layout below:
				 * /components/com_vikrestaurants/layouts/blocks/review.php
				 *
				 * @since 1.8
				 */
				echo $layout->render(['review' => $review]);
			}
		}
		?>

	</div>

</div>
