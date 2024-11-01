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

$id          = isset($displayData['id'])          ? $displayData['id']          : '';
$class       = isset($displayData['class'])       ? $displayData['class']       : '';
$style       = isset($displayData['style'])       ? $displayData['style']       : null;
$text        = isset($displayData['text'])        ? $displayData['text']        : '';
$dismissible = isset($displayData['dismissible']) ? $displayData['dismissible'] : false;
$attributes  = isset($displayData['attributes'])  ? $displayData['attributes']  : [];

if ($id)
{
    $attributes['id'] = $id;
}

if ($class)
{
    $attributes['class'] = (!empty($attributes['class']) ? $attributes['class'] . ' ' : '') . $class;
}

echo VREApplication::getInstance()->alert($text, $style, $dismissible, $attributes);
