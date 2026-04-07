<?php

namespace V4U\ZipChecker\Controller\ZipChecker;

use V4U\ZipChecker\Helper\Data as DataHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use V4U\ZipChecker\Model\GridFactory;

class ZipChecker extends Action
{
    /**
     * @var GridFactory
     */
    protected $modelGridFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * ZipChecker constructor.
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        DataHelper $dataHelper,
        GridFactory $modelGridFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->dataHelper = $dataHelper;
        $this->modelGridFactory = $modelGridFactory;
    }

    public function execute()
    {
        $response = [];

        try {

            if (!$this->getRequest()->isAjax()) {
                throw new \Exception("Invalid Request. Try again.");
            }

            if (!$zipcode = $this->getRequest()->getParam('zipcode')) {
                throw new \Exception("Please enter zipcode");
            }

            $productId = (int) $this->getRequest()->getParam('id');
            $zipcodesProd = [];

            if ($productId) {
                $product = $this->productFactory->create()->load($productId);

                if (!$product->getId()) {
                    throw new \Exception("Product not found");
                }

                /** Product specific zipcodes */
                $zipcodesProdRaw = trim($product->getCheckDeliveryPostcodes() ?? '');

                $zipcodesProd = $zipcodesProdRaw
                    ? array_map('trim', explode(',', $zipcodesProdRaw))
                    : [];
            }

            /** Get active zipcodes from custom table */
            $resultPage = $this->modelGridFactory->create();
            $collection = $resultPage->getCollection();
            $collection->addFieldToSelect('zipcode')
                ->addFieldToFilter('is_active', ['eq' => 1]);

            $zipcodes = [];

            foreach ($collection as $zipCodes) {
                $zipcodes[] = $zipCodes->getZipcode();
            }

            /** Merge both zipcodes */
            $zipCodeFinal = array_merge($zipcodesProd, $zipcodes);

            if (in_array($zipcode, $zipCodeFinal)) {
                $response['type'] = 'success';
                $response['message'] = __(
                    $this->dataHelper->getSuccessMessage(),
                    $zipcode
                );
            } else {
                $response['type'] = 'error';
                $response['message'] = __(
                    $this->dataHelper->getErrorMessage(),
                    $zipcode
                );
            }

        } catch (\Exception $e) {
            $response['type'] = 'error';
            $response['message'] = $e->getMessage();
        }

        return $this->getResponse()->setContent(json_encode($response));
    }
}