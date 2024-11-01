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
 * The APIs response wrapper.
 *
 * @since  1.7
 * @deprecated 1.11  Use E4J\VikRestaurants\API\Response instead.
 */
class ResponseAPIs extends E4J\VikRestaurants\API\Response
{
    /**
     * Copies the information registered here into the provided response.
     * 
     * @param   E4J\VikRestaurants\API\Response  $response
     * 
     * @return  void
     * 
     * @since   1.9
     */
    public function copy(E4J\VikRestaurants\API\Response $response)
    {
        $response->setStatus($this->isVerified());
        $response->setContent($this->getContent());
        $response->setPayload($this->getPayload());
        $response->setContentType($this->getContentType());
    }
}
