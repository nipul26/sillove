<?php
/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\DeleteOrders\Api;

use Aitoc\DeleteOrders\Api\Data\ArchiveInterface;

interface ArchiveRepositoryInterface
{
    /**
     * Save
     *
     * @param \Aitoc\DeleteOrders\Api\Data\ArchiveInterface $archiveModel
     * @return \Aitoc\DeleteOrders\Api\Data\ArchiveInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ArchiveInterface $archiveModel);

    /**
     * Get by entity id
     *
     * @param int $entityId
     * @return \Aitoc\DeleteOrders\Api\Data\ArchiveInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($entityId);

    /**
     * Delete
     *
     * @param \Aitoc\DeleteOrders\Api\Data\ArchiveInterface $archiveModel
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ArchiveInterface $archiveModel);

    /**
     * Delete by id
     *
     * @param int $entityId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);
}
