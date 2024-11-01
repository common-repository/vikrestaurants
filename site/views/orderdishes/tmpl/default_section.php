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

$section = $this->foreachSection;

?>

<div class="vre-order-dishes-section<?php echo $section->highlight ? ' can-highlight' : ''; ?>" id="vrmenusection<?php echo (int) $section->id; ?>" data-id="<?php echo (int) $section->id; ?>">

	<!-- SECTION DETAILS -->

	<h4><?php echo $section->name; ?></h4>

	<?php if ($section->description): ?>
		<div class="dishes-section-description">
			<?php echo $section->description; ?>
		</div>
	<?php endif; ?>

	<!-- PRODUCTS LIST -->

	<div class="vre-order-dishes-products">
		<?php
		foreach ($section->products as $product)
		{
			// assign product for being used in a sub-template
			$this->foreachProduct = $product;

			// display product block
			echo $this->loadTemplate('product');
		}
		?>
	</div>

</div>
