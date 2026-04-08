<?php
namespace Sparsh\Testimonials\Ui\Component\Listing\Column\TestimonialListing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class PageActions extends Column
{
    /**
     * Url path
     */
    public const TESTIMONIAL_URL_PATH_EDIT = 'testimonial/manage/edit';
    public const TESTIMONIAL_URL_PATH_DELETE = 'testimonial/manage/delete';

    /**
     * UrlInterface Variable
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Editurl Variable
     *
     * @var string
     */
    protected $editUrl;

    /**
     * Initialize
     *
     * @param ContextInterface   $context            Initialize ContextInterface
     * @param UiComponentFactory $uiComponentFactory Initialize UiComponentFactory
     * @param UrlInterface       $urlBuilder         Initialize UrlInterface
     * @param array              $components         Initialize components
     * @param array              $data               Initialize data
     * @param string             $editUrl            Initialize editurl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::TESTIMONIAL_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
    /**
     * PrepareDataSource
     *
     * @param  mixed $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData('name');
                if (isset($item['testimonial_id'])) {
                    $item[$name]['view'] = [
                        'href' => $this->urlBuilder
                            ->getUrl($this->editUrl, ['testimonial_id' => $item['testimonial_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder
                            ->getUrl(
                                self::TESTIMONIAL_URL_PATH_DELETE,
                                ['testimonial_id' => $item['testimonial_id']]
                            ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete testimonial'),
                            'message' => __('Are you sure you wan\'t to delete this record?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
