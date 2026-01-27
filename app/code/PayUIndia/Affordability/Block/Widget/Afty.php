<?php

namespace PayUIndia\Affordability\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Afty extends Template implements BlockInterface
{

    protected $_template = "PayUIndia_Affordability::widget/aftytemplate.phtml";
	
	protected $cataloghelper;
	
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Helper\Data $catalogHelper,
        array $data = []
    )
    {        
        $this->cataloghelper = $catalogHelper;
        parent::__construct($context, $data);
    }
	
	
	public function getPrice()
	{
		return round($this->cataloghelper->getProduct()->getPrice(),2);
	}
}