<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Ui\DataProvider\Key\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;

/**
 * Data provider for main panel of product page.
 */
class Advanced implements \Magento\Ui\DataProvider\Modifier\ModifierInterface
{
    const FIELDSET_CODE = 'advanced';

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeWaterMarkField($meta);

        return $meta;
    }

    /**
     * Customize Weight filed.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeWaterMarkField(array $meta)
    {
    }
}
