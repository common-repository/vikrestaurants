<?php
/** 
 * @package     VikRestaurants
 * @subpackage  core
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2023 E4J s.r.l. All Rights Reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @link        https://vikwp.com
 */

// No direct access to this file
defined('ABSPATH') or die('No script kiddies please!');

RestaurantsHelper::printMenu();

?>

<div class="viwppro-cnt vikwppro-download">
	<div class="vikwppro-header">
		<div class="vikwppro-header-inner">
			<div class="vikwppro-download-result">
				<h2><i class="fas fa-sync-alt"></i> <?php echo JText::translate('VREPROPLWAIT'); ?></h2>
				<h3 class="vikwppro-cur-action"><?php echo JText::translate('VREPRODLINGPKG'); ?></h3>
			</div>
			<div class="vikwppro-download-progress">
				<progress value="0" max="100" id="vikwpprogress"></progress> 
			</div>
		</div>
	</div>

	<?php if (!empty($this->changelog)): ?>
		<div class="vikwppro-changelog-wrap">
			<div class="vikwppro-plg-changelog">
				<?php echo $this->changelog; ?>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php
JText::script('VREUPDCOMPLOKCLICK');
JText::script('VREUPDCOMPLNOKCLICK');
?>

<script>
	(function($) {
		'use strict';

		let vikwprunning = false;
		let vikwcomplete = false;

		const vikwpStartDownload = () => {
			if (vikwprunning) {
				return;
			}

			vikwprunning = true;
			dispatchProgress();

			doAjax('admin-ajax.php?action=vikrestaurants&task=license.downloadpro', {
				key: "<?php echo addslashes($this->licenseKey); ?>",
			}, (resp) => {
				// stop with success
				vikwpStopMonitoring(true);
			}, (err) => {
				// stop with error
				vikwpStopMonitoring(err.responseText);
			});
		}

		const vikwpStopMonitoring = (result) => {
			vikwcomplete = true;
			vikwprunning = false;

			$('#vikwpprogress').attr('value', 100);

			if (result === true) {
				$('.vikwppro-download-result').html(
					'<h1 class="vikwp-download-success"><i class="fas fa-check"></i></h1>\n' +
					'<p>\n' + 
						'<button type="button" class="button button-primary" onclick="document.location.href=\'admin.php?page=vikrestaurants\';">' + 
							Joomla.JText._('VREUPDCOMPLOKCLICK') +
						'</button>\n'+
					'</p>\n'
				);
			} else {
				$('.vikwppro-download-result').html(
					'<h1 class="vikwp-download-error"><i class="fas fa-times"></i></h1>\n' +
					'<h3 class="download-error-message">' + result + '</h3>\n' +
					'<p>\n' +
						'<button type="button" class="button" onclick="document.location.href=\'admin.php?page=vikrestaurants&view=gotopro\';">\n' +
							Joomla.JText._('VREUPDCOMPLNOKCLICK') + 
						'</button>\n' + 
					'</p>\n'
				);
				
				$('#vikwpprogress').hide();
			}
		}

		const dispatchProgress = () => {
			setTimeout(() => {
				if (vikwcomplete) {
					$('#vikwpprogress').attr('value', 100);
					return;
				}

				let curprogress = parseInt($('#vikwpprogress').attr('value'));
				let nextstep = Math.floor(Math.random() * 5) + 6;
				
				if ((curprogress + nextstep) > 100) {
					curprogress = 100;
				} else {
					curprogress += nextstep;
				}

				$('#vikwpprogress').attr('value', curprogress);

				if (curprogress < 100) {
					dispatchProgress();
				}
			}, (Math.floor(Math.random() * 501) + 750));
		}

		$(function() {
			vikwpStartDownload();
		});
	})(jQuery);
</script>