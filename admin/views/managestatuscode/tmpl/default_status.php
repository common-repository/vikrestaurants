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

JHtml::fetch('vrehtml.assets.colorpicker');

$status = $this->status;

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField([
	'type'     => 'text',
	'name'     => 'name',
	'class'    => 'input-xxlarge input-large-text',
	'value'    => $status->name,
	'required' => true,
	'label'    => JText::translate('VRMANAGELANG2'),
]);
?>

<!-- CODE - Text -->

<?php
echo $this->formFactory->createField([
	'type'        => 'text',
	'name'        => 'code',
	'value'       => $status->code,
	'required'    => true,
	'label'       => JText::translate('VRMANAGERESCODE2'),
	'description' => JText::translate('VRSTATUSCODECODE_HELP'),
]);
?>

<!-- COLOR - Colorpicker -->

<?php
echo $this->formFactory->createField([
	'type'  => 'color',
	'name'  => 'color',
	'value' => $status->color ?: JHtml::fetch('vrehtml.color.preset'),
	'label' => JText::translate('VRE_UISVG_COLOR'),
	'preview' => true,
]);
?>
