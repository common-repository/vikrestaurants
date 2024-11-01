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
 * Creates a fields collection useful for the user registration.
 * 
 * @since 1.9
 */
class UserRegisterFieldsProvider extends ExtendableFieldsProvider
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
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['top']);

		// set up first name
		$fields[] = Field::getInstance([
			'name'     => 'firstname',
			'id'       => 'register_first_name',
			'type'     => 'text',
			'rule'     => 'nominative',
			'langname' => \JText::translate('VRREGNAME'),
			'required' => true,
		]);

		// set up last name
		$fields[] = Field::getInstance([
			'name'     => 'lastname',
			'id'       => 'register_last_name',
			'type'     => 'text',
			'rule'     => 'nominative',
			'langname' => \JText::translate('VRREGLNAME'),
			'required' => true,
		]);

		// "name" position
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['name']);

		// set up username
		$fields[] = Field::getInstance([
			'name'     => 'username',
			'id'       => 'register_username',
			'type'     => 'text',
			'langname' => \JText::translate('VRREGUNAME'),
			'required' => true,
		]);

		// "username" position
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['username']);

		// set up e-mail
		$fields[] = Field::getInstance([
			'name'     => 'email',
			'id'       => 'register_email',
			'type'     => 'text',
			'rule'     => 'email',
			'langname' => \JText::translate('VRREGEMAIL'),
			'required' => true,
		]);

		// "email" position
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['email']);

		// set up password
		$fields[] = Field::getInstance([
			'name'     => 'password',
			'id'       => 'register_password',
			'type'     => 'password',
			'langname' => \JText::translate('VRREGPWD'),
			'required' => true,
		]);

		// set up confirm password
		$fields[] = Field::getInstance([
			'name'     => 'confpassword',
			'id'       => 'register_conf_password',
			'type'     => 'password',
			'langname' => \JText::translate('VRREGCONFIRMPWD'),
			'required' => true,
		]);

		// "password" position
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['password']);

		if (!empty($this->options['gdpr']))
		{
			/**
			 * Translate setting to support different URLs for several languages.
			 *
			 * @since 1.8
			 */
			$policy = \VikRestaurants::translateSetting('policylink');

			// set up privacy policy link
			$fields[] = Field::getInstance([
				'id'       => 'register_gdpr',
				'name'     => 'gdpr',
				'type'     => 'checkbox',
				'langname' => \JText::translate('GDPR_POLICY_AUTH_NO_LINK'),
				'required' => 1,
				'poplink'  => $policy,
			]);
		}

		// "policy" position
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['policy']);

		if (!empty($this->options['captcha']))
		{
			// set up captcha
			$fields[] = Field::getInstance([
				'type' => 'html',
				'name' => 'captcha', 
				'html' => \VREApplication::getInstance()->reCaptcha(),
				'hiddenLabel' => true,
			]);
		}

		// "captcha" position
		$this->extendFields($fields, 'onDisplayUserRegistrationForm', ['captcha']);

		return $fields;
	}
}
