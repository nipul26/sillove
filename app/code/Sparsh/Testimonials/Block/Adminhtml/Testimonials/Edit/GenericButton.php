<?php
namespace Sparsh\Testimonials\Block\Adminhtml\Testimonials\Edit;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * GenericButton constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->context = $context;
    }
   
    /**
     * GetBackUrl
     *
     * @return void
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/testimonials/');
    }
  
    /**
     * GetDeleteUrl
     *
     * @return void
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['object_id' => $this->getObjectId()]);
    }
 
    /**
     * GetUrl
     *
     * @param  mixed $route
     * @param  mixed $params
     * @return void
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
  
    /**
     * GetObjectId
     *
     * @return void
     */
    public function getObjectId()
    {
        return $this->context->getRequest()->getParam('post_id');
    }
}
