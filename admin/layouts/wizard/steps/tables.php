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
 * Layout variables
 * -----------------
 * @var  WizardStep  $step  The wizard step instance.
 */
extract($displayData);

if (!$step->isCompleted())
{
	// go ahead only if completed
	return;
}
?>

<ul class="wizard-step-summary">
	<?php
	$tables = $step->getTables();

	// display at most 4 tables
	for ($i = 0; $i < min(array(4, count($tables))); $i++)
	{
		?>
		<li>
			<i class="fas fa-<?php echo $tables[$i]->published ? 'check-circle ok' : 'dot-circle no'; ?>"></i>
			<b><?php echo $tables[$i]->name; ?></b>
		</li>
		<?php
	}

	// count remaining tables
	$remaining = count($tables) - 5;

	if ($remaining > 0)
	{
		?>
		<li><?php echo JText::plural('VRWIZARDOTHER_N_ITEMS', $remaining); ?></li>
		<?php
	}
	?>
</ul>
