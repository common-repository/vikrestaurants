<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\API;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\API\Exception\BlockedUserException;
use E4J\VikRestaurants\API\Exception\EventNotAuthorizedException;
use E4J\VikRestaurants\API\Exception\EventNotFoundException;
use E4J\VikRestaurants\API\Exception\InvalidEventException;
use E4J\VikRestaurants\API\Exception\InvalidUserCredentialsException;
use E4J\VikRestaurants\API\Exception\UserAuthenticationException;
use E4J\VikRestaurants\DI\Container;

/**
 * The API abstract framework.
 * This class is used to run all the registered plugins.
 *
 * All the events are runnable only if the user is correctly authenticated.
 *
 * @see User
 * @see Response
 * @see Error
 * @see Event
 *
 * @since 1.9
 */
abstract class API
{
	/**
	 * The object holding all the registered events.
	 * 
	 * @var Container
	 */
	private $container;

	/**
	 * The engine used to look for blocked users.
	 * 
	 * @var BlockEngine
	 */
	private $blockEngine;

	/**
	 * True if the API framework is enabled and accessible.
	 *
	 * @var bool
	 */
	private $enabled = true;	

	/**
	 * The instance of the user which is using the API framework.
	 *
	 * @var User
	 */
	private $user = null;

	/**
	 * The last error caught.
	 *
	 * @var Error
	 */
	private $error = null;

	/**
	 * The array that contains the API configuration.
	 *
	 * @var array
	 */
	private $config;

	/**
	 * Flag used to avoid sending the headers while outputting the event data.
	 *
	 * @var bool
	 */
	protected $sendHeaders = true;

	/**
	 * The instance of the API framework.
	 *
	 * @var API
	 */
	protected static $instance = null;

	/**
	 * Class constructor.
	 * 
	 * @param  Container    $container    The container holding the registered event providers.
	 * @param  BlockEngine  $blockEngine  The engine used to look for banned users.
	 * @param  array        $config       The API framework configuration.
	 */
	public function __construct(Container $container, BlockEngine $blockEngine, array $config = [])
	{
		$this->container   = $container;
		$this->blockEngine = $blockEngine;
		$this->config      = $config;
	}

	/**
	 * Returns the container holding all the registered events.
	 * @protected it is not possible to directly access the container
	 * from the outside.
	 * 
	 * @return  Container
	 */
	final protected function getContainer()
	{
		return $this->container;
	}

	/**
	 * Registers a new event provider for lazy initialization.
	 * 
	 * @param   string    $event     The event name.
	 * @param   callable  $provider  The provider callback.
	 * @param   array     $options   An array of preferences to choose whether the event
	 *                               resource should be `shared` or `protected`.
	 * 
	 * @return  self      This object to support chaining.
	 */
	final public function registerEventProvider(string $event, $provider, array $options = [])
	{
		$options = new \JObject($options);

		/** @var E4J\VikRestaurants\DI\Resource */
		$this->container->set($event, $provider)
			->share($options->get('shared', false))
			->protect($options->get('protected', false));

		return $this;
	}

	/**
	 * Returns true if the APIs framework is enabled and accessible.
	 *
	 * @return  bool  True if enabled, otherwise false.
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Enables the API framework.
	 *
	 * @return  self  This object to support chaining.
	 */
	protected function enable()
	{
		$this->enabled = true;

		return $this;
	}

	/**
	 * Disables the API framework.
	 *
	 * @return  self  This object to support chaining.
	 */
	protected function disable()
	{
		$this->enabled = false;

		return $this;
	}

	/**
	 * Returns true if the user is correctly logged.
	 *
	 * @return  bool  True if logged, otherwise false.
	 */
	public function isConnected()
	{
		return $this->user !== null && $this->user->id();
	}

	/**
	 * Returns the object of the logged user.
	 *
	 * @return  User  The object of the user connected, otherwise NULL.
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Disconnects the user.
	 *
	 * @return  self  This object to support chaining.
	 */
	public function disconnect()
	{
		$this->user = null;

		return $this;
	}

