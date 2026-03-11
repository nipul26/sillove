<?php

namespace Sillove\ProductInquiry\Model;

use Magento\Framework\Model\AbstractModel;
use Sillove\ProductInquiry\Api\Data\InquiryInterface;
use Sillove\ProductInquiry\Model\ResourceModel\Productinquiry as ProductinquiryResourceModel;

class Productinquiry extends AbstractModel implements InquiryInterface
{
    public const CACHE_TAG = 'sillove_productinquiry_admingrid';

    /**
     * @var string
     */
    protected $_cacheTag = 'sillove_productinquiry_admingrid';
    /**
     * @var string
     */
    protected $_eventPrefix = 'sillove_productinquiry_admingrid';

    /**
     * Product Inquiry Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ProductinquiryResourceModel::class);
    }

    /**
     * Get Id
     *
     * @return array|mixed|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id
     *
     * @param int $Id
     * @return Productinquiry
     */
    public function setId($Id)
    {
        return $this->setData(self::ID, $Id);
    }

    /**
     * Get Product Entity Id
     *
     * @return array|mixed|null
     */
    public function getPrdEntityId()
    {
        return $this->getData(self::PRD_ENTITY_ID);
    }

    /**
     * Set Product Entity Id
     *
     * @param int $prd_entity_id
     * @return Productinquiry
     */
    public function setPrdEntityId($prd_entity_id)
    {
        return $this->setData(self::PRD_ENTITY_ID, $prd_entity_id);
    }

    /**
     * Get Product SKU
     *
     * @return array|mixed|null
     */
    public function getPrdSku()
    {
        return $this->getData(self::PRD_SKU);
    }

    /**
     * Set Product SKU
     *
     * @param string $prd_sku
     * @return Productinquiry
     */
    public function setPrdSku($prd_sku)
    {
        return $this->setData(self::PRD_SKU, $prd_sku);
    }

    /**
     * Get Username
     *
     * @return array|mixed|null
     */
    public function getUsrName()
    {
        return $this->getData(self::USR_NAME);
    }

    /**
     * Set Username
     *
     * @param string $usr_name
     * @return Productinquiry
     */
    public function setUsrName($usr_name)
    {
        return $this->setData(self::USR_NAME, $usr_name);
    }

    /**
     * Get Email
     *
     * @return array|mixed|null
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set Email
     *
     * @param string $email
     * @return Productinquiry
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get Subject
     *
     * @return array|mixed|null
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * Set Subject
     *
     * @param array $subject
     * @return Productinquiry
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * Get Inquiry Message
     *
     * @return array|mixed|null
     */
    public function getInqMsg()
    {
        return $this->getData(self::INQ_MSG);
    }

    /**
     * Set Inquiry Message
     *
     * @param string $inq_msg
     * @return Productinquiry
     */
    public function setInqMsg($inq_msg)
    {
        return $this->setData(self::INQ_MSG, $inq_msg);
    }

    /**
     * Get Publish DateTime
     *
     * @return array|mixed|null
     */
    public function getPublishDatetime()
    {
        return $this->getData(self::PUBLISH_DATETIME);
    }

    /**
     * Set Publish DateTime
     *
     * @param string $publish_datetime
     * @return Productinquiry
     */
    public function setPublishDatetime($publish_datetime)
    {
        return $this->setData(self::PUBLISH_DATETIME, $publish_datetime);
    }

    /**
     * Get Update DateTime
     *
     * @return array|mixed|null
     */
    public function getUpadteDatetime()
    {
        return $this->getData(self::UPDATE_DATETIME);
    }

    /**
     * Set Update DateTime
     *
     * @param string $update_datetime
     * @return Productinquiry
     */
    public function setUpadteDatetime($update_datetime)
    {
        return $this->setData(self::UPDATE_DATETIME, $update_datetime);
    }
}
