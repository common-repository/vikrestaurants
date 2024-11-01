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
 * Class used to wrap the PDF constraints.
 *
 * @since 1.4
 * @deprecated 1.10  Use E4J\VikRestaurants\PDF\PDFConstraints instead.
 */
class_alias('E4J\\VikRestaurants\\PDF\\PDFConstraints', 'VikRestaurantsConstraintsPDF');
