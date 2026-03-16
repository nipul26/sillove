<?php

namespace V4U\ZipChecker\Api\Data;

interface GridInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ENTITY_ID = 'entity_id';
    const ZIPCODE   = 'zipcode';
    const IS_ACTIVE = 'is_active';

   /**
    * Get EntityId.
    *
    * @return int
    */
    public function getEntityId();

   /**
    * Set EntityId.
    */
    public function setEntityId($entityId);

   /**
    * Get ZipCode.
    *
    * @return varchar
    */
    public function getZipCode();

   /**
    * Set ZipCode.
    */
    public function setZipCode($zipcode);

   /**
    * Get IsActive.
    *
    * @return varchar
    */
    public function getIsActive();

   /**
    * Set StartingPrice.
    */
    public function setIsActive($isActive);
}
