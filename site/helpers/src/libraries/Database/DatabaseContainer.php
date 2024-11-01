<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Database;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Psr\Container\ContainerInterface;
use E4J\VikRestaurants\Psr\Container\NotFoundExceptionInterface;

/**
 * Describes the interface of a container able to fetch data from a database.
 * 
 * @since 1.9
 */
abstract class DatabaseContainer implements ContainerInterface
{
    /** @var JDatabaseDriver */
    protected $db;

    /**
     * Class constructor.
     * 
     * @param  JDatabaseDriver  $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function has(string $id)
    {
        try
        {
            $this->get($id);
        }
        catch (NotFoundExceptionInterface $e)
        {
            // entry not found
            return false;
        }

        return true;
    }
}
