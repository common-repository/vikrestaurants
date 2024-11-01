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
 * Creates a fields collection useful for the submit of a review.
 * 
 * @since 1.9
 */
class LeaveReviewFieldsProvider extends ExtendableFieldsProvider
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
		$this->extendFields($fields, 'onDisplayLeaveReviewForm', ['top']);

		if (empty($this->options['logged']))
		{
			// set up first name
			$fields[] = Field::getInstance([
				'name'      => 'review_user_name',
				'id'        => 'reviewusername',
				'type'      => 'text',
				'rule'      => 'nominative',
				'langname'  => \JText::translate('VRREVIEWSFIELDUSERNAME'),
				'required'  => true,
				'maxlength' => 128,
			]);

			// set up e-mail
			$fields[] = Field::getInstance([
				'name'      => 'review_user_mail',
				'id'        => 'reviewusermail',
				'type'      => 'text',
				'rule'      => 'email',
				'langname'  => \JText::translate('VRREVIEWSFIELDUSERMAIL'),
				'required'  => true,
				'maxlength' => 128,
			]);
		}

		// "middle" position
		$this->extendFields($fields, 'onDisplayLeaveReviewForm', ['middle']);

		// set up review title
		$fields[] = Field::getInstance([
			'name'      => 'review_title',
			'id'        => 'reviewtitle',
			'type'      => 'text',
			'langname'  => \JText::translate('VRREVIEWSFIELDTITLE'),
			'required'  => true,
			'maxlength' => 64,
		]);

		// set up review rating
		$ratingHtml = '<div class="review-rating-stars">';

		for ($i = 1; $i <= 5; $i++)
		{
			$ratingHtml .= '<div class="vr-ratingstar-box rating-nostar" data-id="' . $i . '"></div>';
		}

		$ratingHtml .= '<div id="vr-newrating-desc">' . \JText::translate('VRREVIEWSTARDESC0') . '</div>
			<input type="hidden" name="vrcfreviewrating" id="vrcfreviewrating" class="required" value="" />
		</div>';

		$fields[] = Field::getInstance([
			'type'     => 'html',
			'name'     => 'review_rating',
			'id'       => 'reviewrating',
			'langname' => \JText::translate('VRREVIEWSFIELDRATING'),
			'html'     => $ratingHtml,
			'class'    => 'has-value',
		]);

		// "bottom" position
		$this->extendFields($fields, 'onDisplayLeaveReviewForm', ['bottom']);

		// set up review comment
		$comment = new \JRegistry($this->options['comment'] ?? []);

		$commentDescription = '<div class="rv-new-charsleft">
			<span>' . \JText::translate('VRREVIEWSCHARSLEFT') . ' </span>
			<span id="vrcommentchars">' . $comment->get('max', 1024) . '</span>
		</div>';

		if ($comment->get('min', 0) > 0)
		{
			$commentDescription .= '<div class="rv-new-minchars">
				<span>' . \JText::translate('VRREVIEWSMINCHARS') . ' </span>
				<span id="vrcommentminchars">' . $comment->get('min', 0) . '</span>
			</div>';
		}

		$fields[] = Field::getInstance([
			'name'        => 'review_comment',
			'id'          => 'reviewcomment',
			'type'        => 'textarea',
			'langname'    => \JText::translate('VRREVIEWSFIELDCOMMENT'),
			'required'    => $comment->get('required', false),
			'maxlength'   => $comment->get('max', 1024),
			'description' => '<div class="rv-comment-descr">' . $commentDescription . '</div>',
		]);

		// "footer" position
		$this->extendFields($fields, 'onDisplayLeaveReviewForm', ['footer']);

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
				'id'       => 'review_gdpr',
				'name'     => 'gdpr',
				'type'     => 'checkbox',
				'langname' => \JText::translate('GDPR_POLICY_AUTH_NO_LINK'),
				'required' => 1,
				'poplink'  => $policy,
			]);
		}

		// "policy" position
		$this->extendFields($fields, 'onDisplayLeaveReviewForm', ['policy']);

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
		$this->extendFields($fields, 'onDisplayLeaveReviewForm', ['captcha']);

		return $fields;
	}
}
