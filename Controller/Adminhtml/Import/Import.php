<?php declare(strict_types=1);
/**
 * @copyright 2020 Ceymox. All rights reserved
 * @author Anzz
 *
 */

namespace Ceymox\ProductImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;
use Exception;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product;

class Import extends Action
{
    /**
     * @var Csv
     */
    private $csv;
    /**
     * @var DirectoryList
     */
    private $directoryList;
     /**
      * @var CategoryLinkManagementInterface
      */
    private $categoryLinkManagement;
    /**
     * @var CategoryLinkRepositoryInterface
     */
    private $categoryLinkRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Product
     */
    private $product;
    
    /**
     * Import constructor.
     * @param Action\Context $context
     * @param Csv $csv
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Action\Context $context,
        Csv $csv,
        DirectoryList $directoryList,
        CategoryLinkManagementInterface $categoryLinkManagement,
        ProductRepository $productRepository,
        Product $product,
        CategoryLinkRepositoryInterface $categoryLinkRepository
    ) {
        $this->csv                  = $csv;
        $this->directoryList        = $directoryList;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->_productRepository = $productRepository;
        $this->product = $product;
        parent::__construct($context);
    }

    /**
     * Import
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $tmpDir = $this->directoryList->getPath('tmp');
        $file = $tmpDir . '/datasheet-productsList.csv';
        $csv = $this->csv;
        $csv->setDelimiter(',');
        $skus = $csv->getData($file);
        $postdata = $this->getRequest()->getPostValue();
        $action = $postdata['action'];
        $categoryIds = $postdata['catalog'];
        $errors = $success = [];
        foreach ($categoryIds as $categoryId) {
            foreach ($skus as $sku) {
                $sku = $sku[0];
                if ($sku!="sku") {
                    if ($action == 1) {
                        if ($this->insertProducts($sku, $categoryIds)) {
                            $success[] = $sku;
                        } else {
                            $errors[] = $sku;
                        }
                            $actionmsg = "Inserted to the selected categories";
                    } else {
                        if ($this->removeProducts($categoryId, $sku)) {
                            $success[] = $sku;
                        } else {
                            $errors[] = $sku;
                        }
                            $actionmsg = "Removed from the selected categories";
                    }
                }
            }
        }
        $success = array_unique($success);
        $errors = array_unique($errors);
        $this->messages($success, $errors, $actionmsg);
        return $resultRedirect->setPath('ceymox_productimport/import/index');
    }

    /**
     * Checking existance of products in csv
     *
     * @return @boolean
     */
    public function productExist($sku)
    {
        
        if ($this->product->getIdBySku($sku)) {
            return true;
        }
    }

    /**
     * Function for Insert SKUs to selected Categories
     *
     * @return @boolean
     */
    public function insertProducts($sku, $categoryIds)
    {
        if ($this->productExist($sku)) {
            $pdct = $this->_productRepository->get($sku);
            $pdctcategories = $pdct->getCategoryIds();
            $categoryIds = array_unique(array_merge($pdctcategories, $categoryIds), SORT_REGULAR);
            $this->categoryLinkManagement->assignProductToCategories($sku, $categoryIds);
            return true;
        }
    }
    
    /**
     * Function for Remove SKUs From selected Categories
     *
     * @return @boolean
     */
    public function removeProducts($categoryId, $sku)
    {
        if ($this->productExist($sku)) {
            $pdct = $this->_productRepository->get($sku);
            $pdctcategories = $pdct->getCategoryIds();
            $exist = in_array($categoryId, $pdctcategories);
            if ($exist) {
                $this->categoryLinkRepository->deleteByIds($categoryId, $sku);
            }
            return true;
        }
    }

    /**
     * Function for Create Messages
     *
     * @return @boolean
     */
    public function messages($success, $errors, $actionmsg)
    {
        
        if ($success == null) {
            $message = "Sorry, No Products in the csv are Existing. Please check the sku's before import";
            return $this->messageManager->addError($message);
        } elseif ($errors!=null) {
            $message = "All existing Products are $actionmsg";
            $error ="Some products in the csv are not existing : ".implode(", ", $errors);
            return $this->messageManager->addSuccess($message)->addError($error);
        } else {
            $message = "All Products in the csv are successfully $actionmsg";
            return $this->messageManager->addSuccess($message);
        }
    }
}
