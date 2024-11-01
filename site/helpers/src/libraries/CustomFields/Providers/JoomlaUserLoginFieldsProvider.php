<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\CustomFields\Providers;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\CustomFields\Field;
use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;

/**
 * Creates a fields collection useful for the user login.
 * 
 * @since 1.9
 */
class JoomlaUserLoginFieldsProvider extends ExtendableFieldsProvider
{
	/** @var array */
	protected $options;

	/**
	 * Class constructor.
	 * 
	 * @param  array                $options
	 * @param  DispatcherInterface  $dispatcher
	 */
	public function __construct(array $options = [], DispatcherInterface $dispatcher = null)
	{
		$this->options = $options;

		parent::__construct($dispatcher);
	}

	/**
	 * @inheritDoc
	 */
	public function getData()
	{
		$fields = [];

		// "top" position
		$this->extendFields($fields, 'onDisplayUserLoginForm', ['top']);

		// set up username
		$fields[] = Field::getInstance([
			'name'     => 'username',
			'type'     => 'text',
			'langname' => \JText::translate('VRLOGINUSERNAME'),
			'required' => true,
		]);

		// "username" position
		$this->extendFields($fields, 'onDisplayUserLoginForm', ['username']);

		// set up password
		$fields[] = Field::getInstance([
			'name'     => 'password',
			'type'     => 'password',
			'langname' => \JText::translate('VRLOGINPASSWORD'),
			'required' => true,
		]);

		// "password" position
		$this->extendFields($fields, 'onDisplayUserLoginForm', ['password']);

		if (!empty($this->options['remember']))
		{
			// auto-remember user
			$fields[] = Field::getInstance([
				'type'  => 'hidden',
				'name'  => 'remember',
				'value' => 'yes',
			]);
		}
		else
		{
			// ask confirmation
			$fields[] = Field::getInstance([
				'type'     => 'checkbox',
				'name'     => 'remember',
				'value'    => 'yes',
				'langname' => \JText::translate('COM_USERS_LOGIN_REMEMBER_ME'),
			]);
		}

		// "remember" position
		$this->extendFields($fields, 'onDisplayUserLoginForm', ['remember']);

		/**
		 * Get additional login buttons to add in a login module. These buttons can be used for
		 * authentication methods external to Joomla such as WebAuthn, login with social media
		 * providers, login with third party providers or even login with third party Single Sign On
		 * (SSO) services.
		 * 
		 * @since 1.9
		 */
		if (class_exists('Joomla\\CMS\\Helper\\AuthenticationHelper') && method_exists('Joomla\\CMS\\Helper\\AuthenticationHelper', 'getLoginButtons'))
		{
			$extraButtons = \Joomla\CMS\Helper\AuthenticationHelper::getLoginButtons($this->options['formId'] ?? 'vrloginform');
		}
		else
		{
			$extraButtons = [];
		}

		// iterate all buttons fetched by third-party plugins
		foreach ($extraButtons as $button)
		{
			$fields[] = Field::getInstance([
				'type' => 'html',
				'name' => 'joomla-button',
				'html' => \JLayoutHelper::render('blocks.login.joomla.button', ['button' => $button]),
			]);
		}

		return $fields;
	}
}
