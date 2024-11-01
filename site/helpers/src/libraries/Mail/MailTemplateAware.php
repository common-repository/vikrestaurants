<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Implements some helpful methods for the mail templates.
 * 
 * Children classes can implement the following techniques to speed up the building
 * process of the mail template.
 * 
 * // the tags defined here will be automatically injected within the template
 * $this->addTemplateData(['name' => 'John Smith', ...]);
 * 
 * // the attached decorators can entirely manipulate the generated mail instance
 * // or include new template tags
 * $this->attachDecorator(new class implements MailTemplateDecorator { ... });
 * 
 * It is suggested to register template tags and decorators within the constructor,
 * in order to avoid registering them more than once due to a duplicate call to the
 * `getMail()` method.
 *
 * @since 1.9
 */
abstract class MailTemplateAware implements MailTemplate
{
	/** @var array */
	private $templateData = [];

	/** @var MailTemplateDecorator[] */
	private $decorators = [];

	/**
	 * Returns the mail instance ready to be sent.
	 *
	 * @return 	Mail
	 */
	final public function getMail()
	{
		$eventArgs = [];

		/** @var Mail */
		$mail = $this->createMail($eventArgs);

		// check whether the e-mail template implements a configuration array
		if (isset($this->options))
		{
			// always include the configuration array at the end of the event arguments
			$eventArgs[] = $this->options;
		}

		// get e-mail subject
		$subject = (string) $mail->getSubject();

		// let plugins manipulate the subject for this e-mail template
		$res = call_user_func_array(
			[$this, 'triggerEvent'],
			array_merge(
				['subject', &$subject],
				// inject any custom arguments
				$eventArgs
			)
		);

		if ($res === false)
		{
			// mail sending aborted
			throw new \RuntimeException('Mail aborted during subject bulding', 406);
		}

		// update mail subject
		$mail->setSubject($subject);

		// get e-mail body
		$body = $mail->getBody();

		// let plugins manipulate the content for this e-mail template
		$res = call_user_func_array(
			[$this, 'triggerEvent'],
			array_merge(
				['content', &$body],
				// inject any custom arguments
				$eventArgs
			)
		);

		if ($res === false)
		{
			// mail sending aborted
			throw new \RuntimeException('Mail aborted during body bulding', 406);
		}

		// update mail body
		$mail->setBody((string) $body);

		// obtain mail template ID
		$templateId = $this->getTemplateId();

		// create conditional texts dispatcher
		$conditionalTextsDispatcher = new ConditionalText\Dispatcher;
		// obtain all the eligible conditional texts
		$conditionalTexts = $conditionalTextsDispatcher->filter($templateId, $eventArgs);

		if (!empty($this->options['test']))
		{
			// we are in test mode, inject fictitious conditional text to support the
			// creation of new conditional texts from the customizer
			$conditionalTexts->add(new ConditionalText\Builtin\CustomizerAddPlaceholder);
		}

		// process the conditional texts
		$conditionalTextsDispatcher->process($mail, $conditionalTexts);

		// iterate all the attached decorators
		foreach ($this->decorators as $decorator)
		{
			// build mail
			$decorator->build($mail, $this);
		}

		// obtain new updated mail body and subject
		$subject = (string) $mail->getSubject();
		$body    = (string) $mail->getBody();

		// parse e-mail template placeholders
		foreach ($this->getTemplateData() as $tag => $value)
		{
			$subject = str_replace("{{$tag}}", $value, $subject);
			$body    = str_replace("{{$tag}}", $value, $body);
		}

		// only if we are not in test mode, inject the style created through the customizer
		if (empty($this->options['test']))
		{
			// fetch group and class from template ID
			list($group, $alias) = explode('.', $templateId);
			// recover the custom CSS created for this mail template
			$css = \JModelVRE::getInstance('mailpreview')->getCss($group, $alias);

			// customize the body
			$body = (new \E4J\VikRestaurants\Document\MailCustomizer($body))->addCss($css)->getHtml();
		}

		// update mail body and subject after injecting the tags
		$mail->setSubject($subject);
		$mail->setBody($body);

		return $mail;
	}

