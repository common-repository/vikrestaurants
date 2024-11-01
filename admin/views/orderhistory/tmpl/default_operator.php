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

$rows = $this->rows;

$vik = VREApplication::getInstance();

if (count($rows) == 0)
{
	?>
	<div style="margin: 10px;">
		<?php echo $vik->alert(JText::translate('JGLOBAL_NO_MATCHING_RESULTS')); ?>
	</div>
	<?php
}
else
{
	// create log details layout
	$layout = new JLayoutFile('blocks.operatorlog');

	foreach ($rows as $row)
	{
		// set current log for being used in sub-layout
		$data = [
			'log'         => $row,
			'reservation' => false, // hide reservation badge
		];

		echo $layout->render($data);
	}
}
