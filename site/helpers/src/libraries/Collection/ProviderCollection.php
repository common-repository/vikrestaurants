<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Collection;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * @inheritDoc
 */
class ProviderCollection extends ObjectsCollection
{
	/**
	 * Class constructor.
	 * 
	 * @param  DatasetProvider  $provider  The provider used to inject the objects.
	 */
	public function __construct(DatasetProvider $provider)
	{
		parent::__construct($provider->getData());
	}
}
