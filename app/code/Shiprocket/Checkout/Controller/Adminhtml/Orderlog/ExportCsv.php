<?php
/**
 * Shiprocket
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the shiprocket.in license that is
 * available through the world-wide-web at this URL:
 * https://checkout.shiprocket.in/magento-license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Shiprocket
 * @package     Shiprocket_Checkout
 * @copyright   Copyright (c) Shiprocket (https://www.shiprocket.in/)
 * @license     https://checkout.shiprocket.in/magento-license
 */
declare(strict_types=1);
namespace Shiprocket\Checkout\Controller\Adminhtml\Orderlog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Url\DecoderInterface;
use Laminas\Uri\Uri;
use Shiprocket\Checkout\Model\ResourceModel\OrderLog\CollectionFactory;
use Shiprocket\Checkout\Model\Status;

class ExportCsv extends Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var Uri
     */
    protected $zendUri;

    /**
     * @var CollectionFactory
     */
    protected $orderLogCollectionFactory;

    /**
     * @var Status
     */
    protected $orderStatus;

    /**
     * @param Context                   $context
     * @param FileFactory               $fileFactory
     * @param DataPersistorInterface    $dataPersistor
     * @param File                      $file
     * @param DecoderInterface          $urlDecoder
     * @param Uri                       $zendUri
     * @param CollectionFactory         $orderLogCollectionFactory
     * @param Status                    $orderStatus
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        DataPersistorInterface $dataPersistor,
        File $file,
        DecoderInterface $urlDecoder,
        Uri $zendUri,
        CollectionFactory $orderLogCollectionFactory,
        Status $orderStatus
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->dataPersistor = $dataPersistor;
        $this->file = $file;
        $this->urlDecoder = $urlDecoder;
        $this->zendUri = $zendUri;
        $this->orderLogCollectionFactory = $orderLogCollectionFactory;
        $this->orderStatus = $orderStatus;
    }

    /**
     * ExportCsv action
     *
     * @return void
     */
    public function execute()
    {
        $fileName = 'srcheckout_orderlog_export.csv';
        $content = [];

        $filterParams = $this->getRequest()->getParam('orderlog_filter', '');

        $collection = $this->orderLogCollectionFactory->create();
        $orderStatus = $this->orderStatus->toOptionArray();

        $filterFields = [''];
        if ($filterParams) {
            $filters = $this->decodeFilters($filterParams);
            foreach ($filters as $field => $condition) {
                if (in_array($field, ['created_at', 'updated_at']) && is_array($condition)) {
                    if (isset($condition['from'])) {
                        $fromDateTime = date('Y-m-d H:i:s', strtotime($condition['from'] . ' 00:00:00'));
                        $collection->addFieldToFilter($field, ['gteq' => $fromDateTime]);
                    }
                    if (isset($condition['to'])) {
                        $toDateTime = date('Y-m-d H:i:s', strtotime($condition['to'] . ' 23:59:59'));
                        $collection->addFieldToFilter($field, ['lteq' => $toDateTime]);
                    }
                } else {
                    $collection->addFieldToFilter($field, $condition);
                }
            }
        }

        foreach ($collection as $i => $item) {
            $content[$i]['ID'] = $item->getId();
            $content[$i]['Fastrr ID'] = $item->getSrOrderId();
            $content[$i]['Payment Type'] = strtoupper($item->getPaymentType());
            $content[$i]['Order Data'] = $item->getSrData();
            $content[$i]['Magento Order ID'] = $item->getOrderId();
            $content[$i]['Status'] = $orderStatus[$item->getStatus()];
            $content[$i]['Created At'] = $item->getCreatedAt();
            $content[$i]['Updated At'] = $item->getUpdatedAt();
        }

        $csvContent = $this->generateCsv($content);

        return $this->fileFactory->create(
            $fileName,
            $csvContent,
            DirectoryList::VAR_DIR,
            'text/csv',
            null
        );
    }

    /**
     * ExportCsv action
     *
     * @param array $data
     * @return void
     */
    private function generateCsv($data)
    {
        if (empty($data)) {
            return '';
        }
        try {
            $csvData = [array_keys(current($data))];

            foreach ($data as $row) {
                $csvData[] = $row;
            }

            $output = $this->file->fileOpen('php://temp', 'w+');
            foreach ($csvData as $line) {
                $this->file->filePutCsv($output, $line);
            }

            rewind($output);
            // phpcs:disable
            $csvOutput = $this->file->fileGetContents($output);
            // phpcs:enable
            $this->file->fileClose($output);
        } catch (\Exception $e) {
            return false;
        }

        return $csvOutput;
    }

    /**
     * Decode filters url string
     *
     * @param string $filterString
     * @return array $filters
     */
    private function decodeFilters($filterString)
    {
        $decodedString = $this->urlDecoder->decode($filterString);
        $this->zendUri->setQuery($decodedString);
        $filters = $this->zendUri->getQueryAsArray();

        return $filters;
    }
}
