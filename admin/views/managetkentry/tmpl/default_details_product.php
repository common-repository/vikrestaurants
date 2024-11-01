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

$entry = $this->entry;

?>

<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($entry->name)
	->class('input-xxlarge input-large-text')
	->required(true)
	->label(JText::translate('VRMANAGETKMENU4'));
?>

<!-- ALIAS - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('alias')
	->value($entry->alias)
	->label(JText::translate('JFIELD_ALIAS_LABEL'));
?>

<!-- IMAGE - Media -->

<?php
echo $this->formFactory->createField()
	->type('media')
	->name('img_path')
	->value($entry->images)
	->multiple(true)
	->label(JText::translate('VRMANAGETKMENU16'));
?>

<!-- TAKE-AWAY MENU - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('id_takeaway_menu')
	->value($entry->id_takeaway_menu)
	->label(JText::translate('VRMANAGETKMENU15'))
	->options(JHtml::fetch('vikrestaurants.takeawaymenus'));
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('select[name="id_takeaway_menu"]').select2({
				allowClear: false,
				width: 300,
			});
		});
	})(jQuery);
</script>