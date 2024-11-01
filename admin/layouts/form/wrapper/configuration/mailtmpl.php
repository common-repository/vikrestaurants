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

/**
 * Layout variables
 * -----------------
 * @var  string   $input  The default rendered input.
 * @var  string   $id     The element identifier.
 * @var  string   $alias  The e-mail template alias.
 * @var  string   $group  The e-mail template group (restaurant or takeaway).
 * @var  string   $path   The path where the e-mail should be loaded.
 */
extract($displayData);

?>

<div class="inline-fields">
    <?php echo $input; ?>

    <div class="btn-group flex-auto">
        <button type="button" class="btn" id="vre-<?php echo $id; ?>-edit-btn">
            <i class="fas fa-pen"></i>
        </button>

        <button type="button" class="btn" id="vre-<?php echo $id; ?>-preview-btn">
            <i class="fas fa-fill-drip"></i>
        </button>
    </div>
</div>

<?php
// email template modal
echo JHtml::fetch(
    'bootstrap.renderModal',
    'jmodal-managetmpl-' . $id,
    array(
        'title'       => JText::translate('VRJMODALEMAILTMPL'),
        'backdrop'    => 'static', // does not close the modal when clicked outside
        'closeButton' => true,
        'keyboard'    => false, 
        'bodyHeight'  => 80,
        'url'         => '',
        'footer'      => '<button type="button" class="btn" data-role="file.savecopy">' . JText::translate('VRSAVEASCOPY') . '</button>'
                       . '<button type="button" class="btn btn-success" data-role="file.save">' . JText::translate('JAPPLY') . '</button>',
    )
);
?>

<script>
    (function($, w) {
        'use strict';

        let SELECTED_MAIL_TMPL_FIELD = null;

        const openMailTemplateModal = (id) => {
            // register related dropdown
            SELECTED_MAIL_TMPL_FIELD = $('select[name="' + id + '"]');

            // get file name
            let file = SELECTED_MAIL_TMPL_FIELD.val();
            // build path of selected file
            let path = '<?php echo addslashes($path); ?>' + file;

            // create management URL
            let url = 'index.php?option=com_vikrestaurants&tmpl=component&task=file.edit&cid[]=' + btoa(path);

            openModal('managetmpl-<?php echo $id; ?>', url, true);
        }

        const openMailPreview = (id, alias) => {
            // define base URL
            let url = 'index.php?option=com_vikrestaurants&view=mailpreview';
            // append template group
            url += '&group=<?php echo $group; ?>';
            // append template alias
            url += '&alias=' + alias;
            // extract mail template from select
            url += '&file=' + $('select[name="' + id + '"]').val();
            // always use current language
            url += '&langtag=<?php echo JFactory::getLanguage()->getTag(); ?>';

            // open URL in a blank tab of the browser
            window.open(url, '_blank');
        }

        const addTemplateFileIntoSelect = (file, selector) => {
            if (selector.find('option[value="' + file.name + '"]').length) {
                // file already in list
                return false;
            }

            // prettify name
            let name = file.name.replace(/\.php$/, '');
            name = name.replace(/[_-]+/g, ' ');
            name = name.split(' ').map((s) => {
                return s.charAt(0).toUpperCase() + s.slice(1);
            }).join(' ');

            // insert new option within the select
            $(selector).each(function() {
                $(this).append('<option value="' + file.name + '" data-path="' + file.path + '">' + name + '</option>');
            });

            return true;
        }

        const openModal = (id, url, jqmodal) => {
            <?php echo VREApplication::getInstance()->bootOpenModalJS(); ?>
        }

        $(function() {
            $('#vre-<?php echo $id; ?>-edit-btn').on('click', () => {
                openMailTemplateModal('<?php echo $id; ?>');
            });

            $('#vre-<?php echo $id; ?>-preview-btn').on('click', () => {
                openMailPreview('<?php echo $id; ?>', '<?php echo $alias; ?>');
            });

            $('#jmodal-managetmpl-<?php echo $id; ?> button[data-role="file.save"]').on('click', () => {
                // trigger click of save button contained in managefile view
                window.modalFileSaveButton.click();
            });

            $('#jmodal-managetmpl-<?php echo $id; ?> button[data-role="file.savecopy"]').on('click', () => {
                // trigger click of savecopy button contained in managefile view
                window.modalFileSaveCopyButton.click();
            });

            $('#jmodal-managetmpl-<?php echo $id; ?>').on('hidden', () => {
                // check if the file was saved
                if (window.modalSavedFile) {
                    let selector = $('select[name="<?php echo $id; ?>"]');

                    // insert file in all template dropdowns
                    if (addTemplateFileIntoSelect(window.modalSavedFile, selector)) {
                        // auto-select new option for the related select
                        $(SELECTED_MAIL_TMPL_FIELD).select2('val', window.modalSavedFile.name);
                    }
                }
            });
        });
    })(jQuery, window);
</script>