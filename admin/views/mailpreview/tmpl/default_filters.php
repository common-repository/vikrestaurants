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

<div class="inspector-form" id="inspector-filters-form">

    <?php echo $vik->bootStartTabSet('mailpreview', ['active' => 'mailpreview_settings']); ?>

        <!-- SETTINGS -->

        <?php echo $vik->bootAddTab('mailpreview', 'mailpreview_settings', JText::translate('VRE_SETTINGS_FIELDSET')); ?>

            <div class="inspector-fieldset">

                <!-- GROUP - Select -->

                <?php
                echo $this->formFactory->createField()
                    ->type('select')
                    ->id('mailpreview_filter_group')
                    ->value($this->filters['group'])
                    ->label(JText::translate('VRMANAGECUSTOMF7'))
                    ->options(JHtml::fetch('vrehtml.admin.groups', ['restaurant', 'takeaway']));
                ?>

                <!-- ALIAS (RESTAURANT) - Select -->

                <?php
                echo $this->formFactory->createField()
                    ->type('select')
                    ->id('mailpreview_filter_alias_restaurant')
                    ->value($this->filters['alias'])
                    ->label(JText::translate('VRMANAGETKAREA2'))
                    ->options($this->supportedAliases['restaurant'])
                    ->control([
                        'class'   => 'restaurant-group-related-field group-related-field',
                        'visible' => false,
                    ]);
                ?>

                <!-- ALIAS (TAKEAWAY) - Select -->

                <?php
                echo $this->formFactory->createField()
                    ->type('select')
                    ->id('mailpreview_filter_alias_takeaway')
                    ->label(JText::translate('VRMANAGETKAREA2'))
                    ->value($this->filters['alias'])
                    ->options($this->supportedAliases['takeaway'])
                    ->control([
                        'class'   => 'takeaway-group-related-field group-related-field',
                        'visible' => false,
                    ]);
                ?>

                <!-- LANGUAGE - Select -->

                <?php
                echo $this->formFactory->createField()
                    ->type('select')
                    ->id('mailpreview_filter_langtag')
                    ->value($this->filters['langtag'])
                    ->label(JText::translate('VRMANAGELANG4'))
                    ->options(JHtml::fetch('contentlanguage.existing'));
                ?>

                <!-- ENABLE INSPECTOR - Checkbox -->

                <?php
                // toggle customizer inspector
                echo $this->formFactory->createField()
                    ->type('checkbox')
                    ->id('mailpreview_filter_customizer_inspector')
                    ->checked(false)
                    ->label(JText::translate('VRE_CUSTOMIZER_ENABLE_INSPECTOR'))
                    ->description(JText::translate('VRE_CUSTOMIZER_ENABLE_INSPECTOR_DESC'));
                ?>

                <!-- ENABLE CONDITIONAL TEXTS - Checkbox -->

                <?php
                // toggle customizer inspector
                echo $this->formFactory->createField()
                    ->type('checkbox')
                    ->id('mailpreview_filter_customizer_mailtext')
                    ->checked(false)
                    ->label(JText::translate('VRE_CUSTOMIZER_ENABLE_MAILTEXT'))
                    ->description(JText::translate('VRE_CUSTOMIZER_ENABLE_MAILTEXT_DESC'));
                ?>

            </div>

        <?php echo $vik->bootEndTab(); ?>

        <!-- ADDITIONAL CSS -->

        <?php echo $vik->bootAddTab('mailpreview', 'mailpreview_css', JText::translate('VRE_CUSTOMIZER_TAB_ADDITIONALCSS')); ?>

            <div class="inspector-fieldset">

                <!-- CSS EDITOR - Editor -->

                <?php
                echo $this->formFactory->createField()
                    ->type('editor')
                    ->name('mailpreview_filter_customizer_css')
                    ->editor('codemirror')
                    ->value($this->filters['css'])
                    ->buttons(false)
                    ->hidden(true)
                    ->params(['syntax' => 'css']);
                ?>

            </div>

        <?php echo $vik->bootEndTab(); ?>

    <?php echo $vik->bootEndTabSet(); ?>

</div>

<script>
    (function($) {
        'use strict';

        const files = <?php echo json_encode($this->supportedFiles); ?>;

        const mailCssLookup = <?php echo json_encode($this->savedCSS); ?>;

        window.fillMailpreviewInspectorForm = (data) => {
            $('#mailpreview_filter_group').val(data.group).trigger('change');
            $('#mailpreview_filter_alias_' + data.group).val(data.alias);
            $('#mailpreview_filter_langtag').val(data.langtag);
            $('#mailpreview_filter_customizer_inspector').prop('checked', data.customizer || false);
            $('#mailpreview_filter_customizer_mailtext').prop('checked', data.mailtext || false);

            // always refresh the editor with the saved CSS code
            Joomla.editors.instances.mailpreview_filter_customizer_css.setValue(mailCssLookup[data.group][data.alias].trim());

            <?php
            /**
             * In WordPress the codemirror seems to have rendering problems while
             * initialized on a hidden panel. For this reason, we need to refresh
             * its contents when the inspector is open, as the editor pane might
             * be immediately visible.
             * @wponly
             */
            if (VersionListener::isWordpress()): ?>
                setTimeout(() => {
                    Joomla.editors.instances.mailpreview_filter_customizer_css.element.codemirror.refresh();
                }, 64);
            <?php endif; ?>
        }

        window.getMailpreviewInspectorData = () => {
            let data = {};

            data.group      = $('#mailpreview_filter_group').val();
            data.alias      = $('#mailpreview_filter_alias_' + data.group).val();
            data.file       = files[data.group][data.alias];
            data.langtag    = $('#mailpreview_filter_langtag').val();
            data.customizer = $('#mailpreview_filter_customizer_inspector').is(':checked');
            data.mailtext   = $('#mailpreview_filter_customizer_mailtext').is(':checked');
            data.css        = Joomla.editors.instances.mailpreview_filter_customizer_css.getValue().trim();

            // check whether the CSS has changed
            data.isChangedCSS = data.css != mailCssLookup[data.group][data.alias];

            // always refresh the lookup with the current CSS code
            mailCssLookup[data.group][data.alias] = data.css;

            return data;
        }

        $(function() {
            VikRenderer.chosen('#inspector-filters-form');

            $('#mailpreview_filter_group').on('change', function() {
                $('#inspector-filters-form .group-related-field').hide();
                $('#inspector-filters-form .' + $(this).val() + '-group-related-field').show();
            });

            $('#mailpreview_filter_group, #mailpreview_filter_alias_restaurant, #mailpreview_filter_alias_takeaway').on('change', () => {
                let group = $('#mailpreview_filter_group').val();
                let alias = $('#mailpreview_filter_alias_' + group).val();

                // always refresh the editor with the saved CSS code
                Joomla.editors.instances.mailpreview_filter_customizer_css.setValue(mailCssLookup[group][alias].trim());
            });

            <?php
            /**
             * In WordPress the codemirror seems to have rendering problems while
             * initialized on a hidden panel. For this reason, we need to refresh
             * its contents when the editor panel is clicked.
             * @wponly
             */
            if (VersionListener::isWordpress()): ?>
                $('#mailpreviewTabs a[href="#mailpreview_css"]').on('click', () => {
                    setTimeout(() => {
                        Joomla.editors.instances.mailpreview_filter_customizer_css.element.codemirror.refresh();
                    }, 64);
                });
            <?php endif; ?>
        });
    })(jQuery);
</script>