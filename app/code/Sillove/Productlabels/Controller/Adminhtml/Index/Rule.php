<?php

namespace Sillove\Productlabels\Controller\Adminhtml\Index;

use Sillove\Productlabels\Model\ProductLabelFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Psr\Log\LoggerInterface;

abstract class Rule extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Date
     */
    protected $dateFilter;

    /**
     * @var ProductLabelFactory
     */
    protected $ruleFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param ProductLabelFactory $ruleFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        ProductLabelFactory $ruleFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->fileFactory = $fileFactory;
        $this->dateFilter = $dateFilter;
        $this->ruleFactory = $ruleFactory;
        $this->logger = $logger;
    }

    /**
     * Initiate rule
     *
     * @return void
     */
    protected function _initRule()
    {
        $rule = $this->ruleFactory->create();
        $this->coreRegistry->register(
            'current_rule',
            $rule
        );
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('label_id')) {
            $id = (int) $this->getRequest()->getParam('label_id');
        }

        if ($id) {
            $this->coreRegistry->registry('current_rule')->load($id);
        }
    }

    /**
     * Initiate action
     *
     * @return Rule
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        return $this;
    }

    /**
     * Check if action is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sillove_Productlabels::menu');
    }
}
