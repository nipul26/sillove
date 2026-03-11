<?php

namespace Sillove\ProductInquiry\Block\Adminhtml\Productinquiry\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Cms\Api\PageRepositoryInterface;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * Generic Constructor
     *
     * @param Context $context
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(
        Context $context,
        PageRepositoryInterface $pageRepository
    ) {
        $this->context = $context;
        $this->pageRepository = $pageRepository;
    }

    /**
     * Getting URL
     *
     * @param string $route
     * @param string $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
