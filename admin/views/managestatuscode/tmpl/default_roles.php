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

$status = $this->status;

?>

<!-- APPROVED - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'        => 'checkbox',
	'name'        => 'approved',
	'checked'     => $status->approved,
	'label'       => JText::translate('VRSTATUSCODEROLE_APPROVED'),
	'description' => JText::translate('VRSTATUSCODEROLE_APPROVED_HELP'),
]);
?>

<!-- RESERVED - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'        => 'checkbox',
	'name'        => 'reserved',
	'checked'     => $status->reserved,
	'label'       => JText::translate('VRSTATUSCODEROLE_RESERVED'),
	'description' => JText::translate('VRSTATUSCODEROLE_RESERVED_HELP'),
]);
?>

<!-- EXPIRED - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'        => 'checkbox',
	'name'        => 'expired',
	'checked'     => $status->expired,
	'label'       => JText::translate('VRSTATUSCODEROLE_EXPIRED'),
	'description' => JText::translate('VRSTATUSCODEROLE_EXPIRED_HELP'),
]);
?>

<!-- CANCELLED - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'        => 'checkbox',
	'name'        => 'cancelled',
	'checked'     => $status->cancelled,
	'label'       => JText::translate('VRSTATUSCODEROLE_CANCELLED'),
	'description' => JText::translate('VRSTATUSCODEROLE_CANCELLED_HELP'),
]);
?>

<!-- PAID - Checkbox -->

<?php
echo $this->formFactory->createField([
	'type'        => 'checkbox',
	'name'        => 'paid',
	'checked'     => $status->paid,
	'label'       => JText::translate('VRSTATUSCODEROLE_PAID'),
	'description' => JText::translate('VRSTATUSCODEROLE_PAID_HELP'),
]);
?>
