<?php declare(strict_types=1);
/**
 * @copyright 2020 Ceymox. All rights reserved
 * @author Anzz
 *
 */

namespace Ceymox\ProductImport\Model;

use Magento\Framework\Model\AbstractModel;

class Products extends AbstractModel
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ceymox\ProductImport\Model\ResourceModel\Products');
    }
}
