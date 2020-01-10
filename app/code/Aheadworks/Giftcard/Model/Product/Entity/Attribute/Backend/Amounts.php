<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Framework\Locale\FormatInterface;

/**
 * Class Amounts
 *
 * @package Aheadworks\Giftcard\Model\Product\Entity\Attribute\Backend
 */
class Amounts extends AbstractBackend
{
    /**
     * @var FormatInterface
     */
    private $format;

    /**
     * @param FormatInterface $format
     */
    public function __construct(
        FormatInterface $format
    ) {
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $this->validateAmounts($object);
        $this->validateOpenAmount($object);

        return $this;
    }

    /**
     * Validate amounts
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    private function validateAmounts($object)
    {
        $amountsRows = $object->getData($this->getAttribute()->getName()) ? : [];
        $amountsKeys = [];
        $amountsKeysPrice = [];
        foreach ($amountsRows as $data) {
            if (!isset($data['price']) || !isset($data['percent']) || !empty($data['delete'])) {
                continue;
            }
            //validate duplicate price (giftcard value)
            if ($data['website_id'] == 0) {
                foreach ($object->getWebsiteIds() as $websiteId) {
                    $key = implode('-', [$websiteId, (float)$data['price']]);
                    $amountsKeys[$key] = $this->processValidateDuplicate($key, $amountsKeys, 'Value');
                }
            }
            $key = implode('-', [$data['website_id'], (float)$data['price']]);
            $amountsKeys[$key] = $this->processValidateDuplicate($key, $amountsKeys, 'Value');

            //validate duplicate percent (real price)
            if ($data['website_id'] == 0) {
                foreach ($object->getWebsiteIds() as $websiteId) {
                    $key = implode('-', [$websiteId, (float)$data['percent']]);
                    $amountsKeysPrice[$key] = $this->processValidateDuplicate($key, $amountsKeysPrice, 'Price');
                }
            }
            $key = implode('-', [$data['website_id'], (float)$data['percent']]);
            $amountsKeysPrice[$key] = $this->processValidateDuplicate($key, $amountsKeysPrice, 'Price');

            // if (isset($data['percent']) && ($data['percent'] < 0 || $data['percent'] > 100)) {
            //     throw new LocalizedException(__('The percent value is between 0 and 100'));
            // }
        }
        if (count($amountsKeys) == 0 && !$object->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT)) {
            return $this;
            throw new LocalizedException(__('Specify amount options'));
        }

        return $this;
    }

    /**
     * Validate on duplicate amount
     *
     * @param string $key
     * @param [] $amountsKeys
     * @return bool
     * @throws LocalizedException
     */
    private function processValidateDuplicate($key, $amountsKeys, $name = '')
    {
        if (array_key_exists($key, $amountsKeys)) {
            throw new LocalizedException(__('Duplicate amount found '.$name));
        }
        return true;
    }

    /**
     * Validate open amount
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @throws LocalizedException
     */
    private function validateOpenAmount($object)
    {
        if ((bool)$object->getData(ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT)) {
            $minAmount = $this->format->getNumber(
                $object->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN)
            );
            $maxAmount = $this->format->getNumber(
                $object->getData(ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX)
            );

            if ($minAmount > $maxAmount) {
                throw new LocalizedException(
                    __('"Open Amount Max Value" must be greater than "Open Amount Min Value".')
                );
            }
        }

        return $this;
    }
}
