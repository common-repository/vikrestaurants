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

$table = $this->table;

?>
				
<!-- NAME - Text -->

<?php
echo $this->formFactory->createField()
	->type('text')
	->name('name')
	->value($table->name)
	->label(JText::translate('VRMANAGETABLE1'))
	->required(true)
	->class('input-xxlarge input-large-text');
?>

<!-- ROOM - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('id_room')
	->value($table->id_room)
	->label(JText::translate('VRMANAGETABLE4'))
	->required(true)
	->options($this->rooms);
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('select[name="id_room"]').select2({
				allowClear: false,
				width: '100%',
			});
		});
	})(jQuery);
</script>