	/**
	 * Creates the mail instance.
	 * 
	 * @param   array  &$args  Inject here the event arguments that should
	 *                         be passed while triggering the event.
	 *
	 * @return 	Mail
	 */
	abstract protected function createMail(array &$args);

	/**
	 * Obtains all the template tags with their respective values.
	 * 
	 * @return  array  The tag-value associative array.
	 */
	final public function getTemplateData()
	{
		return $this->templateData;
	}

	/**
	 * Registers new templates tags with their respective values.
	 * 
	 * @param   array  $data  A tag-value associative array.
	 * 
	 * @return  void
	 */
	final public function addTemplateData(array $data)
	{
		$this->templateData = array_merge($this->templateData, $data);
	}

	/**
	 * Registers new templates tags with their respective values.
	 * 
	 * @param   MailTemplateDecorator  $decorator
	 * 
	 * @return  seld  This object to support chaining.
	 */
	final public function attachDecorator(MailTemplateDecorator $decorator)
	{
		$this->decorators[] = $decorator;

		return $this;
	}

	/**
	 * Creates a template ID from the class name.
	 * 
	 * @return  string  An ID in the form "{group}.{alias}".
	 */
	protected function getTemplateId()
	{
		// split the namespace in chunks
		$chunks = preg_split("/\\\\/", get_class($this));
		// ignore the default namespace: "E4J\VikRestaurants\Mail\Templates"
		$chunks = array_splice($chunks, 4);

		// obtain class and normalize it
		$class = preg_replace("/MailTemplate$/", '', (string) array_pop($chunks));
		// build group with the remaining chunks
		$group = implode('', $chunks);

		return strtolower($group . '.' . $class);
	}

	/**
	 * Helper method used to trigger a plugin event before sending the e-mail.
	 * Any other parameter specified after the target will be included as argument for the plugin event.
	 *
	 * @param   string  $what     Either "subject" or "content", depending on what it is needed to edit.
	 * @param   string  &$target  The content of the target to be edited.
	 *
	 * @return  bool    False in case the e-mail sending has been prevented, true otherwise.
	 */
	protected function triggerEvent($what, &$target)
	{
		/** @var E4J\VikRestaurants\Platform\Dispatcher\DispatcherInterface */
		$dispatcher = \VREFactory::getPlatform()->getDispatcher();

		// fetch group and class from template ID
		list($group, $class) = explode('.', $this->getTemplateId());

		// fetch event name based on what we need to fetch, group and mail class
		$event = 'onBeforeSendMail' . ucfirst($what) . ucfirst($group) . ucfirst($class);

		// get all arguments
		$args = func_get_args();
		// keep only the additional arguments
		$args = array_splice($args, 2);

		// merge target within arguments
		$args = array_merge(array(&$target), $args);

		try
		{
			/**
			 * Triggers an event to let the plugins be able to handle
			 * the subject of the e-mail and the HTML contents of the
			 * related template.
			 *
			 * The event name is built as:
			 * onBeforeSendMail[Subject|Content][Restaurant|Takeaway][Class]
			 *
			 * The event might specify additional arguments, such as the
			 * details of the reservation/order.
			 *
			 * @param   string  &$target  Either the subject or the HTML content,
			 *                            depending on the $what argument that
			 *                            was passed to this method.
			 *
			 * @return  bool    Return false to prevent e-mail sending.
			 *
			 * @since   1.8
			 */
			$result = $dispatcher->filter($event, $args);

			/** @var E4J\VikRestaurants\Event\EventResponse */

			if ($result->isFalse())
			{
				// abort in case a plugin returned false
				return false;
			}
		}
		catch (\Exception $e)
		{
			// prevented by an exception
			return false;
		}

		return true;
	}
}
