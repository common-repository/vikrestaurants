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
	// the credit card details have been manually removed
	echo $this->formFactory->createField()
		->type('alert')
		->style('success')
		->hiddenLabel(true)
		->text(JText::translate('VRCREDITCARDREMOVED'));
	?>
</div>
