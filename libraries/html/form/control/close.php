<?php
/** 
 * @package     VikRestaurants - Libraries
 * @subpackage  html.form
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
 * @var   string   $class        The control class.
 * @var   string   $label        The field label.
 * @var   string   $description  An optional field description.
 * @var   string   $id           The field ID.
 * @var   string   $idparent     The control ID.
 * @var   boolean  $required     True if the field is required.
 * @var   array    $attr         An array of attributes.
 */

?>

        <?php if (!empty($description)): ?>
            <div class="hide-aware-inline-help">
                <small class="form-text"><?php echo JText::translate($description); ?></small>
            </div>
        <?php endif; ?>

	</div>

</div>
