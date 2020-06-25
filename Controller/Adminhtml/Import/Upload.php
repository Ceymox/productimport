<?php declare(strict_types=1);
/**
 * @copyright 2020 Ceymox. All rights reserved
 * @author Anzz
 *
 */

namespace Ceymox\ProductImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Filesystem\Io\File;

class Upload extends Action
{
    private $directoryList;
    private $jsonHelper;
    /** @var File */
    private $file;

    public function __construct(
        Action\Context $context,
        DirectoryList $directoryList,
        JsonHelper $jsonHelper,
        File $file
    ) {
        $this->_jsonHelper = $jsonHelper;
        $this->_directoryList = $directoryList;
        $this->file = $file;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $tmpDir = $this->_directoryList->getPath('tmp');
            $ext = $this->file->getPathInfo('import_products_list.csv')['extension'];
            move_uploaded_file(
                $this->getRequest()
                ->getFiles("csv_uploader")['tmp_name'],
                $tmpDir . "/datasheet-productsList." . $ext
            );
            return $this->jsonResponse(['error' => "File was successfully uploaded! You can import data."]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($response));
    }
}
