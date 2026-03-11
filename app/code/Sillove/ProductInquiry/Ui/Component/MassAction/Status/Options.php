<?php

namespace Sillove\ProductInquiry\Ui\Component\MassAction\Status;

use Magento\Framework\UrlInterface;
use JsonSerializable;

class Options implements JsonSerializable
{
    /**
     * @var Options
     */
    protected $options;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var UrlInterface
     */
    protected $urlPath;
    /**
     * @var $paramName
     */
    protected $paramName;
    /**
     * @var array
     */
    protected $additionalData = [];

    /**
     * Options Constructor
     *
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Json Serialize
     *
     * @return array
     */
    public function jsonSerialize():array
    {
        if ($this->options === null) {
            $options = [
                [
                    "value" => "0",
                    "label" => ('New'),
                ],
                [
                    "value" => "1",
                    "label" => ('Process'),
                ],
                [
                    "value" => "2",
                    "label" => ('Reject'),
                ],
                [
                    "value" => "3",
                    "label" => ('Complete'),
                ],
            ];
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'status_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }
            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare Data
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
