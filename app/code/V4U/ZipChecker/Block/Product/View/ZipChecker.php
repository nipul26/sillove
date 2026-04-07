<?php

namespace V4U\ZipChecker\Block\Product\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use V4U\ZipChecker\Helper\Data as DataHelper;

/**
 * Class ZipChecker
 * @package V4U\ZipChecker\Block\Product\View
 */

class ZipChecker extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * ZipChecker constructor.
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $dataHelper,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
    
    public function getIsActive()
    {
        return $this->dataHelper->getIsActive();
    }
}
