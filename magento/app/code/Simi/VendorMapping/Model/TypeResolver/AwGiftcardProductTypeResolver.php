<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Simi\VendorMapping\Model\TypeResolver;

use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as Type;

/**
 * @inheritdoc
 */
class AwGiftcardProductTypeResolver implements TypeResolverInterface
{
    /**
     * Configurable product type resolver code
     */
    const TYPE_RESOLVER = 'AwGiftcardProduct';

    /**
     * @inheritdoc
     */
    public function resolveType(array $data): string
    {
        if (isset($data['type_id']) && $data['type_id'] == Type::TYPE_CODE) {
            return self::TYPE_RESOLVER;
        }
        return '';
    }
}
