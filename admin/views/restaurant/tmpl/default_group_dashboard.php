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

$vik = VREApplication::getInstance();

?>

<div class="row-fluid dashboard-wrapper" style="margin-top:2px;">

	<?php foreach ($this->currentDashboard as $position => $widgets): ?>

		<div class="dashboard-widgets-container <?php echo $this->escape($this->layout); ?>" data-position="<?php echo $this->escape($position); ?>">

			<?php
			foreach ($widgets as $i => $widget)
			{
				$id = $widget->getID();

				// get widget description
				$help = $widget->getDescription();

				if ($help)
				{
					// create popover with the widget description
					$help = $vik->createPopover([
						'title'   => $widget->getTitle(),
						'content' => $help,
					]);
				}

				// widen or shorten the widget
				switch ($widget->getSize())
				{
					// force EXTRA SMALL width : (100% / N) - (100% / (N * 2))
					case 'extra-small':
						$width = 'width: calc((100% / ' . count($widgets) . ') - (100% / ' . (count($widgets) * 2) . '));';
						break;

					// force SMALL width : (100% / N) - (100% / (N * 4))
					case 'small':
						$width = 'width: calc((100% / ' . count($widgets) . ') - (100% / ' . (count($widgets) * 4) . '));';
						break;

					// force NORMAL width : (100% / N)
					case 'normal':
						$width = 'width: calc(100% / ' . count($widgets) . ');';
						break;

					// force LARGE width : (100% / N) + (100% / (N * 4))
					case 'large':
						$width = 'width: calc((100% / ' . count($widgets) . ') + (100% / ' . (count($widgets) * 4) . '));';
						break;

					// force EXTRA LARGE width : (100% / N) + (100% / (N * 2))
					case 'extra-large':
						$width = 'width: calc((100% / ' . count($widgets) . ') + (100% / ' . (count($widgets) * 2) . '));';
						break;

					// fallback to flex basis to take the remaining space
					default:
						$width = 'flex: 1;';
				}
				?>
				<div
					class="dashboard-widget"
					id="widget-<?php echo $this->escape($id); ?>"
					data-widget="<?php echo $this->escape($widget->getName()); ?>"
					style="<?php echo $this->escape($width); ?>"
				>

					<div class="widget-wrapper">
						<div class="widget-head">
							<h3><?php echo $widget->getTitle() . $help; ?></h3>

							<a href="javascript:void(0);" onclick="openWidgetConfiguration('<?php echo $id; ?>');" class="widget-config-btn">
								<i class="fas fa-ellipsis-h"></i>
							</a>
						</div>

						<div class="widget-body">
							<?php echo $widget->display(); ?>
						</div>

						<div class="widget-error-box" style="display: none;">
							<?php
							echo $this->formFactory->createField()
								->type('alert')
								->style('error')
								->text(JText::translate('VRE_AJAX_GENERIC_ERROR'))
								->hidden(true);
							?>
						</div>
					</div>

				</div>
				<?php
			}
			?>

		</div>
	
	<?php endforeach; ?>

</div>
