<?php

declare(strict_types=1);

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Apotentia\Component\ApofjDownloads\Administrator\View\Logs\HtmlView $this */

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$user      = Factory::getApplication()->getIdentity();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_apofjdownloads&view=logs'); ?>"
      method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table itemList" id="logList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_APOFJDOWNLOADS_LOGS_TABLE_CAPTION'); ?>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-15">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_APOFJDOWNLOADS_HEADING_DOWNLOADED_AT', 'a.downloaded_at', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_APOFJDOWNLOADS_HEADING_DOWNLOAD_TITLE', 'download_title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_APOFJDOWNLOADS_HEADING_USERNAME', 'username', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_APOFJDOWNLOADS_HEADING_STATUS', 'a.status', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-15 d-none d-lg-table-cell">
                                    <?php echo Text::_('COM_APOFJDOWNLOADS_HEADING_IP_HASH'); ?>
                                </th>
                                <th scope="col" class="w-15 d-none d-lg-table-cell">
                                    <?php echo Text::_('COM_APOFJDOWNLOADS_HEADING_USER_AGENT'); ?>
                                </th>
                                <th scope="col" class="w-3 d-none d-lg-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    </td>
                                    <td>
                                        <?php echo HTMLHelper::_('date', $item->downloaded_at, Text::_('DATE_FORMAT_LC5')); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->escape($item->download_title ?? '—'); ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo $this->escape($item->username ?? Text::_('COM_APOFJDOWNLOADS_GUEST')); ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell">
                                        <span class="badge bg-<?php echo $item->status === 'completed' ? 'success' : 'danger'; ?>">
                                            <?php echo $this->escape($item->status); ?>
                                        </span>
                                    </td>
                                    <td class="small d-none d-lg-table-cell text-truncate" style="max-width: 120px;">
                                        <?php echo $this->escape(substr($item->ip_hash, 0, 16) . '…'); ?>
                                    </td>
                                    <td class="small d-none d-lg-table-cell text-truncate" style="max-width: 150px;">
                                        <?php echo $this->escape($item->user_agent ?: '—'); ?>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <?php echo (int) $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
