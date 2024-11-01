<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.wizard
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Layout variables
 * -----------------
 * @var  VREWizardStep  $step  The wizard step instance.
 */
extract($displayData);

$shortcodes = $step->getShortcodes();

if (!count($shortcodes))
{
	// go ahead only in case of shortcodes
	return;
}

$vik = VREApplication::getInstance();

?>

<ul class="wizard-step-summary">
	<?php
	// display at most 4 shortcodes
	for ($i = 0; $i < min(array(4, count($shortcodes))); $i++)
	{
		?>
		<li>
			<b><?php echo $shortcodes[$i]->name; ?></b>
			<span class="badge badge-important"><?php echo JText::translate($shortcodes[$i]->title); ?></span>
		</li>
		<?php
	}

	// count remaining shortcodes
	$remaining = count($shortcodes) - 5;

	if ($remaining > 0)
	{
		?>
		<li><?php echo JText::plural('VRWIZARDOTHER_N_ITEMS', $remaining); ?></li>
		<?php
	}
	?>
</ul>

<?php
if ($step->needShortcode('restaurant'))
{
	echo $vik->alert(__('Define a shortcode also for the restaurant section.', 'vikrestaurants'));
}
else if ($step->needShortcode('takeaway'))
{
	echo $vik->alert(__('Define a shortcode also for the take-away section.', 'vikrestaurants'));
}
