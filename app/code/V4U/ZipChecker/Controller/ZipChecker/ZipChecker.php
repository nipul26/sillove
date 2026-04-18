<?php

namespace V4U\ZipChecker\Controller\ZipChecker;

use V4U\ZipChecker\Helper\Data as DataHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use V4U\ZipChecker\Model\ShiprocketApi;

class ZipChecker extends Action
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ShiprocketApi
     */
    private $shiprocketApi;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * ZipChecker constructor.
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        DataHelper $dataHelper,
        ShiprocketApi $shiprocketApi,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->dataHelper = $dataHelper;
        $this->shiprocketApi = $shiprocketApi;
        $this->resultJsonFactory = $resultJsonFactory;
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
            $zipcode = trim((string) $zipcode);

            $productId = (int) $this->getRequest()->getParam('id');
            $product = null;

            if ($productId) {
                $product = $this->productFactory->create()->load($productId);

                if (!$product->getId()) {
                    throw new \Exception("Product not found");
                }
            }

            $weightKg = $this->resolveShiprocketWeightKg($product);
            $serviceabilityData = $this->shiprocketApi->getServiceabilityData($zipcode, $weightKg);
            if ((bool) ($serviceabilityData['is_serviceable'] ?? false)) {
                $response['type'] = 'success';
                $response['message'] = __(
                    $this->dataHelper->getSuccessMessage(),
                    $zipcode
                );
                $response['estimated_delivery_days'] = $serviceabilityData['estimated_delivery_days'] ?? null;
                $response['etd'] = $serviceabilityData['etd'] ?? null;

                if (!empty($response['estimated_delivery_days']) || !empty($response['etd'])) {
                    $deliveryInfo = [];
                    if (!empty($response['estimated_delivery_days'])) {
                        $deliveryInfo[] = __('in %1 day(s)', $response['estimated_delivery_days']);
                    }
                    if (!empty($response['etd'])) {
                        $deliveryInfo[] = __('by %1', $response['etd']);
                    }

                    if (!empty($deliveryInfo)) {
                        $response['message'] .= '<br/>' . __('Expected delivery %1.', implode(' ', $deliveryInfo));
                    }
                }
            } else {
                $response['type'] = 'error';
                $response['message'] = __(
                    $this->dataHelper->getErrorMessage(),
                    $zipcode
                );
                $response['estimated_delivery_days'] = null;
                $response['etd'] = null;
            }

        } catch (LocalizedException $e) {
            $response['type'] = 'error';
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $response['type'] = 'error';
            $response['message'] = $e->getMessage();
        }

        return $this->resultJsonFactory->create()->setData($response);
    }

    /**
     * Prefer catalog product weight (kg); for configurable, use first child with weight if parent has none.
     * Falls back to admin "Weight (kg)" when product weight is missing or zero.
     *
     * @param Product|null $product
     * @return float|null Positive weight for API, or null to let ShiprocketApi use static config only.
     */
    private function resolveShiprocketWeightKg(?Product $product): ?float
    {
        if ($product === null || !$product->getId()) {
            return null;
        }

        $weight = (float) $product->getWeight();
        if ($weight > 0) {
            return $weight;
        }

        if ($product->getTypeId() === 'configurable') {
            foreach ($product->getTypeInstance()->getUsedProducts($product) as $child) {
                $childWeight = (float) $child->getWeight();
                if ($childWeight > 0) {
                    return $childWeight;
                }
            }
        }

        return null;
    }
}