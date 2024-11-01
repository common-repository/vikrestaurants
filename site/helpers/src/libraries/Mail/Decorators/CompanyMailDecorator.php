<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

namespace E4J\VikRestaurants\Mail\Decorators;

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

use E4J\VikRestaurants\Mail\Mail;
use E4J\VikRestaurants\Mail\MailTemplate;
use E4J\VikRestaurants\Mail\MailTemplateDecorator;

/**
 * Adds support to the following template data (tags):
 * 
 * - {logo}          A <img> tag to display the logo of the restaurant.
 * - {company_name}  The name of the restaurant.
 *
 * @since 1.9
 */
final class CompanyMailDecorator implements MailTemplateDecorator
{
	/**
	 * @inheritDoc
	 */
	public function build(Mail $mail, MailTemplate $template)
	{
		/** @var E4J\VikRestaurants\Config\AbstractConfiguration */
		$config = \VREFactory::getConfig();

		// fetch company logo image
		$imgLogo = $config->get('companylogo');

		if ($imgLogo && \JFile::exists(VREMEDIA . '/' . $imgLogo))
		{
			// create <img> tag
			$imgLogo = \JHtml::fetch('vrehtml.media.display', $imgLogo, [
				'alt'   => $config->get('restname'),
				'small' => false,
				'style' => 'max-width: 100%;',
			]);
		}
		else
		{
			// image not specified, replace with an empty string
			$imgLogo = '';
		}

		// register template data
		$template->addTemplateData([
			'logo'         => $imgLogo,
			'company_name' => $config->get('restname'),
		]);
	}
}
