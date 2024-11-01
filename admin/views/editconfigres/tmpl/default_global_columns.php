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

$params = $this->params;

$vik = VREApplication::getInstance();

/**
 * Trigger event to display custom HTML.
 * In case it is needed to include any additional fields,
 * it is possible to create a plugin and attach it to an event
 * called "onDisplayViewConfigresGlobalColumns". The event method
 * receives the view instance as argument.
 *
 * @since 1.9
 */
$forms = $this->onDisplayView('GlobalColumns');

?>

<!-- RESERVATIONS LIST COLUMNS -->

<div class="config-fieldset">

	<div class="config-fieldset-head">
		<h3><?php echo JText::translate('VRMANAGECONFIG38'); ?></h3>
	</div>

	<div class="config-fieldset-body">
	
		<?php 
		echo $vik->alert(JText::translate('VRMANAGECONFIG38_DESC'), 'info');
		
		$all_list_fields = [
			'1'  => 'id',
			'2'  => 'sid',
			'20' => 'payment',
			'3'  => 'checkin_ts',
			'4'  => 'people',
			'VRMANAGETABLE4'  => 'rname',
			'5'  => 'tname',
			'18' => 'customer',
			'6'  => 'mail', 
			'16' => 'phone',
			'7'  => 'info',
			'8'  => 'coupon',
			'10' => 'billval',
			'19' => 'rescode',
			'12' => 'status',
		];

		$listable_fields = [];

		if (!empty($params['listablecols']))
		{
			$listable_fields = explode(',', $params['listablecols']);
		}
		
		foreach ($all_list_fields as $k => $f)
		{
			$checked = (int) in_array($f, $listable_fields); 

			if (preg_match("/^\d+$/", $k))
			{
				$lk = 'VRMANAGERESERVATION' . $k;
			}
			else
			{
				$lk = $k;
			}

			// display checkbox
			echo $this->formFactory->createField([
				'type'     => 'checkbox',
				'name'     => $f . 'listcol',
				'checked'  => $checked,
				'label'    => JText::translate($lk),
				'onchange' => 'toggleListField(\'' . $f . '\', this.checked)',
			]);

			// related hidden field for save purposes
			echo $this->formFactory->createField([
				'type'     => 'hidden',
				'name'     => 'listablecols',
				'multiple' => true,
				'value'    => $f . ':' . $checked,
				'id'       => 'vrhidden' . $f,
			]);
		} 
		?>

		<!-- Define role to detect the supported hook -->
		<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalColumns","key":"basic","type":"field"} -->

		<?php   
		/**
		 * Look for any additional fields to be pushed within
		 * the Global > Columns > Reservations List Columns fieldset.
		 *
		 * @since 1.9
		 */
		if (isset($forms['basic']))
		{
			echo $forms['basic'];

			// unset details form to avoid displaying it twice
			unset($forms['basic']);
		}
		?>

	</div>

</div>

<!-- CUSTOM FIELDS -->

<?php
// do not allow the selection of some rules, which are already 
// supported by the system as default columns (name, email, phone)
$fields = $this->customFields->filter(new class implements E4J\VikRestaurants\Collection\CollectionFilter {
	public function match(E4J\VikRestaurants\Collection\Item $item) {
		return !in_array($item->get('rule'), ['nominative', 'email', 'phone']);
	}
});

if ($fields): ?>
	<div class="config-fieldset">

		<div class="config-fieldset-head">
			<h3><?php echo JText::translate('VRMENUCUSTOMFIELDS'); ?></h3>
		</div>

		<div class="config-fieldset-body">
		
			<?php
			echo $vik->alert(JText::translate('VRCONFIG_CFCOLUMNS_TIP'), 'info');

			$listable_fields = explode(',', $params['listablecf']);
			
			foreach ($fields as $field)
			{
				$checked = (int) in_array($field->id, $listable_fields);

				// display checkbox
				echo $this->formFactory->createField([
					'type'     => 'checkbox',
					'name'     => 'listcolcf' . $field->id,
					'checked'  => $checked,
					'label'    => $field->langname,
					'onchange' => 'toggleListCustomField(\'' . $field->id . '\', this.checked)',
				]);

				// related hidden field for save purposes
				echo $this->formFactory->createField([
					'type'     => 'hidden',
					'name'     => 'listablecf',
					'multiple' => true,
					'value'    => $field->id . ':' . $checked,
					'id'       => 'vrcfhidden' . $field->id,
				]);
			}
			?>

		</div>

	</div>
<?php endif; ?>

<!-- Define role to detect the supported hook -->
<!-- {"rule":"customizer","event":"onDisplayViewConfigresGlobalColumns","type":"fieldset"} -->

<?php
/**
 * Iterate remaining forms to be displayed as new fieldsets
 * within the Global > Columns tab.
 *
 * @since 1.9
 */
foreach ($forms as $formTitle => $formHtml)
{
	?>
	<div class="config-fieldset">
		
		<div class="config-fieldset-head">
			<h3><?php echo JText::translate($formTitle); ?></h3>
		</div>

		<div class="config-fieldset-body">
			<?php echo $formHtml; ?>
		</div>
		
	</div>
	<?php
}
?>

<script>
	(function($, w) {
		'use strict';

		w.toggleListField = (field, checked) => {
			$('#vrhidden' + field).val(field + ':' + (checked ? 1 : 0));
		}

		w.toggleListCustomField = (id, checked) => {
			$('#vrcfhidden' + id).val(id + ':' + (checked ? 1 : 0));
		}
	})(jQuery, window);
</script>