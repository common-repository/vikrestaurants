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
 * @var  string   $text     The text to display within the badge.
 * @var  string   $type     The badge type.
 * @var  array    $attrs    A list of field attributes.
 */
extract($displayData);

// fetch attributes string
$attrs_str = '';

foreach ($attrs as $k => $v)
{
	if ($k != 'class')
	{
		$attrs_str .= ' ' . $k;

		if (!is_bool($v))
		{
			$attrs_str .= ' = "' . $this->escape($v) . '"';
		}
	}
}

?>

<span class="badge<?php echo $type . (!empty($attrs['class']) ? ' badge-' . $attrs['class'] : ''); ?>"<?php echo $attrs_str; ?>>
	<?php echo $text; ?>
</span>
