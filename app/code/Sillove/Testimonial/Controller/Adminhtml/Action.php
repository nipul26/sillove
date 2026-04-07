<?php
// @codingStandardsIgnoreFile
namespace Sillove\Testimonial\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Sillove\Testimonial\Model\TestimonialFactory;
use Sillove\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Helper\Js;

abstract class Action extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

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
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * File Factory.
     *
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
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->testimonialFactory = $testimonialFactory;
        $this->testimonialCollectionFactory = $testimonialCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        $namespace = (new \ReflectionObject($this))->getNamespaceName();
        $string = strtolower(str_replace(__NAMESPACE__ . '\\', '', $namespace));
        $action =  explode('\\', $string);
        $action =  array_shift($action);
        return $this->_authorization->isAllowed("Sillove_Testimonial::testimonial_$action");
    }
}
