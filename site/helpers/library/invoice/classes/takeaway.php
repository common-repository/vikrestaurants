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
 * Class used to generate the invoices of the take-away orders.
 *
 * @since 1.6
 * @deprecated 1.10  Use E4J\VikRestaurants\Invoice\Templates\TakeawatInvoiceTemplate instead.
 */
class_alias('E4J\\VikRestaurants\\Invoice\\Templates\\TakeawatInvoiceTemplate', 'VREInvoiceTakeaway');