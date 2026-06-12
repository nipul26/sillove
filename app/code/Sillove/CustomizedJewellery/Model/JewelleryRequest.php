<?php
namespace Sillove\CustomizedJewellery\Model;

use Magento\Framework\Model\AbstractModel;

class JewelleryRequest extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Sillove\CustomizedJewellery\Model\ResourceModel\JewelleryRequest::class);
    }
}