	/**
	 * Connects the specified user to the API framework.
	 *
	 * In case the login fails, here is evaluated a permanent BAN.
	 * Otherwise the MANIFEST of the user is updated and the BAN is reset.
	 *
	 * This method can raise the following internal errors:
	 * - 100 = Authentication Error (Generic)
	 * - 101 = The username is empty or invalid
	 * - 102 = The password is empty or invalid
	 * - 104 = The account is blocked
	 *
	 * @param   User  $user  The object to represent the user login.
	 *
	 * @return  bool  True if the user is accepted, otherwise false.
	 */
	public function connect(User $user)
	{
		// check if API framework is enabled
		if (!$this->isEnabled())
		{
			// do not log anything and stop flow
			return false;
		}

		$banned = false;

		try
		{
			// make sure the user is not banned
			if ($banned = $this->blockEngine->isBanned($user))
			{
				// user banned, do not proceed further
				throw new BlockedUserException($user);
			}

			// make sure the user credentials are ok
			if (!$user->isConnectable())
			{
				// username and/or password missing
				throw new InvalidUserCredentialsException($user);
			}

			// try to perform the connection
			if (!$this->doConnection($user))
			{
				// login failed, raise generic error
				throw new UserAuthenticationException($user);
			}
		}
		catch (\Exception $e)
		{
			// login failed, if user is not yet banned, evaluate a permanent ban
			if (!$banned && $this->blockEngine->needBan($user))
			{
				// ban the user
				$this->blockEngine->ban($user);
			}

			// only if the user is not banned register the failure of the login
			if (!$banned)
			{
				/** @var \stdClass */
				$credentials = $user->getCredentials();

				$this->registerEvent(
					// do not register the requested event
					null,
					new Response(
						// failed connection
						false,
						// error message
						sprintf(
							'%s Authentication error for user {%s : %s}.',
							$e->getMessage(),
							$credentials->username,
							$credentials->password
						)
					)
				);
			}

			// catch the exception and register it as error
			$this->setError($e);
			return false;
		}

		// login successful
		$this->user = $user;

		// update user manifest
		$this->updateUserManifest();

		// reset any ban previously registered for this user
		$this->blockEngine->unban($this->user);

		return true;
	}

	/**
	 * Fires the specified event.
	 * Accessible only in case the user is correctly connected.
	 *
	 * This method can raise the following internal errors:
	 * - 100 = Authentication Error (Generic)
	 * - 200 = The requested event is missing
	 * - 202 = The requested event does not exist
	 * - 203 = The requested event is not executable
	 * - 204 = The requested event is not authorized
	 * - 500 = Internal error of the plugin executed
	 *
	 * The response of the plugin is always echoed.
	 *
	 * @param   string  $event     The filename of the plugin to run.
	 * @param   array   $args      The arguments to pass within the plugin.
	 * @param   bool    $register  True to register the response, otherwise false to skip it.
	 *
	 * @return  bool    True if the plugin is executed without errors.
	 */
	public function trigger(string $event, array $args = [], bool $register = true)
	{
		$response = $plugin = null;

		try
		{
			// make sure the API framework is still enabled and the user is connected
			if (!$this->isEnabled() || !$this->isConnected())
			{
				// this condition can be verified only when manually triggered
				throw new UserAuthenticationException;
			}

			// prepare response
			$response = new Response;

			/** @var Event */
			$plugin = $this->getPlugin($event);

			// make sure the user is allowed to execute the event
			if (!$this->user->authorise($plugin))
			{
				// the user is not allowed to access this plugin
				throw new EventNotAuthorizedException($event);
			}
		}
		catch (\Exception $e)
		{
			if ($response && $register)
			{
				// log response in the system
				$response->setContent($e->getMessage());
				$this->registerEvent($plugin, $response);
			}

			$this->setError($e);
			return false;
		}

		try
		{
			// run the event, which is able to modify the response
			$output = $plugin->run($args, $response);
		}
		catch (\Exception $e)
		{
			// Catch any exception that might have been thrown
			// by the dispatched event. Generates an error
			// according to the Error specifications.
			$output = new Error($e->getCode(), $e->getMessage());
		}

		// invoke abstract method to save the configuration of the event
		$this->saveEventConfig($plugin);

		// register request payload for extended logging
		$response->setPayload($args);

		if ($response->isVerified())
		{
			// call get error function to clean any registered error
			$this->getError();

			// safely output the response fetched by the event
			$this->output($output, $response->getContentType());
		}
		else
		{
			if ($output instanceof Error)
			{
				// set error retrieved from the plugin
				$this->setError($output);
			}
			else
			{
				// generic event error (500) : get details from response
				$this->setError(500, $response->getContent());
			}
		}

		// register event and response
		if ($register)
		{
			$this->registerEvent($plugin, $response);
		}

		return $response->isVerified();
	}

	/**
	 * Dispatch the specified event to catch the response echoed from the plugin.
	 * Accessible only in case the user is correctly connected.
	 *
	 * This method can raise the following internal errors:
	 * - 100 = Authentication Error (Generic)
	 * - 200 = The requested event is missing
	 * - 202 = The requested event does not exist
	 * - 203 = The requested event is not executable
	 * - 204 = The requested event is not authorized
	 * - 500 = Internal error of the plugin executed
	 *
	 * @param   string   $event     The filename of the plugin to run.
	 * @param   array    $args      The arguments to pass within the plugin.
	 * @param   bool     $register  True to register the response, otherwise false to skip it.
	 *
	 * @return  string   The response echoed from the plugin on success.
	 *
	 * @throws 	\Exception
	 */
	public function dispatch(string $event, array $args = [], bool $register = false)
	{
		// temporarily lock the headers
		$headers = $this->sendHeaders;
		$this->sendHeaders = false;

		// start catching the response echoed
		ob_start();
		// trigger the plugin and get the verified status
		$verified = $this->trigger($event, $args, $register);
		// get the response echoed
		$contents = ob_get_contents();
		// stop catching
		ob_end_clean();

		// unlock the headers (by setting the previous value)
		$this->sendHeaders = $headers;

		if ($verified)
		{
			return $contents;
		}

		// raise error
		throw $this->getError()->asException();
	}

