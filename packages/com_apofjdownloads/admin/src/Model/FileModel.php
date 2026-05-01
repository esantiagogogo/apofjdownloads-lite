<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\Model;

use Apotentia\Library\ApofjDownloads\Mime\MimeDetector;
use Apotentia\Library\ApofjDownloads\Storage\LocalAdapter;
use Apotentia\Library\ApofjDownloads\Upload\FileUploadHandler;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * File item model.
 *
 * Handles file CRUD with upload processing: stores files via the
 * LocalAdapter, computes SHA-256/MD5 hashes, and verifies MIME type
 * server-side using finfo.
 */
class FileModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var string
     */
    public $typeAlias = 'com_apofjdownloads.file';

    /**
     * Get the form for the item.
     */
    public function getForm($data = [], $loadData = true): Form|false
    {
        $form = $this->loadForm(
            'com_apofjdownloads.file',
            'file',
            ['control' => 'jform', 'load_data' => $loadData],
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Save the file record, processing any uploaded file.
     *
     * If a file was uploaded, the handler stores it, computes hashes and
     * MIME type, and populates the record metadata before the DB save.
     */
    public function save($data): bool
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
        $files = $input->files->get('jform', [], 'raw');

        // Check if a file was uploaded
        if (!empty($files['upload_file']) && $files['upload_file']['error'] === UPLOAD_ERR_OK) {
            try {
                $result = $this->processFileUpload($files['upload_file'], (int) ($data['download_id'] ?? 0));

                // Populate the record from the upload result
                $data['filename']      = $result->filename;
                $data['filepath']      = $result->filepath;
                $data['size']          = $result->size;
                $data['mime_type']     = $result->mimeType;
                $data['mime_verified'] = $result->mimeVerified ? 1 : 0;
                $data['hash_sha256']   = $result->hashSha256;
                $data['hash_md5']      = $result->hashMd5;

                $this->verifyStoredFileCompliance((int) $data['size']);
            } catch (\RuntimeException $e) {
                $this->setError($e->getMessage());

                return false;
            }
        }

        return parent::save($data);
    }

    /**
     * Load the data for the form.
     */
    protected function loadFormData(): mixed
    {
        $data = Factory::getApplication()->getUserState(
            'com_apofjdownloads.edit.file.data',
            [],
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     */
    protected function prepareTable($table): void
    {
        $date = Factory::getDate();

        if (empty($table->id)) {
            $table->created = $date->toSql();

            if (empty($table->ordering)) {
                $db = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from('#__apofjdl_files');
                $db->setQuery($query);
                $max = (int) $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    /**
     * Publish/unpublish files.
     */
    public function publish(&$pks, $value = 1): bool
    {
        return parent::publish($pks, $value);
    }

    /**
     * Delete a file record and its stored file.
     *
     * @param  int[]  &$pks  Array of record IDs to delete.
     */
    public function delete(&$pks): bool
    {
        $storage = $this->getStorageAdapter();

        foreach ($pks as $pk) {
            $table = $this->getTable();
            $table->load($pk);

            // Remove stored file if it exists
            if (!empty($table->filepath)) {
                $storage->delete($table->filepath);
            }
        }

        return parent::delete($pks);
    }

    /**
     * Process a file upload through the handler.
     *
     * @throws \RuntimeException
     */
    private function processFileUpload(array $fileData, int $downloadId): \Apotentia\Library\ApofjDownloads\Upload\FileUploadResult
    {
        $storage = $this->getStorageAdapter();
        $mimeDetector = new MimeDetector();
        $handler = new FileUploadHandler($storage, $mimeDetector);

        return $handler->processUpload($fileData, $downloadId);
    }

    private function verifyStoredFileCompliance(int $fileSize): void
    {
        $liteMaximum = 5 * 1024 * 1024;

        if (!$this->hasProEdition() && $fileSize > $liteMaximum) {
            throw new \RuntimeException(
                'File exceeds the free edition size limit. Upgrade to APO FJ Downloads Pro.',
            );
        }
    }

    private function hasProEdition(): bool
    {
        $editionFile = JPATH_ADMINISTRATOR . '/components/com_apofjdownloads/.edition';

        if (!file_exists($editionFile)) {
            return false;
        }

        $content = trim((string) file_get_contents($editionFile));

        return strlen($content) === 64 && ctype_xdigit($content);
    }

    /**
     * Get the storage adapter configured for this component.
     */
    private function getStorageAdapter(): LocalAdapter
    {
        $params = ComponentHelper::getParams('com_apofjdownloads');
        $storagePath = $params->get('file_storage_path', 'media/com_apofjdownloads/files');

        // Resolve relative paths against Joomla root
        if (!str_starts_with($storagePath, '/')) {
            $storagePath = JPATH_ROOT . '/' . $storagePath;
        }

        return new LocalAdapter($storagePath);
    }
}
