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

VRELoader::import('library.mvc.controllers.admin');

/**
 * VikRestaurants mail preview controller.
 *
 * @since 1.9
 */
class VikRestaurantsControllerMailpreview extends VREControllerAdmin
{
	/**
	 * AJAX end-point used to save the CSS code stored for a specific template.
	 * 
	 * @return  void
	 */
	public function savecss()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// get e-mail template identifier
		$group = $app->input->getString('group', '');
		$alias = $app->input->getString('alias', '');

		// get CSS code from the request
		$css = $app->input->getRaw('css', '');

		/** @var JModelLegacy */
		$model = $this->getModel();

		// register CSS in configuration for the given e-mail template
		$saved = $model->setCSS($group, $alias, $css);

		if (!$saved)
		{
			// get last error
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error, 500);
			}

			// inform the user about the error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		$this->sendJSON(1);
	}

	/**
	 * Tries to render a preview of the selected e-mail template.
	 *
	 * @return 	void
	 */
	public function rendertemplate()
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		$id    = $app->input->get('id', 0, 'uint');
		$group = $app->input->get('group', '', 'string');
		$alias = $app->input->get('alias', '', 'string');
		$file  = $app->input->get('file', '', 'string');
		$lang  = $app->input->get('langtag', '', 'string');

		$options = [];

		// build base arguments
		$args = array($group, $alias);

		$options = [];

		// flag as test, so that attached plugins can recognize this purpose
		$options['test'] = true;
		// always add a fake recipient to prevent the email templates from throwing an error in case
		// the reservation/order does not specify a customer e-mail address
		$options['recipient'] = VikRestaurants::getAdminMail();

		// fetch arguments based on specified group and alias
		if ($group == 'restaurant')
		{
			// restaurant group supports only reservations
			if (!$id)
			{
				// find latest reservation
				$q = $db->getQuery(true)
					->select($db->qn('id'))
					->from($db->qn('#__vikrestaurants_reservation'))
					->where($db->qn('closure') . ' = 0')
					->where($db->qn('id_parent') . ' <= 0')
					->order($db->qn('id') . ' DESC');

				$db->setQuery($q, 0, 1);
				$id = (int) $db->loadResult();

				if (!$id)
				{
					throw new Exception('Before to see a preview of the e-mail template, you have to create at least a reservation.', 400);
				}
			}

			// inject reservation ID within the arguments
			$args[] = $id;
		}
		else
		{
			// take-away group owns different types of providers
			if ($alias == 'stock')
			{
				// do nothing for stock template
			}
			else if ($alias == 'review')
			{
				if (!$id)
				{
					// find latest review
					$q = $db->getQuery(true)
						->select($db->qn('id'))
						->from($db->qn('#__vikrestaurants_reviews'))
						->order($db->qn('id') . ' DESC');

					$db->setQuery($q, 0, 1);
					$id = (int) $db->loadResult();

					if (!$id)
					{
						throw new Exception('Before to see a preview of the e-mail template, you have to create at least a review.', 400);
					}
				}

				// inject review ID within the arguments
				$args[] = $id;
			}
			else
			{
				if (!$id)
				{
					// find latest order
					$q = $db->getQuery(true)
						->select($db->qn('id'))
						->from($db->qn('#__vikrestaurants_takeaway_reservation'))
						->order($db->qn('id') . ' DESC');

					$db->setQuery($q, 0, 1);
					$id = (int) $db->loadResult();

					if (!$id)
					{
						throw new Exception('Before to see a preview of the e-mail template, you have to create at least an order.', 400);
					}
				}

				// inject reservation ID within the arguments
				$args[] = $id;
			}
		}

		if ($lang)
		{
			// force language tag too
			$options['lang'] = $lang;
		}

		if ($alias == 'cancellation')
		{
			// we should include a sample cancellation reason
			// text to make it visible for styling
			$options['cancellation_reason'] = 'The cancellation reason will be printed here in case the system supports it.';
		}
		else if ($alias == 'stock')
		{
			// pass test attributes to retrieve some junk data
			$options['start']  = $app->input->getUint('start');
			$options['offset'] = $app->input->getUint('offset');
		}

		$args[] = $options;

		/** @var E4J\VikRestaurants\Mail\MailTemplate  instantiate provider by using the fetched arguments */
		$mailTemplate = call_user_func_array(['E4J\\VikRestaurants\\Mail\\MailFactory', 'getTemplate'], $args);

		// overwrite template file
		$mailTemplate->setFile($file);

		/** @var E4J\VikRestaurants\Mail\Mail  generate mail from template */
		$mail = $mailTemplate->getMail();

		// get mail subject, used as page title
		$title = $mail->getSubject();

		// get mail body
		$tmpl = $mail->getBody();

		// include style to prevent body from having margins
		$tmpl = '<style>body{margin:0;padding:0;}</style>' . $tmpl;

		$data = [
			'title' => $title,
			'body'  => $tmpl,
		];

		if ($app->input->getBool('live'))
		{
			// apply customizations
			$customizer = new E4J\VikRestaurants\Document\MailCustomizer($data['body']);
			$customizer->addCss($this->getModel()->getCss($group, $alias));
			$data['body'] = $customizer->getHtml();

			// display resulting template
			$base = VREBASE . DIRECTORY_SEPARATOR . 'layouts';
			echo JLayoutHelper::render('document.blankpage', $data, $base);
			$app->close();
		}
		else
		{
			// load customizer script and style
			$vik = VREApplication::getInstance();
			$vik->addScript(VREASSETS_URI . 'js/customizer.js');
			$vik->addStyleSheet(VREASSETS_URI . 'css/customizer.css');

			$app->input->set('tmpl', 'component');

			// force document title
			JFactory::getDocument()->setTitle($data['title']);

			echo $data['body'];
		}
	}

	/**
	 * AJAX end-point used to quickly save conditional texts.
	 * 
	 * @return  void
	 */
	public function savemailtext()
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if (!JSession::checkToken())
		{
			// missing CSRF-proof token
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JINVALID_TOKEN'));
		}

		// check user permissions
		if (!$user->authorise('core.access.config', 'com_vikrestaurants'))
		{
			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close(403, JText::translate('JERROR_ALERTNOAUTHOR'));
		}

		// obtain conditional text data from the request
		$name = $app->input->get('name', '', 'string');
		$body = JComponentHelper::filterText($app->input->get('body', '', 'raw'));
		$position = $app->input->get('position', '', 'string');
		$language = $app->input->get('language', '', 'string');
		$template = $app->input->get('template', '', 'string');

		// create conditional text model
		$model = $this->getModel('mailtext');

		// prepare conditional text data
		$data = [
			'name'    => $name,
			'actions' => [],
			'filters' => [],
		];

		// create a new body action for the specified position
		$data['actions'][] = [
			'id' => 'body',
			'options' => [
				'position' => $position,
				'text'     => $body,
			],
		];

		if ($language && $language !== '*')
		{
			// in case of specific language, create a new filter
			$data['filters'][] = [
				'id' => 'language',
				'options' => [
					'langtag' => $language,
				],
			];
		}

		if ($template)
		{
			// in case of specific template, create a new filter
			$data['filters'][] = [
				'id' => 'template',
				'options' => [
					'templates' => [$template],
				],
			];
		}

		// attempt to save the conditional text
		$id = $model->save($data);

		if (!$id)
		{
			// get last error message
			$error = $model->getError($last = null, $string = false);

			if (!$error instanceof Exception)
			{
				$error = new Exception($error ?: 'Error', 500);
			}

			// raise error
			E4J\VikRestaurants\Http\Document::getInstance($app)->close($error->getCode(), $error->getMessage());
		}

		// return conditional text data to caller
		$this->sendJSON($model->getData());
	}
}
