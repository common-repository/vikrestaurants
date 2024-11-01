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

<!-- TABLES CLUSTER - Select -->

<?php
echo $this->formFactory->createField()
	->type('select')
	->name('cluster')
	->value($table->cluster)
	->id('vr-cluster-sel')
	->multiple(true)
	->disabled($table->multi_res)
	->hiddenLabel(true)
	->description(JText::translate('VRMANAGETABLE13_DESC'))
	->options($this->allTables[$table->id_room] ?? []);
?>

<script>

	(function($) {
		'use strict';

		$(function() {
			$('#vr-cluster-sel').select2({
				allowClear: false,
				width: '100%',
			});

			$('select[name="id_room"]').on('change', function() {
				// get all room tables
				const tables = <?php echo json_encode($this->allTables); ?>;
				const room   = $(this).val();

				const list = tables.hasOwnProperty(room) ? tables[room] : [];

				let html = '';

				for (let i = 0; i < list.length; i++) {
					html += '<option value="' + list[i].value + '">' + list[i].text + '</option>\n';
				}

				$('#vr-cluster-sel').html(html).select2('val', []);
			});
		});
	})(jQuery);

</script>
