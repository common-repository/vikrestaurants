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

$uri = 'index.php?option=com_vikrestaurants&task=orderdish.start&table=' . $this->table->secretkey;

JText::script('VRQRCODE_REGENERATE_WARN');
JText::script('VRSYSTEMCONNECTIONERR');

?>

<!-- QR CODE - Image -->

<?php
echo $this->formFactory->createField()
    ->hiddenLabel(true)
    ->description(JText::translate('VRQRCODE_TABLE_DESC'))
    ->render(function($data) use ($uri) {
        ?>
        <div style="text-align: center; margin-bottom: 10px;">
            <?php
            echo JHtml::fetch('vikrestaurants.qr', $uri, [
                'image' => true,
                'attrs' => [
                    'id'    => 'qrimg',
                    'style' => 'max-width: 100%',
                ],
            ]);
            ?>

            <div class="loading-overlay static shifted" style="display: none;">
                <div class="vr-loading-tmpl">
                    <div class="spinner size2x dark">
                        <div class="double-bounce1"></div>
                        <div class="double-bounce2"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    });
?>

<!-- ACTIONS - Buttons -->

<?php
$helpButton = $this->formFactory->createField()
    ->type('button')
    ->hidden(true)
    ->text('<i class="fas fa-life-ring"></i> ' . JText::translate('JTOOLBAR_HELP'))
    ->onclick('helpTableQR()')
    ->style('margin-right: 6px;');

$printButton = $this->formFactory->createField()
    ->type('button')
    ->hidden(true)
    ->text('<i class="fas fa-print"></i> ' . JText::translate('VRPRINT'))
    ->onclick('printTableQR()');

$regenerateButton = $this->formFactory->createField()
    ->type('button')
    ->hidden(true)
    ->text('<i class="fas fa-sync-alt"></i> ' . JText::translate('VRQRCODE_REGENERATE'))
    ->onclick('regenerateTableQR(this)');

echo $this->formFactory->createField()
    ->hiddenLabel(true)
    ->render(function($data) use ($helpButton, $printButton, $regenerateButton) {
        ?>
        <div class="btn-group pull-left">
            <?php echo $helpButton; ?>
        </div>
        <div class="btn-group pull-left">
            <?php echo $printButton; ?>
        </div>
        <div class="btn-group pull-right">
            <?php echo $regenerateButton; ?>
        </div>
        <?php
    });

// display help modal
echo JHtml::fetch(
    'bootstrap.renderModal',
    'jmodal-qrcode-help',
    [
        'title'       => JText::translate('JTOOLBAR_HELP'),
        'closeButton' => true,
        'keyboard'    => true, 
        'bodyHeight'  => 80,
    ],
    $this->loadTemplate('qrcode_help')
);
?>

<script>
    (function($, w) {
        'use strict';

        const openJModal = (id, url, jqmodal) => {
            <?php echo VREApplication::getInstance()->bootOpenModalJS(); ?>
        }

        w.helpTableQR = () => {
            /**
             * @todo consider to open a popup from the knowledge base
             */
            openJModal('qrcode-help');   
        }
        
        w.printTableQR = () => {
            const qr = $('#qrimg').clone()[0].outerHTML;
        
            // write the proper HTML source onto a new window
            let printFrame = window.open('', '_blank');
            printFrame.document.write(qr);
            printFrame.document.close();
            
            // delay the print command to avoid printing a blank page
            setTimeout(() => {
                printFrame.focus();
                printFrame.print();
                printFrame.close();
            }, 512);
        }

        w.regenerateTableQR = (button) => {
            if (!confirm(Joomla.JText._('VRQRCODE_REGENERATE_WARN'))) {
                // action aborted by the user
                return false;
            }

            $('.loading-overlay').show();
            $(button).prop('disabled', true);

            new Promise((resolve, reject) => {
                UIAjax.do(
                    '<?php echo VREFactory::getPlatform()->getUri()->ajax('index.php?option=com_vikrestaurants&task=table.refreshqr'); ?>',
                    {
                        id: <?php echo (int) $this->table->id ?>,
                    },
                    (qr) => {
                        resolve(qr);
                    },
                    (error) => {
                        reject(error.responseText || Joomla.JText._('VRSYSTEMCONNECTIONERR'));
                    }
                )
            }).then((qr) => {
                // replace existing QR code with the new one
                $('#qrimg').replaceWith(qr);
            }).catch((error) => {
                // an error has occurred
                setTimeout(() => {
                    alert(error);
                }, 128);
            }).finally(() => {
                $('.loading-overlay').hide();
                $(button).prop('disabled', false);
            });
        }
    })(jQuery, window);
</script>