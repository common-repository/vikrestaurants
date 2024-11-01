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

$menu = $this->foreachMenu;

$sections = [];

foreach ($menu->sections as $s)
{
	if ($s->highlight)
	{
		$opt = new stdClass;
		$opt->id       = $s->id;
		$opt->name     = $s->name;
		$opt->selected = $sections ? false : true;
		
		// copy section in head bar
		$sections[] = $opt;
	}
}

?>

<div class="vre-order-dishes-menu">

	<!-- MENU DETAILS -->

	<h3><?php echo $menu->name; ?></h3>

	<?php if ($menu->description): ?>
		<div class="dishes-menu-description">
			<?php echo $menu->description; ?>
		</div>
	<?php endif; ?>

	<?php
	/**
	 * Display sections filter
	 *
	 * @since 1.8.1
	 */
	if (count($sections)): ?>
		<div class="vrmenu-sectionsbar orderdishes-page">
			<?php foreach ($sections as $s): ?>
				<span class="vrmenu-sectionsp">
					<a href="javascript: void(0);" class="vrmenu-sectionlink <?php echo ($s->selected ? 'vrmenu-sectionlight' : ''); ?>" onClick="vrFadeSection(<?php echo (int) $s->id; ?>);" id="vrmenuseclink<?php echo (int) $s->id; ?>">
						<?php echo $s->name; ?>
					</a>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- SECTIONS LIST -->

	<div class="vre-order-dishes-sections">
		<?php
		foreach ($menu->sections as $section)
		{
			// assign section for being used in a sub-template
			$this->foreachSection = $section;

			// display section block
			echo $this->loadTemplate('section');
		}
		?>
	</div>

</div>
