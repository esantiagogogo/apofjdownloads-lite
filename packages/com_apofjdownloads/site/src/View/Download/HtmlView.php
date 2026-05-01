<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\View\Download;

use Apotentia\Library\ApofjDownloads\Layout\LayoutEngine;
use Apotentia\Library\ApofjDownloads\Layout\LayoutType;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Single download detail view for the site frontend.
 */
class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $files;
    protected $params;
    public string $renderedOutput = '';
    private ?LayoutEngine $layoutEngine = null;

    public function setLayoutEngine(LayoutEngine $engine): void
    {
        $this->layoutEngine = $engine;
    }

    public function display($tpl = null): void
    {
        /** @var \Apotentia\Component\ApofjDownloads\Site\Model\DownloadModel $model */
        $model = $this->getModel();

        $this->item  = $model->getItem();
        $this->files = $this->item ? $model->getFiles((int) $this->item->id) : [];

        // Increment hit counter
        if ($this->item) {
            $model->hit((int) $this->item->id);
        }

        if ($this->layoutEngine !== null && $this->item) {
            $license = null;

            if (!empty($this->item->license_title)) {
                $license = (object) [
                    'title' => $this->item->license_title,
                    'body' => $this->item->license_body ?? '',
                    'require_agree' => (bool) ($this->item->license_require_agree ?? false),
                ];
            }

            $this->renderedOutput = $this->layoutEngine->render(
                LayoutType::DOWNLOAD_DETAIL,
                [
                    'download' => $this->item,
                    'files' => $this->files,
                    'license' => $license,
                    'canDownload' => true,
                ],
            );
        }

        parent::display($tpl);
    }
}
