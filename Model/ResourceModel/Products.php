<?php declare(strict_types=1);
/**
 * @copyright 2020 Ceymox. All rights reserved
 * @author Anzz
 *
 */

namespace Ceymox\ProductImport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Products extends AbstractDb
{
    public function _construct()
    {
        $this->_init('catalog_category_product', 'entity_id');
    }
}
