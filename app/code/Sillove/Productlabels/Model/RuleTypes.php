<?php

namespace Sillove\Productlabels\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Sillove\Productlabels\Model\ResourceModel\ProductLabel\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Sillove\Productlabels\Model\ProductLabelFactory;

class RuleTypes implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $productlabeldata;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ProductLabelFactory
     */
    protected $productLabelmodel;

    /**
     * Constructor
     *
     * @param CollectionFactory $productlabeldata
     * @param Http $request
     * @param ProductLabelFactory $productLabelmodel
     */
    public function __construct(
        CollectionFactory $productlabeldata,
        Http $request,
        ProductLabelFactory $productLabelmodel
    ) {
        $this->productlabeldata = $productlabeldata;
        $this->request = $request;
        $this->productLabelmodel = $productLabelmodel;
    }

    /**
     * Get option array for the dropdown.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $collection = $this->productlabeldata->create();
        $collection->addFieldToFilter('rule_type', ['eq' => 0]);
        if (!count($collection->getData())) {
             $options['0'] = __('New');
        }
        $collection = $this->productlabeldata->create();
        $collection->addFieldToFilter('rule_type', ['eq' => 1]);
        if (!count($collection->getData())) {
            $options['1'] = __('Discount');
        }

        $id = $this->request->getParam('label_id');
        if ($id) {
            $model = $this->productLabelmodel->create();
            $model->load($id);

            if ($model->getRuleType() == 0) {
                $options['0'] = __('New');
            }

            if ($model->getRuleType() == 1) {
                $options['1'] = __('Discount');
            }
        }
        $options['2'] = __('Catalog Rule-Based');

        return $options;
    }

    /**
     * Get all options for the dropdown
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);

        return $res;
    }

    /**
     * Get options for the dropdown.
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }

        return $res;
    }

    /**
     * Convert options array to option array format.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
