<?php
/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\DeleteOrders\Api;

use Aitoc\DeleteOrders\Api\Data\RulesInterface;

interface RulesRepositoryInterface
{
    /**
     * Save
     *
     * @param \Aitoc\DeleteOrders\Api\Data\RulesInterface $rulesModel
     * @return \Aitoc\DeleteOrders\Api\Data\RulesInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(RulesInterface $rulesModel);

    /**
     * Get by entity id
     *
     * @param int $entityId
     * @return \Aitoc\DeleteOrders\Api\Data\RulesInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($entityId);

    /**
     * Delete
     *
     * @param \Aitoc\DeleteOrders\Api\Data\RulesInterface $rulesModel
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(RulesInterface $rulesModel);

    /**
     * Delete by id
     *
     * @param int $entityId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);
}
