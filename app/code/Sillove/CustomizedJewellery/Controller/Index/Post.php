<?php
namespace Sillove\CustomizedJewellery\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Sillove\CustomizedJewellery\Model\JewelleryRequestFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Sillove\CustomizedJewellery\Helper\Data as Helper;

class Post extends Action
{
    /**
     * @var JewelleryRequestFactory
     */
    protected $requestFactory;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Context $context
     * @param JewelleryRequestFactory $requestFactory
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        JewelleryRequestFactory $requestFactory,
        Helper $helper
    ) {
        $this->requestFactory = $requestFactory;
        $this->messageManager = $context->getMessageManager();
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->helper->isEnabled()) {
            $this->messageManager->addErrorMessage(__('Customized jewellery requests are currently disabled.'));
            return $resultRedirect->setPath('/');
        }

        if ($data) {
            try {
                $model = $this->requestFactory->create();
                $model->setData($data);
                $model->save();

                $this->helper->sendNotificationEmail($data);

                $this->messageManager->addSuccessMessage(__('Thank you for your request. We will contact you soon.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving your request.'));
            }
        }
        return $resultRedirect->setPath('customizedjewellery/index/index');
    }
}
