<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Database\Exception;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Psr\Container\NotFoundExceptionInterface;

/**
 * No entry was found in the database container.
 * 
 * @since 1.9
 */
class DatabaseEntryNotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{

}
