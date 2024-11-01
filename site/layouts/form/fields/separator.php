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

$label       = isset($displayData['label']) ? $displayData['label'] : '';
$description = isset($displayData['description']) ? $displayData['description'] : '';
$id          = !empty($displayData['id'])   ? $displayData['id']    : '';
$class       = isset($displayData['class']) ? $displayData['class'] : '';

if (!preg_match("/\bcustom-field\b/", (string) $class))
{
    // add "custom-field" class if missing
    $class = 'custom-field ' . $class;
}

if (!preg_match("/\bseparator\b/", (string) $class))
{
    // add "separator" class if missing
    $class = 'separator ' . $class;
}
?>

<div class="vrseparatorcf <?php echo $this->escape(trim($class)); ?>" <?php echo $id ? 'id="' . $this->escape($id) . '"' : ''; ?>>
    <?php echo $description ?: $label; ?>
</div>
