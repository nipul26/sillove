<?php
/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\DeleteOrders\Controller\Adminhtml\Rules;

use Magento\Backend\App\Action;

/**
 * Class NewAction
 *
 * New rule
 */
class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Aitoc_DeleteOrders::rules';

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
