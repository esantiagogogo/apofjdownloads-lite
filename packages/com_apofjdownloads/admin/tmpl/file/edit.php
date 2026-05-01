<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Apotentia\Component\ApofjDownloads\Administrator\View\File\HtmlView $this */

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<form action="<?php echo Route::_('index.php?option=com_apofjdownloads&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_APOFJDOWNLOADS_TAB_DETAILS')); ?>
        <div class="row">
            <div class="col-lg-9">
                <div>
                    <?php echo $this->form->renderField('download_id'); ?>
                    <?php echo $this->form->renderField('upload_file'); ?>
                    <?php echo $this->form->renderField('filename'); ?>
                    <?php echo $this->form->renderField('filepath'); ?>
                    <?php echo $this->form->renderField('storage_adapter'); ?>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <?php echo $this->form->renderField('state'); ?>
                        <?php echo $this->form->renderField('ordering'); ?>
                        <?php echo $this->form->renderField('size'); ?>
                        <?php echo $this->form->renderField('mime_type'); ?>
                        <?php echo $this->form->renderField('mime_verified'); ?>
                        <?php echo $this->form->renderField('download_count'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'hashes', Text::_('COM_APOFJDOWNLOADS_TAB_HASHES')); ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $this->form->renderField('hash_sha256'); ?>
                <?php echo $this->form->renderField('hash_md5'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $this->form->renderField('created'); ?>
                <?php echo $this->form->renderField('id'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
