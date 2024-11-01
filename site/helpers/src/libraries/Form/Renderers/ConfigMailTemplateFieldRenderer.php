<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Form\Renderers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Form\FormFieldRendererLayout;

/**
 * Wraps the mail template dropdown within a box to support inline editing and preview.
 * 
 * @since 1.9
 */
class ConfigMailTemplateFieldRenderer extends FormFieldRendererLayout
{
	/** @var string */
	protected $layoutId = 'form.wrapper.configuration.mailtmpl';

	/**
	 * The element identifier.
	 * 
	 * @var string
	 */
	protected $id;

	/**
	 * The e-mail template alias.
	 * 
	 * @var string
	 */
	protected $alias;

	/**
	 * Class constructor.
	 */
	public function __construct(string $id, string $alias)
	{
		$this->id    = $id;
		$this->alias = $alias;
	}

	/**
	 * @inheritDoc
	 */
	protected function getDisplayData($data, $input)
	{
		// detect group
		$group = substr($this->id, 0, 2) !== 'tk' ? 'restaurant' : 'takeaway';

		return [
			'input' => $input,
			'id'    => $this->id,
			'alias' => $this->alias,
			'group' => $group,
			'path'  => VREHELPERS . DIRECTORY_SEPARATOR . ($group === 'restaurant' ? 'mail_tmpls' : 'tk_mail_tmpls') . DIRECTORY_SEPARATOR,
		];
	}
}
