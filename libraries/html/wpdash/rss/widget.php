<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.wpdash
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var  JRegistry         $config  The configuration registry
 * @var  JDashboardWidget  $widget  The widget instance.
 * @var  array             $feeds   A list of feeds.
 */

$document = JFactory::getDocument();

$internalFilesOptions = array('version' => VIKRESTAURANTS_SOFTWARE_VERSION);

// system.js must be loaded on both front-end and back-end for tmpl=component support
$document->addScript(VIKRESTAURANTS_CORE_MEDIA_URI . 'js/system.js', $internalFilesOptions, array('id' => 'vre-sys-script'));
$document->addScript(VIKRESTAURANTS_CORE_MEDIA_URI . 'js/admin.js', $internalFilesOptions, array('id' => 'vre-admin-script'));
$document->addScript(VIKRESTAURANTS_CORE_MEDIA_URI . 'js/bootstrap.min.js', $internalFilesOptions, array('id' => 'bootstrap-script'));
$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/system.css', $internalFilesOptions, array('id' => 'vre-sys-style'));
$document->addStyleSheet(VIKRESTAURANTS_CORE_MEDIA_URI . 'css/bootstrap.lite.css', $internalFilesOptions, array('id' => 'bootstrap-lite-style'));

// prepare modal to display opt-in
echo JHtml::fetch(
	'bootstrap.renderModal',
	'jmodal-vre-rss-feed',
	array(
		'title'       => '',
		'closeButton' => true,
		'keyboard'    => true,
		'top'         => true,
		'width'       => 70,
		'height'      => 80,
	),
	'{placeholder}'
);

?>

<style>
	#vik_restaurants_rss .inside {
		padding: 0 !important;
		margin: 0 !important;
	}
	#vik_restaurants_rss .modal-header h3 {
		margin: 0;
		line-height: 50px;
		font-weight: normal;
		font-size: 22px;
	}
	#vik_restaurants_rss .modal-header h3 .dashicons-before:before {
		line-height: 50px;
	}
	#vik_restaurants_rss img {
		max-width: 100%;
	}

	.vre-rss-widget ul {
		margin: 0;
		padding: 0;
	}
	.vre-rss-widget ul li {
		list-style: none;
		display: flex;
		align-items: center;
		justify-content: space-between;
		flex-wrap: wrap;
		margin: 0;
		padding: 8px 12px;
		border-bottom: 1px solid #eee;
	}
	.vre-rss-widget ul li:last-child {
		border-bottom: 0;
	}
	.vre-rss-widget ul li:nth-child(odd) {
		background: #fafafa;
	}

	.vre-rss-widget ul li .feed-icon {
		width: 32px;
	}
	.vre-rss-widget ul li .feed-details {
		flex: 1;
	}
	.vre-rss-widget ul li .feed-date-time {
		text-align: right;
	}

	.vre-rss-widget .rss-missing-optin {
		padding: 10px 10px 0 10px;
	}
</style>

<div class="vre-rss-widget">

	<?php
	// make sure the RSS service is enabled
	if (!$config->get('optin'))
	{
		// service not enabled
		?>
		<div class="rss-missing-optin">
			<div class="notice notice-error inline">
				<p>
					<?php _e('<b>You haven\'t opted in the RSS service!</b><br />Click the following button to start receiving RSS feeds.', 'vikrestaurants'); ?>
				</p>

				<p>
					<a href="admin.php?page=vikrestaurants&view=editconfig#rss_optin_status" class="button button-primary">
						<?php _e('Activate RSS Feeds', 'vikrestaurants'); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<ul>
			<?php
			foreach ($feeds as $i => $feed)
			{
				switch (strtolower($feed->category))
				{
					case 'promo':
						$icon = 'star-filled';
						break;

					case 'tips':
						$icon = 'welcome-learn-more';
						break;

					case 'news':
						$icon = 'megaphone';
						break;

					default:
						$icon = 'rss';
				}

				?>
				<li data-id="<?php echo $feed->id; ?>">
					<div class="feed-icon">
						<span class="dashicons-before dashicons-<?php echo $icon; ?>"></span>
					</div>

					<div class="feed-details" data-title="<?php echo $this->escape($feed->title); ?>" data-category="<?php echo $this->escape($feed->category); ?>">
						<div class="feed-title">
							<a href="javascript: void(0);">
								<b><?php echo $feed->title; ?></b>
							</a>
						</div>
						<div class="feed-category"><?php echo $feed->category; ?></div>
					</div>

					<div class="feed-date-time">
						<div class="feed-date">
							<?php echo JHtml::fetch('date', $feed->date, JText::translate('DATE_FORMAT_LC3')); ?>
						</div>
						<div class="feed-time">
							<?php echo JHtml::fetch('date', $feed->date, get_option('time_format')); ?>
						</div>
					</div>

					<div style="display: none;" class="rss-content">
						<?php echo $feed->content; ?>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}
	?>

</div>

<script>

	jQuery(document).ready(function() {

		jQuery('#vik_restaurants_rss .feed-details a').on('click', function() {
			// get parent <li>
			var li = jQuery(this).closest('li');
			// find feed details
			var details = li.find('.feed-details');
			// find feed content
			var content = li.find('.rss-content').html();

			// get modal
			var modal = jQuery('#jmodal-vre-rss-feed');

			// register feed ID
			modal.attr('data-feed-id', li.data('id'));

			// update modal title
			modal.find('.modal-header h3').html(
				li.find('.feed-icon').html() + ' ' +
				details.data('category') + ' - ' +
				details.data('title')
			);

			// update modal content
			modal.find('.modal-body').html(content);

			// display modal
			wpOpenJModal('vre-rss-feed');
		});

	});

</script>