	/**
	 * Sets the last error caught.
	 *
	 * @param   mixed   $code  Either the error code identifier or the error instance.
	 * @param   string  $str   A text description of the error.
	 *
	 * @return  self    This object to support chaining.
	 */
	protected function setError($code, string $str = '')
	{
		if ($code instanceof Error)
		{
			$this->error = $code;
		}
		else if ($code instanceof \Exception)
		{
			$this->error = new Error($code->getCode(), $code->getMessage());
		}
		else
		{
			$this->error = new Error($code, $str);
		}

		return $this;
	}

	/**
	 * Gets the last error caught and cleans it.
	 *
	 * @return 	Error  The error object if exists, otherwise NULL.
	 */
	public function getError()
	{
		$err = $this->error;
		$this->error = null;
		return $err;
	}

	/**
	 * Returns true if an error has been raised.
	 *
	 * @return 	bool  True in case of error, otherwise false.
	 */
	public function hasError()
	{
		return $this->error !== null;
	}

	/**
	 * Checks if the specified key is set in the configuration.
	 *
	 * @param   string  $key  The configuration key to check.
	 *
	 * @return  bool    True if exists, otherwise false.
	 */
	public function has(string $key)
	{
		return array_key_exists($key, $this->config);
	}

	/**
	 * Returns the configuration value of the specified setting.
	 *
	 * @param   string  $key  The key of the configuration value to get.
	 * @param   mixed   $def  The default value if not exists.
	 *
	 * @return  mixed   The configuration value if exists, otherwise the default value.
	 */
	public function get(string $key, $def = null)
	{
		if ($this->has($key))
		{
			return $this->config[$key];
		}

		return $def;
	}

	/**
	 * Sets the configuration value for the specified setting.
	 *
	 * @param   string  $key  The key of the configuration value to set.
	 * @param   mixed   $val  The configuration value to set.
	 *
	 * @return  self    This object to support chaining.
	 */
	public function set(string $key, $val)
	{
		$this->config[$key] = $val;

		return $this;
	}

	/**
	 * Returns the plugin registered with the specified ID.
	 * 
	 * @param   string  $id  The plugin identifier.
	 * 
	 * @return  Event   The event instance.
	 * 
	 * @throws  EventNotFoundException
	 * @throws  InvalidEventException
	 */
	public function getPlugin(string $id)
	{
		if (!$this->container->has($id))
		{
			// event not found
			throw new EventNotFoundException($id);
		}

		// Invoke abstract method to load the configuration of the event.
		// This way, the framework implementor can retrieve the preferences
		// by using the preferred storage system.
		$options = $this->loadEventConfig($id);

		/** @var Event */
		$plugin = $this->container->get(
			// event ID
			$id,
			// event constructor arguments
			$id, $options
		);

		// make sure we have a valid event instance
		if (!$plugin instanceof Event)
		{
			// invalid event
			throw new InvalidEventException($id);
		}

		return $plugin;
	}

	/**
	 * Returns a list containing all the registered plugins.
	 *
	 * @return 	Event[]  A list of plugins found.
	 */
	public function getPluginsList()
	{
		$plugins = [];
		
		foreach ($this->container->keys() as $id)
		{
			try
			{
				/** @var Event */
				$plugins[] = $this->getPlugin($id);
			}
			catch (\Exception $e)
			{
				// ignore plugin
			}
		}

		// sort plugins by title
		usort($plugins, function($a, $b)
		{
			return strcmp($a->getTitle(), $b->getTitle());
		});

		return $plugins;
	}

	/**
	 * Authenticates the provided user and connect it on success.
	 *
	 * @param   User  $user  The object of the user.
	 *
	 * @return  bool  True on success false otherwise.
	 */
	abstract protected function doConnection(User $user);

	/**
	 * Registers the provided event and response.
	 * This log should be visible only from the administrator.
	 *
	 * @param   Event     $event     The event requested.
	 * @param   Response  $response  The response caught or raised.
	 *
	 * @return  bool      True if the event has been registered, otherwise false.
	 */
	abstract protected function registerEvent(Event $event, Response $response);

	/**
	 * Updates the user manifest after a successful authentication.
	 *
	 * @return  bool  True on success, otherwise false.
	 */
	abstract protected function updateUserManifest();

	/**
	 * Prepares the document to output the given data.
	 *
	 * @param   mixed  $data  The data to output.
	 * @param   mixed  $type  The content type.
	 *
	 * @return 	void
	 */
	abstract public function output($data, $type = 'application/json');

	/**
	 * Loads the configuration for the specified event and user.
	 *
	 * @param   string  $eventName  The name of the event.
	 * @param   User    $user       The object of the user.
	 *
	 * @return  mixed   Either an array or an object.
	 */
	abstract protected function loadEventConfig(string $eventName, User $user = null);

	/**
	 * Saves the configuration for the specified event and user.
	 *
	 * @param   Event  $event  The event requested.
	 * @param   User   $user   The object of the user.
	 *
	 * @return  bool   True on success, false otherwise.
	 */
	abstract protected function saveEventConfig(Event $event, User $user = null);
}
