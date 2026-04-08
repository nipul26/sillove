<?php
namespace Sparsh\Testimonials\Controller\AddNew;

use \Magento\Framework\App\Request\DataPersistorInterface;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Sparsh\Testimonials\Model\Data
     */
    protected $testimonialLoader;

    /**
     * Add constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Sparsh\Testimonials\Model\Data $testimonialLoader
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Sparsh\Testimonials\Model\Data $testimonialLoader,
        DataPersistorInterface $dataPersistor
    ) {
        $this->pageFactory = $pageFactory;
        $this->dataPersistor = $dataPersistor;
        $this->dateTime = $dateTime;
        $this->testimonialLoader = $testimonialLoader;
        return parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $dataPersistors = $this->dataPersistor;
        $name = $this->getRequest()->getParam("name");
        $rating = (int)$this->getRequest()->getParam("rating");
        $message = $this->getRequest()->getParam("message");
        $datetime = $this->dateTime;
        $date = $datetime->gmtDate('Y-m-d');
        $item = $this->testimonialLoader;
        try {
            $item->setName($name);
            $item->setRating($rating);
            $item->setMessage($message);
            $item->setTestimonial_date($date);
            $item->setStatus('Disabled');
            $item->setShowOnHome('No');
            $item->save();
            $this->messageManager->addSuccessMessage(
                __('Thanks for your valuable time.')
            );
            $dataPersistors->clear('testimonials');
            return $this->resultRedirectFactory->create()->setPath('testimonials');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your data. Please try again.')
            );
            $dataPersistors->set('testimonial', $this->getRequest()->getParams());
            return $this->resultRedirectFactory->create()->setPath('testimonial/addnew');
        }
    }
}
