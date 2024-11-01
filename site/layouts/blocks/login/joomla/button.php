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

$button = $displayData['button'];

// register in a new array all the attributes that start with "data-"
$dataAttributeKeys = array_filter(array_keys($button), function ($key)
{
	return substr($key, 0, 5) == 'data-';
});

?>

<button type="button"
	class="vre-btn primary<?php echo !empty($button['class']) ? ' ' . $button['class'] : ''; ?>"
	<?php
	foreach ($dataAttributeKeys as $key)
	{
		echo $key . '="' . $this->escape($button[$key]) . '" ';
	}
	if ($button['onclick'])
	{
		?>
		onclick="<?php echo $this->escape($button['onclick']); ?>"
		<?php
	}
	?>
	title="<?php echo $this->escape(JText::translate($button['label'])); ?>"
	id="<?php echo $this->escape($button['id']); ?>"
>
	<?php
	if (!empty($button['icon']))
	{
		?>
		<span class="<?php echo $this->escape($button['icon']); ?>"></span>
		<?php
	}
	else if (!empty($button['image']))
	{
		echo $button['image'];
	}
	else if (!empty($button['svg']))
	{
		echo $button['svg'];
	}
	
	echo JText::translate($button['label']);
	?>
</button>