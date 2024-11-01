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
 * @var  string   $input       The default rendered input.
 * @var  string   $textBefore  The text to be displayed before the input.
 * @var  string   $textAfter   The text to be displayed after the input.
 */
extract($displayData);

$classes = [];

if ($textBefore)
{
	$classes[] = 'input-prepend';
}

if ($textAfter)
{
	$classes[] = 'input-append';
}

?>

<div class="<?php echo implode(' ', $classes); ?>">
	<?php if ($textBefore): ?>
		<?php foreach ((array) $textBefore as $text): ?>
			<span class="btn text-nowrap">
				<?php echo $text; ?>
			</span>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php echo $input; ?>

	<?php if ($textAfter): ?>
		<?php foreach ((array) $textAfter as $text): ?>
			<span class="btn text-nowrap">
				<?php echo $text; ?>
			</span>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
