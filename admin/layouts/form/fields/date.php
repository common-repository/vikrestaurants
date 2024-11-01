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

$name       = !empty($displayData['name'])      ? $displayData['name']       : 'name';
$id         = !empty($displayData['id'])        ? $displayData['id']         : $name;
$value      = isset($displayData['value'])      ? $displayData['value']      : '';
$class      = isset($displayData['class'])      ? $displayData['class']      : '';
$attributes = isset($displayData['attributes']) ? $displayData['attributes'] : [];

/**
 * Old attributes notation.
 * 
 * @deprecated 1.10 Use "attributes" instead.
 */
$attrs = isset($displayData['attrs']) ? $displayData['attrs'] : [];

if ($attrs)
{
	// merge old attributes with the new ones
	$attributes = array_merge($attrs, $attributes);
}

if ($class)
{
	if (!empty($attributes['class']))
	{
		$attributes['class'] .= ' ' . $class;
	}
	else
	{
		$attributes['class'] = $class;
	}
}

$vik = VREApplication::getInstance();

echo $vik->calendar($value, $name, $id, $format = null, $attributes);
