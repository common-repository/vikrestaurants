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
 * @var  object|null    $coordinates  The latitude and longitude.
 * @var  string|null    $zip          The ZIP code.
 * @var  string|null    $city         The city name.
 * @var  string|null    $address      The street name and number.
 * @var  DeliveryQuery  $query        The delivery search query.
 * @var  Area|null      $area         The delivery area found.
 * @var  object|null    $texts        Some formatted texts.
 */
extract($displayData);

?>

<?php if (empty($area)): ?>

	<div class="fail"><?php echo JText::translate('VRTKDELIVERYLOCNOTFOUND'); ?></div>

<?php else: ?>

	<div class="success">

		<?php if ($coordinates): ?>
			<div class="info">
				<div class="data-label"><?php echo JText::translate('VRMANAGETKAREA16'); ?></div>
				<div class="data-value"><?php echo $coordinates->latitude . ', ' . $coordinates->longitude; ?></div>
			</div>
		<?php endif; ?>

		<div class="info">
			<div class="data-label"><?php echo JText::translate('VRMANAGETKAREA17'); ?></div>
			<div class="data-value"><?php echo $area->name; ?></div>
		</div>

		<div class="info">
			<div class="data-label"><?php echo JText::translate('VRMANAGETKAREA4'); ?></div>
			<div class="data-value"><?php echo $texts->charge; ?> </div>
		</div>

		<div class="info">
			<div class="data-label"><?php echo JText::translate('VRMANAGETKAREA18'); ?></div>
			<div class="data-value"><?php echo $texts->minCost; ?></div>
		</div>

	</div>

<?php endif; ?>
