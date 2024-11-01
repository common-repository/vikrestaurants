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
 * @var  int          $id_assoc    The product-bill relationship ID.
 * @var  int          $id_product  The product ID.
 * @var  object|null  $item        The item details if the product exists. Otherwise
 *                                 null in case the item has to be created first.
 */
extract($displayData);

?>

<div class="control-group">
	<h4><?php echo ($item !== null ? $item->name : JText::translate('VRCREATENEWPROD')); ?></h4>
</div>

<?php if ($item === null): ?>
	<div class="vrfront-field">
		<span class="field-label"><?php echo JText::translate('VRNAME'); ?></span>

		<span class="field-value">
			<input type="text" name="name" value="" size="32" />
		</span>
	</div>

	<div class="vrfront-field">
		<span class="field-label"><?php echo JText::translate('VRPRICE'); ?></span>

		<div class="field-value currency">
			<input type="number" name="price" value="0.00" size="4" min="0" step="any" />

			<span><?php echo VREFactory::getCurrency()->getSymbol(); ?></span>
		</div>
	</div>
<?php elseif (count($item->options)): ?>
	<div class="vrfront-field">
		<span class="field-label"><?php echo JText::translate('VRVARIATION'); ?></span>

		<div class="field-value vre-select-wrapper">
			<select name="id_option" class="vrtk-variations-reqselect vre-select">
				<?php foreach ($item->options as $opt): ?>
					<option
						value="<?php echo $opt->id; ?>"
						<?php echo (($item->id_product_option ?? 0) == $opt->id ? 'selected="selected"' : ''); ?>
					><?php echo $opt->name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>

<?php if (VREFactory::getConfig()->getBool('servingnumber')): ?>
	<div class="vrfront-field">
		<span class="field-label"><?php echo JText::translate('VRE_ORDERDISH_SERVING_NUMBER_LABEL_SHORT'); ?></span>

		<div class="field-value vre-select-wrapper">
			<select name="serving_number" class="vrtk-variations-reqselect vre-select">
				<?php foreach ([0, 1, 2] as $number): ?>
					<option
						value="<?php echo $number; ?>"
						<?php echo ($item->servingnumber ?? 0) == $number ? 'selected="selected"' : ''; ?>
					><?php echo JText::translate('VRE_ORDERDISH_SERVING_NUMBER_' . $number); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>

<div class="vrfront-field">
	<span class="field-label"><?php echo JText::translate('VRTKADDQUANTITY'); ?></span>

	<span class="field-value">
		<input type="number" name="quantity" value="<?php echo (int) ($item->quantity ?? 1); ?>" size="4" min="1" step="1" />
	</span>
</div>
			
<div class="vrfront-field">
	<span class="field-label"><?php echo JText::translate('VRNOTES'); ?></span>

	<span class="field-value">
		<textarea name="notes" maxlength="128" style="width:100%;height:100px;"><?php echo $item->notes ?? ''; ?></textarea>
	</span>
</div>

<div class="vrfront-field">
	<span class="field-label"></span>

	<span class="field-value">
		<button type="button" class="vre-btn primary large" onClick="vrPostItem(<?php echo ($item !== null ? 1 : 0); ?>);">
			<?php echo strtoupper(JText::translate($id_assoc >= 0 ? 'VRSAVE' : 'VRTKADDOKBUTTON')); ?>
		</button>
	</span>
</div>

<input type="hidden" name="item_index" value="<?php echo (int) $id_assoc; ?>" />
<input type="hidden" name="id_entry" value="<?php echo (int) $id_product; ?>" />
