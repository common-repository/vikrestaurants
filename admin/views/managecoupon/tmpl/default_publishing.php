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

$coupon = $this->coupon;

?>

<!-- DATE START - Calendar -->

<?php
echo $this->formFactory->createField()
    ->type('date')
    ->name('start_publishing')
    ->value(E4J\VikRestaurants\Helpers\DateHelper::sql2date($coupon->start_publishing))
    ->label(JText::translate('VRMANAGECOUPON5'))
    ->attributes([
        'showTime' => true,
    ]);
?>

<!-- DATE END - Calendar -->

<?php
echo $this->formFactory->createField()
    ->type('date')
    ->name('end_publishing')
    ->value(E4J\VikRestaurants\Helpers\DateHelper::sql2date($coupon->end_publishing))
    ->label(JText::translate('VRMANAGECOUPON6'))
    ->attributes([
        'showTime' => true,
    ]);
?>
