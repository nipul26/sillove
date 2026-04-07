<?php
namespace Sillove\Testimonial\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Helper\Js;
use Sillove\Testimonial\Model\TestimonialFactory;
use Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory;

abstract class Testimonial extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var TestimonialFactory
     */
    protected $testimonialFactory;

    /**
     * @var CollectionFactory
     */
    protected $testimonialCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * __construct
     *
     * @param Context            $context
     * @param TestimonialFactory $testimonialFactory
     * @param CollectionFactory  $testimonialCollectionFactory
     * @param Registry           $coreRegistry
     * @param FileFactory        $fileFactory
     * @param PageFactory        $resultPageFactory
     * @param LayoutFactory      $resultLayoutFactory
     * @param ForwardFactory     $resultForwardFactory
     * @param Js                 $jsHelper
     */
    public function __construct(//NOSONAR
        Context $context,
        TestimonialFactory $testimonialFactory,
        CollectionFactory $testimonialCollectionFactory,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory,
        ForwardFactory $resultForwardFactory,
        Js $jsHelper
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->fileFactory = $fileFactory;
        $this->jsHelper = $jsHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->testimonialFactory = $testimonialFactory;
        $this->testimonialCollectionFactory = $testimonialCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return true;
    }
}
