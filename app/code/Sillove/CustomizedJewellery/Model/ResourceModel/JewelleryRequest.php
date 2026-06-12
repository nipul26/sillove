<?php
namespace Sillove\CustomizedJewellery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class JewelleryRequest extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sillove_customized_jewellery', 'entity_id');
    }
}
