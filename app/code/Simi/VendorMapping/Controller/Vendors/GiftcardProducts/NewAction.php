<?php
/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Simi\VendorMapping\Controller\Vendors\GiftcardProducts;

use Aheadworks\Giftcard\Model\Product\Type\Giftcard as TypeGiftcard;
use Magento\Catalog\Model\ProductFactory;
use Vnecoms\Vendors\App\Action\Context;

/**
 * Class NewAction
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Product
 */
class NewAction extends \Vnecoms\Vendors\Controller\Vendors\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    protected $_aclResource = 'Simi_VendorMapping::giftcard_products';

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @param Context $context
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
    }

    /**
     * Create new action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_getSession()->setBackToAwGiftcardGridFlag(true);
        $this->_getSession()->setResetBackToAwGiftcardGridFlag(false);
        return $this->_redirect(
            'catalog/product/new',
            [
                'set' => $this->productFactory->create()->getDefaultAttributeSetId(),
                'type' => TypeGiftcard::TYPE_CODE
            ]
        );
    }
}
