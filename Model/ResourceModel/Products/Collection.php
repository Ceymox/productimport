<?php declare(strict_types=1);
/**
 * @copyright 2020 Ceymox. All rights reserved
 * @author Anzz
 *
 */

namespace Ceymox\ProductImport\Model\ResourceModel\Products;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    public function _construct()
    {
        $this->_init('Ceymox\ProductImport\Model\Products', 'Ceymox\ProductImport\Model\ResourceModel\Products');
    }
}
