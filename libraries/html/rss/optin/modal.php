<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.rss
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/** @var E4J\VikRestaurants\Platform\Form\FormFactoryInterface */
$formFactory = VREFactory::getPlatform()->getFormFactory();

?>

<style>
	/* make background color of checkbox darker */
	#vre_formfield_rss_optin_status:not(:checked) + label:before {
		background-color: #ccc; 
	}

	#vre_formfield_rss_optin_status-control {
		display: flex;
		align-items: center;
		flex-direction: row-reverse;
		justify-content: start;
	}
	#vre_formfield_rss_optin_status-control .controls {
		display: flex;
		margin-right: 8px;
	}
</style>

<!-- RSS intro -->

<p>
	<?php
	_e(
		'VikRestaurants supports the possibility of subscribing to a RSS channel and we are wondering whether you might be interested in using this service.', 
		'vikrestaurants'
	);
	?>
</p>

<!-- explain RSS usage -->

<p>
	<b>
		<?php
		_e(
			'Why should I opt in to this service?',
			'vikrestaurants'
		);
		?>
	</b>
</p>

<p>
	<?php
	_e(
		'This RSS service mainly covers these macro sections: <b>news</b>, <b>tips</b> and <b>offers</b>. You might receive news about VikRestaurants or anything else that interests the WordPress world, such as the jQuery conflict that broke millions of websites with WP 5.5. Sometimes you could receive notifications about tips or features that you didn\'t even think they could exist. During the most important festivities you might receive coupon codes to renew you license at a discount price. We guarantee you that this service won\'t result in an annoying advertising system.', 
		'vikrestaurants'
	);
	?>
</p>

<!-- privacy policy -->

<p>
	<b>
		<?php
		_e(
			'What kind of personal data do we collect?',
			'vikrestaurants'
		);
		?>
	</b>
</p>

<p>
	<?php
	_e(
		'Our company does not collect any personal data here. The syndication URL never includes sensitive data that may be linked back to you.',
		'vikrestaurants'
	);
	?>
</p>

<!-- opt in checkbox -->

<p>
	<?php
	_e(
		'We need you to explicitly opt in to this RSS service for GDPR compliance. Toggle the checkbox below if you are interested or leave it unchecked. You are free to change your decision in any time from the configuration of VikRestaurants.',
		'vikrestaurants'
	);
	?>
</p>

<?php
echo $formFactory->createField()
	->type('checkbox')
	->name('rss_optin_status')
	->checked(true)
	->label(__('I want to opt in to VikWP RSS service', 'vikrestaurants'));
?>

<!-- finalisation -->

<p>
	<?php
	_e(
		'Hit the <b>Save</b> button to confirm your choice and close this popup.',
		'vikrestaurants'
	);
	?>
</p>
