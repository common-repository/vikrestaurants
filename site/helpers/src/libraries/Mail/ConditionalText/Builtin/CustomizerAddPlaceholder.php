<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\ConditionalText\Builtin;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\ConditionalText\ConditionalText;
use E4J\VikRestaurants\Mail\ConditionalText\Factory;

/**
 * Built-in conditional text used to append a button at the end of each supported
 * position of the body action.
 *
 * @since 1.9
 */
class CustomizerAddPlaceholder extends ConditionalText
{
    /**
     * inheritDoc
     */
    public function __construct()
    {
        /** @var E4J\VikRestaurants\Mail\ConditionalText\Actions\BodyAction */
        $body = Factory::getInstance()->getAction('body');

        $data = [
            'actions' => [],
        ];

        // iterate all the supported positions
        foreach ($body->getSupportedPositions() as $position)
        {
            // register an action for each supported position
            $data['actions'][] = [
                'id' => $body->getID(),
                'options' => [
                    'position' => $position,
                    'text' => '<a href="javascript:void(0)" class="customizer-conditional-text-add" data-position="' . $position . '"></a>',
                ],
            ];
        }

        // construct through parent
        parent::__construct($data);
    }
}
