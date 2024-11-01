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
 * Template file used to display a menu block.
 * This file iterates the products that belong to
 * the menu, which are displayed using a different
 * sub-template.
 *
 * @since 1.8
 */

$config = VREFactory::getConfig();

$menu = $this->forMenu;

?>

<div class="vrtk-menu-outer <?php echo $menu->layout; ?>-layout">

	<div class="vrtkmenuheader">

		<div class="vrtkmenutitlediv<?php echo (!$menu->isActive ? ' disabled' : ''); ?>">

			<div class="vrtk-menu-title"><?php echo $menu->title; ?></div>
			
			<?php if (!$menu->isActive): ?>
				<div class="vrtk-menusubtitle-notactive">
					<?php echo $menu->availError; ?>
				</div>
			<?php endif; ?>

		</div>

		<div class="vrtkmenudescdiv">
			<?php
			// prepare description to properly interpret included plugins
			VREApplication::getInstance()->onContentPrepare($menu->description);

			echo $menu->description->text;
			?>
		</div>

	</div>

	<div class="vrtkitemsofmenudiv">
	
		<?php
		foreach ($menu->products as $item)
		{
			// keep a reference of the current product for
			// being used in a sub-template
			$this->forItem = $item;

			// sisplays the current product block
			echo $this->loadTemplate('item');
		}
		?>

	</div>

</div>
