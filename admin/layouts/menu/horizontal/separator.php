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

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   boolean  $selected   True whether the separator owns an active item.
 * @var   boolean  $collapsed  True whether the separator is collapsed.
 * @var   string   $href       The separator HREF, if specified.
 * @var   string   $icon       The custom icon, if specified.
 * @var   string   $title      The separator title.
 * @var   array    $children   The separator menu items.
 * @var   string   $html       The children HTML.
 */

?>

<div class="parent">
	<div class="title<?php echo $selected || $collapsed ? ' selected' : ''; ?><?php echo strlen($href) ? ' has-href' : ''; ?>">

		<a <?php echo strlen($href) ? 'href="' . $href . '"' : ''; ?>>
			<?php
			if (strlen($icon))
			{
				?><i class="fas fa-<?php echo $icon; ?>"></i><?php
			}

			?><span><?php echo $title; ?></span><?php
			
			if (count($children))
			{
				?><i class="fas fa-angle-down vre-angle-dir"></i><?php
			}
			?>
		</a>

		<?php
		if (strlen($html))
		{
			?>
			<div class="wrapper"><?php echo $html; ?></div>
			<?php
		}
		?>

	</div>
</div>