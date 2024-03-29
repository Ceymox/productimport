<?php declare(strict_types=1);
/**
 * @copyright 2020 Ceymox. All rights reserved
 * @author Anzz
 *
 */

namespace Ceymox\ProductImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Filesystem\Io\File;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;

class Upload extends Action
{
    /**
     * @var Data
     */
    private $jsonHelper;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        Data $jsonHelper
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->jsonHelper = $jsonHelper;
        
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'csv_uploader']);
            $workingDir = $this->varDirectory->getAbsolutePath('tmp/');
            $result = $uploader->save($workingDir, 'datasheet-productsList.csv');
           
            return $this->jsonResponse(['error' => "File was successfully uploaded! You can import data."]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($response));
    }
}
