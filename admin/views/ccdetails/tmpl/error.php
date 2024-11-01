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

<div style="padding: 10px;">
	<?php
	// the order doesn't exist or doesn't have credit card details
	echo $this->formFactory->createField()
		->type('alert')
		->style('error')
		->hiddenLabel(true)
		->text(JText::translate('JGLOBAL_NO_MATCHING_RESULTS'));
	?>
</div>
