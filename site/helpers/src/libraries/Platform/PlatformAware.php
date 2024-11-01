<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Platform;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface;
use E4J\VikRestaurants\Platform\Form\FormFactoryInterface;
use E4J\VikRestaurants\Platform\Payment\PaymentFactoryInterface;
use E4J\VikRestaurants\Platform\Uri\UriInterface;

/**
 * Declares all the helper methods that may differ between every supported platform.
 * 
 * @since 1.9
 */
abstract class PlatformAware implements PlatformInterface
{
	/** @var DispatcherInterface */
	private $dispatcher;

	/** @var FormFactoryInterface */
	private $formFactory;

	/** @var PaymentFactoryInterface */
	private $paymentFactory;

	/** @var UriInterface */
	private $uri;

	/**
	 * @inheritDoc
	 */
	public function getDispatcher()
	{
		if (is_null($this->dispatcher))
		{
			// lazy creation
			$this->dispatcher = $this->createDispatcher();
		}

		// make sure we have a valid instance
		if (!$this->dispatcher instanceof DispatcherInterface)
		{
			if (is_object($this->dispatcher))
			{
				// extract class name from object
				$t = get_class($this->dispatcher);
			}
			else
			{
				// fetch the type of the property
				$t = gettype($this->dispatcher);
			}

			// nope, throw a "Not acceptable" 406 error
			throw new \UnexpectedValueException(sprintf('The [%s] object is not a valid dispatcher instance', $t), 406);
		}

		return $this->dispatcher;
	}

	/**
	 * @inheritDoc
	 */
	public function getFormFactory()
	{
		if (is_null($this->formFactory))
		{
			// lazy creation
			$this->formFactory = $this->createFormFactory();
		}

		// make sure we have a valid instance
		if (!$this->formFactory instanceof FormFactoryInterface)
		{
			if (is_object($this->formFactory))
			{
				// extract class name from object
				$t = get_class($this->formFactory);
			}
			else
			{
				// fetch the type of the property
				$t = gettype($this->formFactory);
			}

			// nope, throw a "Not acceptable" 406 error
			throw new \UnexpectedValueException(sprintf('The [%s] object is not a valid form factory instance', $t), 406);
		}

		return $this->formFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getPaymentFactory()
	{
		if (is_null($this->paymentFactory))
		{
			// lazy creation
			$this->paymentFactory = $this->createPaymentFactory();
		}

		// make sure we have a valid instance
		if (!$this->paymentFactory instanceof PaymentFactoryInterface)
		{
			if (is_object($this->paymentFactory))
			{
				// extract class name from object
				$t = get_class($this->paymentFactory);
			}
			else
			{
				// fetch the type of the property
				$t = gettype($this->paymentFactory);
			}

			// nope, throw a "Not acceptable" 406 error
			throw new \UnexpectedValueException(sprintf('The [%s] object is not a valid payment factory instance', $t), 406);
		}

		return $this->paymentFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getUri()
	{
		if (is_null($this->uri))
		{
			// lazy creation
			$this->uri = $this->createUri();
		}

		// make sure we have a valid instance
		if (!$this->uri instanceof UriInterface)
		{
			if (is_object($this->uri))
			{
				// extract class name from object
				$t = get_class($this->uri);
			}
			else
			{
				// fetch the type of the property
				$t = gettype($this->uri);
			}

			// nope, throw a "Not acceptable" 406 error
			throw new \UnexpectedValueException(sprintf('The [%s] object is not a valid URI instance', $t), 406);
		}

		return $this->uri;
	}

	/**
	 * Creates a new event dispatcher instance.
	 * 
	 * @return  DispatcherInterface
	 */
	abstract protected function createDispatcher();

	/**
	 * Creates a new form factory instance.
	 * 
	 * @return  FormFactoryInterface
	 */
	abstract protected function createFormFactory();

	/**
	 * Creates a new payment factory instance.
	 * 
	 * @return  PaymentFactoryInterface
	 */
	abstract protected function createPaymentFactory();

	/**
	 * Creates a new URI helper instance.
	 *
	 * @return  UriInterface
	 */
	abstract protected function createUri();
}
