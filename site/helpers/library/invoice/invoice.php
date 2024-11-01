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
 * Abstract class used to implement the common functions
 * that will be invoked to generate and send invoices.
 *
 * @since 1.8
 * @deprecated 1.10  Use the E4J\VikRestaurants\Invoice\InvoiceTemplate instead.
 */
class_alias('E4J\\VikRestaurants\\Invoice\\InvoiceTemplate', 'VREInvoice');
