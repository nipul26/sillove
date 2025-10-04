<?php

namespace Sillove\Productlabels\Controller\Adminhtml\Index;

use Sillove\Productlabels\Model\DiscountFactory;
use Sillove\Productlabels\Model\ImageUploader;
use Sillove\Productlabels\Model\ProductLabelFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Save extends Action
{
    /**
     * @var ProductLabelFactory
     */
    protected $productLabelmodel;

    /**
     * @var DiscountFactory
     */
    protected $discountmodel;

    /**
     * @var Session
     */
    protected $adminsession;

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param ProductLabelFactory $productLabelmodel
     * @param DiscountFactory $discountmodel
     * @param Session $adminsession
     * @param ImageUploader $imageUploader
     * @param DataPersistorInterface $dataPersistor
     * @param SerializerInterface $serializer
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     */
    public function __construct(
        Context $context,
        ProductLabelFactory $productLabelmodel,
        DiscountFactory $discountmodel,
        Session $adminsession,
        ImageUploader $imageUploader,
        DataPersistorInterface $dataPersistor,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        ResponseInterface $response
    ) {
        $this->productLabelmodel = $productLabelmodel;
        $this->discountmodel = $discountmodel;
        $this->adminsession = $adminsession;
        $this->imageUploader = $imageUploader;
        $this->dataPersistor = $dataPersistor;
        $this->serializer = $serializer;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        parent::__construct($context);
    }

    /**
     * Execute the controller action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->dataPersistor->set('label_rule_data', $data);
        if (!empty($data['display_in'])) {
            $data['display_in'] = implode(",", $data['display_in']);
        }

        if (!empty($data['customer_group'])) {
            $data['customer_group'] = implode(",", $data['customer_group']);
        }

        if (!empty($data['storeviews'])) {
            $data['storeviews'] = implode(",", $data['storeviews']);
        }

        if (isset($data['label_id'])) {
            $imagename = $this->uploadLabelImage($data, $data['label_id']);
        } else {

            $id = 0;
            $imagename = $this->uploadLabelImage($data, $id);
        }
        $data['label_img'] = $imagename;

        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }

        try {
            $model = $this->productLabelmodel->create();
            $id = $this->getRequest()->getParam('label_id');
            if ($id) {
                $model->load($id);
            }
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This tab no longer exists.'));
                return $resultRedirect->setPath('productlable/index/index');
            }
            $model->loadPost($data);
            $data = $this->prepareData($data);
            $model->save();
            if (isset($data['discount_row'])) {
                $this->setDiscount($model);
            } else {
                $this->removeDiscount($model);
            }
            $this->dataPersistor->clear('label_rule_data');
            $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
            if ($this->getRequest()->getParam('back')) {
                if ($this->getRequest()->getParam('back') == 'add') {
                    return $resultRedirect->setPath('productlable/index/add');
                } else {
                    return $resultRedirect->setPath(
                        'productlable/index/add',
                        [
                            'label_id' => $model->getId(),
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            return $resultRedirect->setPath('productlable/index/add');
        }
        return $resultRedirect->setPath('productlable/index/index');
    }

    /**
     * Upload label image and retrieve image name.
     *
     * @param array $data
     * @param int $id
     * @return string
     */
    public function uploadLabelImage($data, $id)
    {
        $image_name = '';
        if ($id) {

            $prd_label_data = $this->productLabelmodel->create();
            $prd_label_data->load($id);
            $image_name = $prd_label_data->getLabelImg();
            if (isset($data['label_img'][0]['name'])) {
                if ($image_name != $data['label_img'][0]['name']) {
                    $image_name = $data['label_img'][0]['name'];
                    $image_name = $this->imageUploader->moveFileFromTmp($image_name);
                } else {
                    $image_name = $prd_label_data->getLabelImg();
                }
            } else {
                $image_name = '';
            }
        } else {

            if (!empty($data['label_img'])) {
                $image_name = $data['label_img'][0]['name'];
                $image_name = $this->imageUploader->moveFileFromTmp($image_name);
            } else {
                $image_name = '';
            }
        }
        return $image_name;
    }

    /**
     * Prepare data for saving.
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        unset($data['rule']);
        return $data;
    }

    /**
     * Get label discount collection by label ID.
     *
     * @param int $id
     * @return array
     */
    public function getLabelDiscountCollection($id)
    {
        return $this->discountmodel->create()->getCollection()->addFieldToFilter('label_id', $id);
    }

    /**
     * Set discount for the label.
     *
     * @param ProductLabelFactory $model
     * @return void
     */
    public function setDiscount($model)
    {
        $discountCollection = $this->getLabelDiscountCollection($model->getId());
        $discount = [];
        foreach ($model->getDiscountRow() as $value) {
            $value['label_id'] = $model->getId();
            $discount_data = $this->discountmodel->create();
            $discount_data->setData($value);
            $discount_data->save();
            $discount[] = $discount_data->getDiscountId();
        }
        foreach ($discountCollection as $discountVal) {
            if (!in_array($discountVal->getDiscountId(), $discount)) {
                $this->discountmodel->create()->load($discountVal->getDiscountId())->delete();
            }
        }
    }

    /**
     * Remove discounts associated with the label.
     *
     * @param ProductLabelFactory $model
     * @return void
     */
    public function removeDiscount($model)
    {
        $discountCollection = $this->getLabelDiscountCollection($model->getId());
        foreach ($discountCollection as $discountVal) {
            $this->discountmodel->create()->load($discountVal->getDiscountId())->delete();
        }
    }
}
