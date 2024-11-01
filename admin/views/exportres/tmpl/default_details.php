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

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('filename')
	->label(JText::translate('VREXPORTRES1'));
?>

<!-- EXPORT DRIVER - Select -->

<?php
$options = [
	JHtml::fetch('select.option', '', '')
];

foreach ($this->drivers as $k => $v)
{
	$options[] = JHtml::fetch('select.option', $k, $v);
}

echo $this->formFactory->createField()
	->type('select')
	->name('driver')
	->id('vr-driver-sel')
	->required(true)
	->label(JText::translate('VREXPORTRES2'))
	->options($options);
?>

<!-- DATE FROM - Calendar -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('fromdate')
	->value($this->data->fromdate)
	->label(JText::translate('VREXPORTRES3'));
?>

<!-- DATE TO - Calendar -->

<?php
echo $this->formFactory->createField()
	->type('date')
	->name('todate')
	->value($this->data->todate)
	->label(JText::translate('VREXPORTRES4'));
?>

<?php
JText::script('VRE_FILTER_SELECT_DRIVER');
?>

<script>
	(function($, w) {
		'use strict';

		$(function() {
			$('#vr-driver-sel').select2({
				placeholder: Joomla.JText._('VRE_FILTER_SELECT_DRIVER'),
				allowClear: false,
				width: '90%',
			}).on('change', w.vrExportDriverChanged);
		});
	})(jQuery, window);
</script>