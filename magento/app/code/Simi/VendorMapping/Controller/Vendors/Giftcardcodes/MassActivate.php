<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\Giftcardcodes;

use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassActivate
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Giftcard
 */
class MassActivate extends MassAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($collection)
    {
        $count = 0;
        foreach ($collection->getItems() as $item) {
            try {
                $giftcardCode = $this->giftcardRepository->get($item->getId());
                $giftcardCode->setState(Status::ACTIVE);
                $this->giftcardRepository->save($giftcardCode);
                $count++;
            } catch (LocalizedException $e) {
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 Gift Card code(s) have been activated', $count));
    }
}
