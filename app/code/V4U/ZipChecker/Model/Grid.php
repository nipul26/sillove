<?php
namespace V4U\ZipChecker\Model;

use V4U\ZipChecker\Api\Data\GridInterface;

class Grid extends \Magento\Framework\Model\AbstractModel implements GridInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'v4u_zip_checker';

    /**
     * @var string
     */
    protected $_cacheTag = 'v4u_zip_checker';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'v4u_zip_checker';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('V4U\ZipChecker\Model\ResourceModel\Grid');
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set EntityId.
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get ZipCode.
     *
     * @return varchar
     */
    public function getZipCode()
    {
        return $this->getData(self::ZIPCODE);
    }

    /**
     * Set ZipCode.
     */
    public function setZipCode($zipcode)
    {
        return $this->setData(self::ZIPCODE, $zipcode);
    }

    /**
     * Get IsActive.
     *
     * @return varchar
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set IsActive.
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
}
