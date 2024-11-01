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
JHtml::fetch('vrehtml.sitescripts.animate');
JHtml::fetch('vrehtml.assets.toast', 'bottom-center');
JHtml::fetch('vrehtml.assets.fontawesome', 'bottom-center');

$item = $this->item;

?>

<!-- display "breadcrumb" -->

<div class="vrtk-itemdet-category">

	<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeaway' . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>"><?php echo JText::translate('VRTAKEAWAYALLMENUS'); ?></a>

	<span class="arrow-separator">&raquo;</span>

	<a href="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeaway&takeaway_menu=' . $item->menu->id . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>"><?php echo $item->menu->title; ?></a>

</div>

<?php
/**
 * Changed "takeaway_entry" with "takeaway_item" within the form action.
 * This field is required for those products that own at least
 * a topping group assigned to a specific variation. In this case,
 * after switching variation, the form is self-submitted to reload 
 * the toppings to show. Since this field were missing, the view 
 * wasn't able to recover the correct product, causing a redirect
 * to the takeaway list page.
 *
 * @since 1.7.4
 */
?>
<form action="<?php echo JRoute::rewrite('index.php?option=com_vikrestaurants&view=takeawayitem&takeaway_item=' . $item->id . ($this->itemid ? '&Itemid=' . $this->itemid : '')); ?>" method="post" name="vrtkitemform" id="vrtkitemform">

	<div class="vrtk-itemdet-page">
			
		<!-- Product Wrapper -->
		<div class="vrtk-itemdet-product">
			<?php
			// display the product details with a sub-template
			echo $this->loadTemplate('item');
			?>
		</div>

	</div>

	<input type="hidden" name="option" value="com_vikrestaurants" />
	<input type="hidden" name="view" value="takeawayitem" />
	<input type="hidden" name="id_entry" value="<?php echo (int) $item->id; ?>" />
	<input type="hidden" name="item_index" value="-1" />

</form>

<!-- delimiter for take-away cart module -->

<div class="vrtkgotopaydiv">&nbsp;</div>

<?php if (count($this->attributes)): ?>
	<div class="vrtk-attributes-legend">
		<?php foreach ($this->attributes as $attr): ?>
			<div class="vrtk-attribute-box">
				<?php
				echo JHtml::fetch('vrehtml.media.display', $attr->icon, [
					'alt' => $attr->name,
				]);
				?>
				<span><?php echo $attr->name; ?></span>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<?php
/**
 * Creates the popup that will be used to display the details
 * of the products that are going to be added.
 *
 * The popup will be shown when trying to edit a product
 * from the cart.
 */
echo $this->loadTemplate('overlay');

if ($this->reviews !== false)
{
	// display the reviews list by using a sub-template
	echo $this->loadTemplate('reviews');
}
