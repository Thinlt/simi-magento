<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Giftcardcodes;

/**
 * Class MassDelete
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class MassDelete extends MassAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $count = 0;
        foreach ($collection->getItems() as $item) {
            $this->giftcardRepository->deleteById($item->getId());
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 gift card(s) have been deleted.', $count));
    }
}
