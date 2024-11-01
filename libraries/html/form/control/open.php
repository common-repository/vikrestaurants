<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.form
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $class        The control class.
 * @var   string   $label        The field label.
 * @var   string   $description  An optional field description.
 * @var   string   $id           The field ID.
 * @var   string   $idparent     The control ID.
 * @var   boolean  $required     True if the field is required.
 * @var   array    $attr         An array of attributes.
 */

$idparent = isset($idparent) ? $idparent : '';
$attr     = isset($attr)     ? $attr     : '';
$class    = isset($class)    ? $class    : '';

$label = JText::translate($label);

// check if the label ends with a "colon"
$has_colon = preg_match("/:$/", $label);

// remove trailing "colon" if already specified by the translation
$label = rtrim($label, ':');
// remove trailing "*" if already specified by the translation (only if required)
$label = $required ? rtrim($label, '*') : $label;

// Add '*' and ':' only if there is plain text.
// For example, in case of <a><i></i></a>, we don't
// need to proceed.
if ($label && strip_tags($label))
{
	$label .= ($required ? '*' : '') . ($has_colon ? ':' : '');
}

?>
<div class="control<?php echo $class ? ' ' . $class : ''; ?>"<?php echo ($idparent ? ' id="' . $idparent . '"' : ''); ?><?php echo $attr; ?>>
	<label
		<?php echo $id ? 'for="' . esc_attr($id) . '"' : ''; ?>
	><?php echo $label; ?></label>
	<div class="control-value">
