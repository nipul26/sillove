<?php

namespace Sillove\Productlabels\Helper;

use Sillove\Productlabels\Model\ResourceModel\Discount\CollectionFactory as DiscountCollection;
use Sillove\Productlabels\Model\ResourceModel\ProductLabel\CollectionFactory as ProductLabelCollection;
use Magento\Bundle\Api\ProductLinkManagementInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;

class ProductLabeHelper extends AbstractHelper
{
    /**
     * @var ProductLabelCollection
     */
    protected $productlabeldata;

    /**
     * @var DiscountCollection
     */
    protected $discountCollection;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var DateTime
     */
    protected $ruledatetime;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var ProductFactory
     */
    protected $productdata;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $shape_rep = 0;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ProductLinkManagementInterface
     */
    private $productLinkManagement;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductLabelCollection $productlabeldata
     * @param DiscountCollection $discountCollection
     * @param Http $request
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param DateTime $ruledatetime
     * @param RuleFactory $ruleFactory
     * @param Repository $assetRepo
     * @param ProductFactory $productdata
     * @param ProductLinkManagementInterface $productLinkManagement
     * @param Registry $registry
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductLabelCollection $productlabeldata,
        DiscountCollection $discountCollection,
        Http $request,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        DateTime $ruledatetime,
        RuleFactory $ruleFactory,
        Repository $assetRepo,
        ProductFactory $productdata,
        ProductLinkManagementInterface $productLinkManagement,
        Registry $registry,
        TimezoneInterface $timezone
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productlabeldata = $productlabeldata;
        $this->discountCollection = $discountCollection;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->ruledatetime = $ruledatetime;
        $this->ruleFactory = $ruleFactory;
        $this->assetRepo = $assetRepo;
        $this->productdata = $productdata;
        $this->productLinkManagement = $productLinkManagement;
        $this->registry = $registry;
        $this->timezone = $timezone;
    }

    /**
     * Get store scope.
     *
     * @return string
     */
    public function setStoreScope()
    {
        return ScopeInterface::SCOPE_STORE;
    }

    /**
     * Get module status from configuration.
     *
     * @return bool
     */
    public function getModuleStatus()
    {
        $config = 'productlable/general/enable';
        return $this->scopeConfig->getValue($config, $this->setStoreScope());
    }

    /**
     * Get padding value from configuration.
     *
     * @return int
     */
    public function getPaddingValue()
    {
        $config = 'productlable/general/padding_setting';
        return $this->scopeConfig->getValue($config, $this->setStoreScope());
    }

    /**
     * Get display mode from configuration.
     *
     * @return string
     */
    public function getDisplayMode()
    {
        $config = 'productlable/general/display_mode';
        return $this->scopeConfig->getValue($config, $this->setStoreScope());
    }

    /**
     * Get product label data collection.
     *
     * @return ProductLabelCollection
     */
    public function getLabelData()
    {
        $collection = $this->productlabeldata->create();
        $collection->addFieldToFilter('rule_status', ['eq' => 1]);
        $collection->setOrder('rule_priority', 'ASC');
        return $collection;
    }

    /**
     * Get current product.
     *
     * @return ProductInterface|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get current page name.
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->request->getFullActionName();
    }

    /**
     * Determine if the page type matches the given page type.
     *
     * @param string $pagetype
     * @return bool
     */
    public function getPageType($pagetype)
    {
        $page_flage = 0;
        $page_arra = explode(',', $pagetype);
        $page_num = $this->getPageNumber();
        if ($page_num == $pagetype) {
            $page_flage = 1;
            return $page_flage;
        }
        // check store id
        if (in_array($page_num, $page_arra)) {
            $page_flage = 1;
        }
        return $page_flage;
    }

    /**
     * Get the page number based on the current page.
     *
     * @return int
     */
    public function getPageNumber()
    {
        $page_number = 0;
        $page = $this->getPageName();
        if ($page == 'catalog_product_view') {
            $page_number = 1;
        } else {
            $page_number = 0;
        }
        return $page_number;
    }

    /**
     * Determine if the current store matches the given store value.
     *
     * @param int $store_val
     * @return bool
     */
    public function getCurrentStore($store_val)
    {
        $store_flage = 0;
        $store_curr = $this->storeManager->getStore()->getId();
        $store = $this->getStringArray($store_val);
        // for all store
        if (($store_val == 0) || ($store_curr == $store_val)) {
            $store_flage = 1;
            return $store_flage;
        }
        // check store id
        if (in_array($store_curr, $store)) {
            $store_flage = 1;
        }
        return $store_flage;
    }

    /**
     * Get the current customer group
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->httpContext->getValue('customer_group');
    }

    /**
     * Get the current customer group ID.
     *
     * @return int
     */
    public function getGroupIDs()
    {
        $login = $this->httpContext->getValue('customer_logged_in');
        if ($login) {
            $groupid = $this->httpContext->getValue('customer_group');
        } else {
            $groupid = 0;
        }
        return $groupid;
    }

    /**
     * Determine if the current customer group matches the given group value.
     *
     * @param int $group_val
     * @return bool
     */
    public function getCurrentGroup($group_val)
    {
        $curr_group = $this->getGroupIDs();
        $group = $group_val ? explode(',', $group_val) : [];
        // for all group
        if (($group_val == 32000) || ($curr_group == $group_val)) {
            return true;
        }
        // for other group
        if (in_array($curr_group, $group)) {
            return true;
        }
        return false;
    }

    /**
     * Convert comma-separated string to array.
     *
     * @param string $value Comma-separated string
     * @return array
     */
    public function getStringArray($value)
    {
        return array_filter(explode(',', $value));
    }

    /**
     * Check if product data meets the conditions of the rule.
     *
     * @param AbstractCondition $rule
     * @param array $prd_data
     * @return int
     */
    public function getConditionData($rule, $prd_data)
    {
        $flage = 0;
        if ($rule->getConditions()->validate($prd_data)) {
            $flage = 1;
        }
        return $flage;
    }

    /**
     * Retrieve discount data for a given label ID.
     *
     * @param int $id
     * @return DiscountCollection
     */
    public function getDiscountData($id)
    {
        $dicount_data = $this->discountCollection->create();
        $dicount_data->addFieldToFilter('label_id', ['eq' => $id]);
        return $dicount_data;
    }

    /**
     * Calculate and retrieve the discount for a product.
     *
     * @param ProductFactory $prd
     * @param int $group
     * @return float
     */
    public function getDiscount($prd, $group)
    {
        // get prodcut discount
        $discount = 0;
        // get website id
        $storeId = $prd->getStoreId();
        $store = $this->storeManager->getStore($storeId);
        $websiteId = $store->getWebsiteId();
        // get rule date
        $rule_date = $this->ruledatetime->gmtDate();
        $rule = $this->ruleFactory->create();
        // for simple product and Group product  virtual
        if (($prd->getTypeId() == 'simple') ||
            ($prd->getTypeId() == 'downloadable') ||
            ($prd->getTypeId() == 'virtual')) {
            $discount = 0;
            // get product regular price
            $simple_regular_pice = $prd->getPrice();
            //get product final price
            $simple_final_pice = $prd->getFinalPrice();
            // check discount
            if ($simple_regular_pice != $simple_final_pice) {
                $discount = $this->getDiscountPersentage($simple_regular_pice, $simple_final_pice);
                return $discount;
            }
        }
        // for group product
        if ($prd->getTypeId() == 'grouped') {
            // initial discount
            $discount = 0;
            // initial group product price
            $group_regularPrice = $group_specialPrice = 0;
            $usedProds = $prd->getTypeInstance(true)->getAssociatedProducts($prd);
            foreach ($usedProds as $child) {
                if ($child->getId() != $prd->getId()) {
                    $group_regularPrice += $child->getPrice();
                    $group_specialPrice += $child->getFinalPrice();
                }
            }
            if ($group_regularPrice != $group_specialPrice) {
                $discount = $this->getDiscountPersentage($group_regularPrice, $group_specialPrice);
                return $discount;
            }
        } elseif ($prd->getTypeId() == 'configurable') {
            $basePrice = $prd->getPriceInfo()->getPrice('regular_price');
            $regularPrice = $basePrice->getMinRegularAmount()->getValue();
            $specialPrice = $prd->getFinalPrice();
            // initial discount
            $discount = 0;
            $children = $prd->getTypeInstance()->getUsedProducts($prd);
            $discount_arry = [];
            foreach ($children as $child) {
                $prddata = $this->getProductData($child->getEntityId());
                $r_prd = $prddata->getPrice();
                $s_prd = $prddata->getFinalPrice();
                $discount_config = $this->getDiscountPersentage($r_prd, $s_prd);
                if ($discount_config) {
                    $discount_arry[] = $discount_config;
                }
            }
            if (!empty($discount_arry)) {
                $discount = min($discount_arry);
            }
            return $discount;
        } elseif ($prd->getTypeId() == 'bundle') {
            $discount_bundel = [];
            $typeInstance = $prd->getTypeInstance();
            $requiredChildrenIds = $typeInstance->getChildrenIds($prd->getId(), false);
            $i = 0;
            foreach ($requiredChildrenIds as $Childrenkey => $Childrenvalue) {
                foreach ($Childrenvalue as $key => $value) {
                    $child = $this->getProductData($value);
                    $r_bprd = $child->getPrice();
                    $s_bprd = $child->getFinalPrice();
                    $discount_b = $this->getDiscountPersentage($r_bprd, $s_bprd);
                    if ($discount_b) {
                        $discount_bundel[] = $discount_b;
                    }
                }
                $i++;
            }
            if (!empty($discount_bundel)) {
                $discount = min($discount_bundel);
            }
        } else {
            $discount = 0;
        }
        return $discount;
    }

    /**
     * Calculate percentage discount.
     *
     * @param float $list_price
     * @param float $sale_price
     * @return float
     */
   public function getDiscountPersentage($list_price, $sale_price)
    {
        $discount = 0;

        // Convert to float and validate
        $list_price = (float) $list_price;
        $sale_price = (float) $sale_price;

        if ($list_price > 0 && $sale_price >= 0) {
            $discount = ($list_price - $sale_price) / $list_price;
            $discount = $discount * 100;
            $discount = round($discount, 2);
        }

        return $discount;
    }
    /**
     * Get label shape class based on shape number.
     *
     * @param int $shape
     * @return string
     */
    public function getLabelShape($shape)
    {
        $shape_no = $shape + 1;
        $shape = "shape-" . $shape_no;
        return $shape;
    }

    /**
     * Get full media URL for a label image.
     *
     * @param string $image
     * @return string
     */
    public function getLabelImage($image)
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'ProductLabel/';
        $mediaUrl .= $image;
        return $mediaUrl;
    }

    /**
     * Load product data by ID.
     *
     * @param int $id
     * @return ProductFactory
     */
    public function getProductData($id)
    {
        return $this->productdata->create()->load($id);
    }

    /**
     * Get discount color data based on label ID and discount percentage.
     *
     * @param int $id
     * @param float $discount
     * @return array
     */
    public function getDiscountColore($id, $discount)
    {
        $discount_col = [];
        $discount = round($discount);
        // check not discount value
        if ($discount == 0) {
            return $discount_col;
        }
        $discount_color = $this->getDiscountData($id);
        $color_val = [];
        $j = 0;
        for ($x = 0; $x < 10; $x++) {
            $color_values = [];
            $color_values[0] = $j;
            if ($j == 0) {
                $j = $j + 10;
            } else {
                $j = $j + 9;
            }
            $color_values[1] = $j;
            $j++;
            $color_val[$x] = $color_values;
        }
        $i = 1;
        foreach ($discount_color as $colre_key => $colre_value) {
            $disc_nu = $colre_value->getDiscountNumber();
            if (($discount >= $color_val[$disc_nu][0]) && ($discount <= $color_val[$disc_nu][1])) {
                $discount_col = $colre_value->getData();
                break;
            }
            $i++;
        }
        return $discount_col;
    }

    /**
     * Calculate date comparison for label rule.
     *
     * @param string|null $formdate
     * @param string|null $todate
     * @return bool
     */
    public function getDateCalculation($formdate, $todate)
    {
        if (!$formdate && !$todate) {
            return false;
        }
        $date_flage = 0;

        $curr_date = $this->timezone->date()->format('Y-m-d');

        $from_date = date('Y-m-d', strtotime($formdate ?: $curr_date));
        $to_date = date('Y-m-d', strtotime($todate ?: $curr_date));
        //@codingStandardsIgnoreStart
        if ((gettype($todate) == null) && (gettype($formdate) != null)) {

            if ($curr_date >= $from_date) {
                $date_flage = 1;
            }
        } elseif ((gettype($formdate) == null) && (gettype($todate) != null)) {

            if ($curr_date <= $to_date) {
                $date_flage = 1;
            }
        } elseif ((gettype($formdate) != null) && (gettype($todate) != null)) {

            if (($curr_date >= $from_date) && ($curr_date <= $to_date)) {
                $date_flage = 1;
            }
        } else {
            return $date_flage;
        }
        //@codingStandardsIgnoreEnd
        return $date_flage;
    }

    /**
     * Generate HTML for product labels.
     *
     * @param array $data
     * @param float $discount
     * @return string
     */
    public function getProductLabelHtml($data, $discount)
    {
        $label_html = '';

        $position_re = [];
        $position_set = [];
        $this->shape_rep = 0;
        foreach ($data as $data_key => $data_value) {

            if (isset($data_value['label_position'])) {
                if (in_array($data_value['label_position'], $position_re)) {
                    $this->shape_rep = 1;

                    $s_height = $position_set[$data_value['label_position']]['height'];
                    $s_width = $position_set[$data_value['label_position']]['width'];
                    $position_set[$data_value['label_position']]['height'] = $data_value['label_height'] + $s_height;
                    $position_set[$data_value['label_position']]['width'] = $data_value['label_width'] + $s_width;
                } else {
                    $position_re[$data_value['label_position']] = $data_value['label_position'];
                    $this->shape_rep = 0;
                    $position_set[$data_value['label_position']]['height'] = $data_value['label_height'];
                    $position_set[$data_value['label_position']]['width'] = $data_value['label_width'];
                }
            }
            if (($data_value['rule_type'] == 0) || ($data_value['rule_type'] == 2)) {

                $label_html .= $this->getNewProductLabel($data_value, $position_set);
            } elseif ($data_value['rule_type'] == 1) {

                $label_html .= $this->getDiscountLabel($data_value, $discount, $position_set);
            } else {
                $label_html .= '';
            }
        }
        return $label_html;
    }

    /**
     * Generate HTML for new product label.
     *
     * @param array $data
     * @param array $position_re
     * @return string
     */
    public function getNewProductLabel($data, $position_re)
    {
        $html_new = '';

        $position = $data['label_position'];
        $pos_class = $this->getPositionClass($position);
        $html_new = "<span class='product-label-data-" . $pos_class . "' ";
        if ($data['label_type']) {

            $html_new = $this->getCustomLabel($data, $position_re);
        } else {

            $html_new = $this->getImageLabel($data, $position_re);
        }
        return $html_new;
    }

    /**
     * Generate HTML for discount label.
     *
     * @param array $data
     * @param float $discount
     * @param array $position_re
     * @return string
     */
    public function getDiscountLabel($data, $discount, $position_re)
    {
        $html_data = '';

        $position = $data['label_position'];
        $pos_class = $this->getPositionClass($position);
        if (isset($data['discount'])) {

            $discount_data = $this->getDiscountColore($data['label_id'], $discount);
            if (!empty($discount_data)) {

                $shape_number = $this->getLabelShape($data['label_shape']);

                $overlap_shape = $this->getShapeValueOverlap($data, $position_re);

                $html_data .= "<div class='shape-section-" . $pos_class . "' style='" . $overlap_shape . "' >";

                $colore_tras = $data['label_shape'];
                if (($colore_tras == 7) || ($colore_tras == 8)) {
                    $discount_data['discount_back_colore'] = "";
                }

                $html_data .= "<div class='" . $shape_number . "' style='width: "
                                . $data['label_width'] . "px;height: " . $data['label_height']
                                . "px;background:#" . $discount_data['discount_back_colore']
                                . ";--label-color-var: #" . $discount_data['discount_back_colore']
                            . ";'>";

                $html_data .= "<span class='product-label-data' ";

                $font_size = '';
                if (isset($data['label_fontsize'])) {
                    if ($data['label_fontsize'] != 0) {
                        $font_size = "font-size: " . $data['label_fontsize'] . "px;";
                    } else {
                        $font_size = '';
                    }
                }
                $html_data .= " style='color: #" . $discount_data['discount_text_colore'] . ";" . $font_size . "' >";

                $page = $this->getPageName();

                $prd_type = $this->getCurrentProduct();
                if (($page == 'catalog_product_view') && ($prd_type->getTypeId() == 'configurable')) {

                    $html_data .= "<div class='page-discount-wrapper product'><span class='page-discount'>
                                    <span class='base' data-ui-id='page-discount-wrapper' itemprop='discount'>"
                                    . $data['discount'] . "%</span></span></div></span>";

                } else {
                    $html_data .= $data['discount'] . "%</span>";
                }
                $html_data .= "</div></div>";
            }
        }
        return $html_data;
    }

    /**
     * Get CSS class for label position.
     *
     * @param int $position
     * @return string
     */
    public function getPositionClass($position)
    {
        $pos_class = '';
        if ($position == 0) {
            $pos_class = 'top-left';
        } elseif ($position == 1) {
            $pos_class = 'top-center';
        } elseif ($position == 2) {
            $pos_class = 'top-right';
        } elseif ($position == 3) {
            $pos_class = 'center-left';
        } elseif ($position == 4) {
            $pos_class = 'center-right';
        } elseif ($position == 5) {
            $pos_class = 'bottom-left';
        } elseif ($position == 6) {
            $pos_class = 'bottom-center';
        } elseif ($position == 7) {
            $pos_class = 'bottom-right';
        } else {
            $pos_class = '';
        }

        if ($this->shape_rep == 1) {
            $pos_class .= "   overlap-shape";
        }
        return $pos_class;
    }

    /**
     * Generate HTML for custom label.
     *
     * @param array $data
     * @param array $position_re
     * @return string
     */
    public function getCustomLabel($data, $position_re)
    {
        $html_data = '';
        // get label position
        $position = $data['label_position'];
        $pos_class = $this->getPositionClass($position);
        $shape_number = $this->getLabelShape($data['label_shape']);
        //overlap shape
        $overlap_shape = $this->getShapeValueOverlap($data, $position_re);
        // for label font size
        $font_size = '';
        if (isset($data['label_fontsize'])) {
            if ($data['label_fontsize'] != 0) {
                $font_size = "font-size: " . $data['label_fontsize'] . "px;";
            } else {
                $font_size = '';
            }
        }
        // for two shape 7 and 8
        if (($data['label_shape'] == 7) || ($data['label_shape'] == 8)) {
            $shpe_height = $data['label_height'] / 2;

            $html_data .= "<div class='shape-section-" . $pos_class . "' style='" . $overlap_shape . "' >
                            <div class='" . $shape_number . "' style='width: " . $data['label_width']
                            . "px;height: " . $data['label_height'] . "px;background: transparent;--label-color-var: #"
                            . $data['label_back_color'] . ";border-top: " . $shpe_height . "px solid #"
                            . $data['label_back_color'] . ";border-bottom :" . $shpe_height . "px solid #"
                            . $data['label_back_color'] . ";'
                            >";
        } else {
            $html_data .= "<div class='shape-section-" . $pos_class . "' style='" . $overlap_shape . "' >
                            <div class='" . $shape_number . "' style='width: " . $data['label_width']
                            . "px;height: " . $data['label_height'] . "px;background:#"
                            . $data['label_back_color'] . ";--label-color-var: #"
                            . $data['label_back_color'] . ";'
                            >";

        }
        $html_data .= "<span class='product-label-data' ";
        $html_data .= " style='color: #" . $data['label_color'] . ";" . $font_size . "' >" .
            $data['label_text'] . "</span>";
        $html_data .= "</div></div>";
        return $html_data;
    }

    /**
     * Generate HTML for image label.
     *
     * @param array $data
     * @param array $position_re
     * @return string
     */
    public function getImageLabel($data, $position_re)
    {
        $html_data = '';
        // get label position
        $position = $data['label_position'];
        $pos_class = $this->getPositionClass($position);
        //overlap shape
        $overlap_shape = $this->getShapeValueOverlap($data, $position_re);
        $html_data .= "<div class='shape-section-" . $pos_class . "' style='" . $overlap_shape . "'  >";
        // get label image
        $img_path = $this->getLabelImage($data['label_img']);
        //@codingStandardsIgnoreStart
        $html_data .= "<img src='" . $img_path . "' style='width: " . $data['label_width'] . "px;height: " . $data['label_height'] . "px;' >";
        //@codingStandardsIgnoreEnd
        $html_data .= "</div>";
        return $html_data;
    }

    /**
     * Get style for overlapping shapes.
     *
     * @param array $data
     * @param array $position_re
     * @return string
     */
    public function getShapeValueOverlap($data, $position_re)
    {
        $overlap_style = '';
        $padding_val = $this->getPaddingValue();
        if ($this->shape_rep == 1) {
            if (is_array($position_re[$data['label_position']])) {
                $css_style = 0;
                $css_style = $position_re[$data['label_position']]['height'] - $data['label_height'] + $padding_val;
                if ($data['label_position'] == 0) {
                    //Top Left
                    $overlap_style = "top: " . $css_style . "px;";
                } elseif ($data['label_position'] == 1) {
                    //Top Center
                    $overlap_style = "top: " . $css_style . "px;";
                } elseif ($data['label_position'] == 2) {
                    //Top Right
                    $overlap_style = "top: " . $css_style . "px;";
                } elseif ($data['label_position'] == 3) {
                    //Center Left
                    $overlap_style = "left: " . $css_style . "px;";
                } elseif ($data['label_position'] == 4) {
                    //Center Right
                    $overlap_style = "right: " . $css_style . "px;";
                } elseif ($data['label_position'] == 5) {
                    // Bottom Left
                    $overlap_style = "left: " . $css_style . "px;";
                } elseif ($data['label_position'] == 6) {
                    // Bottom Center
                    $css_style = $css_style * 2;
                    $overlap_style = "left: " . $css_style . "px;";
                } elseif ($data['label_position'] == 7) {
                    //Bottom Right
                    $overlap_style = "bottom: " . $css_style . "px;";
                } else {
                    //nothing
                    $overlap_style = '';
                }
            }
        }
        return $overlap_style;
    }
}
