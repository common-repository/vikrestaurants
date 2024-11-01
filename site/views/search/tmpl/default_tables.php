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

/**
 * Template file used to display the tables map.
 *
 * @since 1.8
 */

$room_has_shared_table = (bool) array_filter($this->selectedRoom->tables, function($t)
{
	// return true if the table is shared and available
	return $t->multi_res && $t->available;
});

if ($room_has_shared_table)
{
	// shows shared table legend
	?>
	<div id="vrlegendsharedtablediv">
		<?php echo JText::translate('VRLEGENDSHAREDTABLE'); ?>
	</div>
	<?php
}

/**
 * Display new map layout using SVG factory.
 *
 * @since 1.7.4
 */
?>
	
<div id="vre-tables-map" class="vre-map-svg-wrapper">
	<?php
	VRELoader::import('library.map.factory');

	$options = [];
	$options['callback'] = 'selectTable';
	
	echo VREMapFactory::getInstance($options)
		->setRoom($this->selectedRoom)
		->setTables($this->selectedRoom->tables)
		->build();
	?>
</div>

<div class="vryourtablediv">
	<span id="vrbooknoselsp" style="display: none;"></span>
	<span id="vrbooktabselsp" style="display: none;"></span>
</div>

<?php
JText::script('VRYOURTABLESEL');
?>

<script>
	(function($, w) {
		'use strict';

		w.selectTable = (id, tableName, tableAvailable) => {
			if (tableAvailable == 1) {
				// check if a table was already selected
				let wasSelected = w.SELECTED_TABLE ? true : false;

				w.SELECTED_TABLE = id;
		
				$('#vrbooknoselsp').hide();
		
				$('#vrbooktabselsp').text(Joomla.JText._('VRYOURTABLESEL').replace('%s', tableName));
				$('#vrbooktabselsp').fadeIn('normal');
				// $('#vrbooktabselsp').show();

				if (!wasSelected) {
					// animate only for the first time
					$('html,body').animate({
						scrollTop: $('#vre-search-continue-btn').offset().top - 300,
					}, {
						duration: 'slow',
					});
				}
			}
		}
	})(jQuery, window);
</script>