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
 * @var   boolean  $pro  True if there is an active PRO license.
 */
extract($displayData);

?>

<div class="license-box custom <?php echo $pro ? 'is-pro' : 'get-pro'; ?>">
	
	<?php
	if (!$pro)
	{
		?>
			<a href="admin.php?page=vikrestaurants&view=gotopro">
				<i class="fas fa-rocket"></i>
				<span><?php echo JText::translate('VREGOTOPROBTN'); ?></span>
			</a>
		<?php
	}
	else
	{
		?>
		<a href="admin.php?page=vikrestaurants&view=gotopro">
			<i class="fas fa-trophy"></i>
			<span><?php echo JText::translate('VREISPROBTN'); ?></span>
		</a>
		<?php
	}
	?>

</div>