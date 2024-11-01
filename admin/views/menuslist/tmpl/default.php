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

$vik = VREApplication::getInstance();

$cardLayout = new JLayoutFile('blocks.card');

?>

<div style="padding: 10px;">
	<?php
	if (count($this->menus) == 0)
	{
		echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
	}
	else
	{
		?>
		<div class="vre-cards-container">

			<?php
			foreach ($this->menus as $menu)
			{
				if (empty($menu->image) || !is_file(VREMEDIA . DIRECTORY_SEPARATOR . $menu->image))
				{
					if ($this->specialDay->group == 1)
					{
						// use default menu icon for restaurant
						$menu->image = VREMEDIA_URI . 'menu_default_icon.jpg';
					}
					else
					{
						// use default menu icon for take-away
						$menu->image = VREASSETS_ADMIN_URI . 'images/product-placeholder.png';
					}
				}
				else
				{
					// use menu image URI
					$menu->image = VREMEDIA_URI . $menu->image;
				}

				$displayData = [];
				$displayData['image']   = $menu->image;
				$displayData['primary'] = $menu->name;
				
				?>
				<div class="vre-card-fieldset">
					<?php echo $cardLayout->render($displayData); ?>
				</div>
				<?php
			}
			?>

		</div>
		<?php
	}
	?>
</div>
