<?php

namespace Sillove\ProductInquiry\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Productinquiry extends AbstractDb
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * Product Inquiry Construct
     *
     * @param Context $context
     * @param DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        Context $context,
        DateTime $date,
        ?string $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Product Inquiry Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sillove_productinquiry_admingrid', 'id');
    }
}
