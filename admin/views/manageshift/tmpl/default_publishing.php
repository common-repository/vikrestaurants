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

$shift = $this->shift;

?>

<!-- WEEK DAYS - Checkbox -->

<?php
$date = new JDate();

for ($i = 1; $i <= 7; $i++)
{
    $wd = $i == 7 ? 0 : $i;

    echo $this->formFactory->createField()
        ->type('checkbox')
        ->name('days')
        ->value($wd)
        ->checked(in_array($wd, $shift->days))
        ->multiple(true)
        ->label($date->dayToString($wd));
}
?>
