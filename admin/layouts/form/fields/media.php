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

$name       = isset($displayData['name'])       ? $displayData['name']       : '';
$value      = isset($displayData['value'])      ? $displayData['value']      : 1;
$id         = isset($displayData['id'])         ? $displayData['id']         : null;
$multiple   = isset($displayData['multiple'])   ? $displayData['multiple']   : null;
$attributes = isset($displayData['attributes']) ? $displayData['attributes'] : [];

if (!is_null($multiple))
{
    $attributes['multiple'] = (bool)  $multiple;
}

echo JHtml::fetch('vrehtml.mediamanager.field', $name, $value, $id, $attributes);
