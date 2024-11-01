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

$name       = !empty($displayData['name'])      ? $displayData['name']       : '';
$id         = !empty($displayData['id'])        ? $displayData['id']         : $name;
$value      = isset($displayData['value'])      ? $displayData['value']      : '';
$class      = isset($displayData['class'])      ? $displayData['class']      : '';
$multiple   = isset($displayData['multiple'])   ? $displayData['multiple']   : false;
$disabled   = isset($displayData['disabled'])   ? $displayData['disabled']   : false;
$options    = isset($displayData['options'])    ? $displayData['options']    : [];
$attributes = isset($displayData['attributes']) ? $displayData['attributes'] : [];

if ($multiple)
{
	$value = (array) $value;
}

if ($multiple)
{
	$attributes['multiple'] = true;
}

if ($disabled)
{
	$attributes['disabled'] = true;
}

if ($class)
{
	$attributes['class'] = $class;
}

echo JHtml::fetch(
	'select.groupedList',
	$options,
	$name,
	[
		'id'          => $id,
		'list.attr'   => $attributes,
		'group.items' => null,
		'list.select' => $value,
	]
);
