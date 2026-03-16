<?php

namespace Sillove\Testimonial\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Sillove\Testimonial\Model\TestimonialFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Http\Context as HttpContext;
use Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory;

class Save extends Action
{
    /**
     * @var testimonialFactory
     */
    protected $testimonialFactory;

    /**
     * @var messageManager
     */
    protected $messageManager;

    /**
     * @var resultRedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var storeManager
     */
    protected $storeManager;

    /**
     * @var transportBuilder
     */
    protected $transportBuilder;

    /**
     * @var scopeConfig
     */
    protected $scopeConfig;

    /**
     * @var httpContext
     */
    protected $httpContext;

    /**
     * @var collectionFactory
     */
    protected $collectionFactory;

    /**
     * Summary of __construct
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Sillove\Testimonial\Model\TestimonialFactory $testimonialFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        HttpContext $httpContext,
        TestimonialFactory $testimonialFactory,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->httpContext = $httpContext;
        $this->testimonialFactory = $testimonialFactory;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Summary of execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $customer_id =$this ->getCustomerId();

        if (!$data || empty($data['name']) || empty($data['text']) || empty($data['rating_summary'])) {
            $this->messageManager->addErrorMessage(__('Invalid data.'));
            return $this->resultRedirectFactory->create()->setPath('testimonial/index/index');
        }

        if ($customer_id) {
            $existingTestimonial = $this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $customer_id)
                ->getFirstItem();
            if ($existingTestimonial->getId()) {
                $this->messageManager->addErrorMessage(__('You have already submitted a testimonial.'));
                return $this->resultRedirectFactory->create()->setPath('testimonial/index/index');
            }
        }

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $testimonial = $this->testimonialFactory->create();

            $testimonial->setData([
                'name'            => $data['name'],
                'text'            => $data['text'],
                'rating_summary'  => $data['rating_summary'],
                'stores'          => $storeId,
                'order'           => $data['order'] ?? 0,
                'status'          => "2",
                'customer_id'    => $customer_id

            ]);
            $testimonial->save();

            // Send email notification
            $this->sendEmailToAdmin($data, $storeId);

            $this->messageManager->addSuccessMessage(__('Testimonial submitted successfully and email sent.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: ') . $e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('testimonial/index/index');
    }

    /**
     * Summary of sendEmailToAdmin
     *
     * @param array $data
     * @param int $storeId
     * @throws \Exception
     * @return void
     */
    protected function sendEmailToAdmin(array $data, int $storeId)
    {
        try {
            $adminEmails = $this->scopeConfig->getValue(
                'testimonial/general/admin_email_list',
                ScopeInterface::SCOPE_STORE
            );
            $adminNames = $this->scopeConfig->getValue(
                'testimonial/general/admin_name_list',
                ScopeInterface::SCOPE_STORE
            );
            
            if (!$adminEmails) {
                throw new LocalizedException(__('Admin email list is not configured.'));
            }

            $emailList = array_map('trim', explode(',', $adminEmails));
            $nameList = $adminNames ? array_map('trim', explode(',', $adminNames)) : [];

            $storeName = $this->storeManager->getStore($storeId)->getName();

            foreach ($emailList as $index => $email) {
                $adminName = $nameList[$index] ?? 'Admin'; // Fallback to 'Admin' if name is missing
                
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('testimonial_admin_email')
                    ->setTemplateOptions([
                        'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ])
                    ->setTemplateVars([
                        'name'           => $data['name'],
                        'text'           => $data['text'],
                        'rating_summary' => $data['rating_summary'],
                        'store_name'     => $storeName,
                        'admin_name'     => $adminName
                    ])
                    ->setFromByScope('general')
                    ->addTo($email, $adminName)
                    ->getTransport();
                
                $transport->sendMessage();
            }

        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Email Error: ') . $e->getMessage());
        }
    }
    
    /**
     * Summary of getIsCustomerLoggedIn
     *
     * @return bool
     */
    protected function getIsCustomerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Summary of getCustomerId
     *
     * @return mixed|null
     */
    protected function getCustomerId()
    {
        return $this->httpContext->getValue('customer_id');
    }
}
