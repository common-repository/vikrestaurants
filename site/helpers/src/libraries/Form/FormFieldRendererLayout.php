<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Form;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Form field renderer aware to support layout files.
 * 
 * @since 1.9
 */
abstract class FormFieldRendererLayout implements FormFieldRenderer
{
	/** @var string */
	protected $layoutId;

	/**
	 * @inheritDoc
	 */
	public function render($data, $input)
	{
		if (!$this->layoutId)
		{
			throw new \RuntimeException('Missing layout ID in ' . get_class($this), 400);
		}

		// render layout file
		$output = \JLayoutHelper::render(
			// use the specified ID
			$this->layoutId,
			// let the implementor prepares the display data
			$this->getDisplayData($data, $input)
		);

		if (!$output)
		{
			// no output, layout file missing
			throw new \RuntimeException('Field renderer [' . $this->layoutId . '] layout not found', 404);
		}

		return $output;
	}

	/**
	 * Prepares the data to be injected within the renderer layout file.
	 * 
	 * @param   \JObject  $data   The field display data registry.
	 * @param   string    $input  The default rendered input, if any.
	 * 
	 * @return  array
	 */
	abstract protected function getDisplayData($data, $input);
}
