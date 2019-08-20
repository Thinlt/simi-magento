<?php
/**
 * Encrypted config field backend model
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\VendorsConfig\Model\Config\Backend;

class Encrypted extends \Vnecoms\VendorsConfig\Model\Config
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Vnecoms\VendorsConfig\Model\ResourceModel\Config $resource
     * @param \Vnecoms\VendorsConfig\Model\ResourceModel\Config\Collection $resourceCollection
     * @param \Vnecoms\VendorsConfig\Helper\Data $configHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Vnecoms\VendorsConfig\Model\ResourceModel\Config $resource = null,
        \Vnecoms\VendorsConfig\Model\ResourceModel\Config\Collection $resourceCollection = null,
        \Vnecoms\VendorsConfig\Helper\Data $configHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        array $data = []
    ) {
        $this->_encryptor = $encryptor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $configHelper, $data);
    }

    /**
     * Magic method called during class serialization
     *
     * @return string[]
     */
    public function __sleep()
    {
        $properties = parent::__sleep();
        return array_diff($properties, ['_encryptor']);
    }

    /**
     * Magic method called during class un-serialization
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $this->_encryptor = \Magento\Framework\App\ObjectManager::getInstance()->get(
            'Magento\Framework\Encryption\EncryptorInterface'
        );
    }

    /**
     * Decrypt value after loading
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = $this->_encryptor->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    /**
     * Encrypt value before saving
     *
     * @return void
     */
    public function beforeSave()
    {
        $this->_dataSaveAllowed = false;
        $value = (string)$this->getValue();
        // don't save value, if an obscured value was received. This indicates that data was not changed.
        if (!preg_match('/^\*+$/', $value) && !empty($value)) {
            $this->_dataSaveAllowed = true;
            $encrypted = $this->_encryptor->encrypt($value);
            $this->setValue($encrypted);
        } elseif (empty($value)) {
            $this->_dataSaveAllowed = true;
        }
    }

    /**
     * Process config value
     *
     * @param string $value
     * @return string
     */
    public function processValue($value)
    {
        return $this->_encryptor->decrypt($value);
    }
}
