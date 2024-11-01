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

$product = $this->product;

?>

<?php if ($product->hidden == 0): ?>

	<!-- PUBLISHED - Checkbox -->

	<?php
	echo $this->formFactory->createField()
		->type('checkbox')
		->name('published')
		->checked($product->published)
		->label(JText::translate('VRMANAGEROOM3'));
	?>

<?php endif; ?>

<!-- TAGS - Select -->

<?php
$tags = [];

foreach (JHtml::fetch('vikrestaurants.tags', 'products') as $tag)
{
	$tags[] = $tag->name;
}

// construct tags field
$hiddenTags = $this->formFactory->createField()
	->type('hidden')
	->name('tags')
	->value((string) (is_array($product->tags) ? implode(',', $this->product->tags) : $product->tags));

// wrap hidden field into a control
echo $this->formFactory->createField()
	->label(JText::translate('VRTAGS'))
	->render(function($data, $input) use ($hiddenTags) {
		echo $hiddenTags;
	});

JText::script('VRETAGPLACEHOLDER');
?>

<script>
	(function($) {
		'use strict';

		$(function() {
			$('#adminForm input[name="tags"]').select2({
				placeholder: Joomla.JText._('VRETAGPLACEHOLDER'),
				allowClear: true,
				tags: <?php echo json_encode($tags); ?>,
				tokenSeparators: [','],
				width: '100%',
			});
		});
	})(jQuery);
</script>