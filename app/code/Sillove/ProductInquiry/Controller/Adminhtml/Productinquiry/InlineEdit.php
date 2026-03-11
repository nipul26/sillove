<?php

namespace Sillove\ProductInquiry\Controller\Adminhtml\Productinquiry;

use Magento\Backend\App\Action\Context;
use Sillove\ProductInquiry\Model\ProductinquiryFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;

class InlineEdit extends Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var ProductinquiryFactory
     */
    protected $_productinquiryFactory;

    /**
     * Inline Edit Constructor
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ProductinquiryFactory $productinquiryFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ProductinquiryFactory $productinquiryFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->_productinquiryFactory = $productinquiryFactory;
    }

    /**
     * Execute Method
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelId) {
                    $model = $this->_productinquiryFactory->create()->load($modelId);
                    try {
                        //@codingStandardsIgnoreStart
                        $model->setData(array_merge($model->getData(), $postItems[$modelId]));
                        //@codingStandardsIgnoreEnd
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[ID: {$modelId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }
}
