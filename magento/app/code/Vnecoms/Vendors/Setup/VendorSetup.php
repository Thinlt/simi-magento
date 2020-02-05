<?php
/**
 * Customer resource setup model
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;

/**
 * @codeCoverageIgnore
 */
class VendorSetup extends EavSetup
{
    /**
     * EAV configuration
     *
     * @var Config
     */
    protected $eavConfig;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context $context
     * @param CacheInterface $cache
     * @param CollectionFactory $attrGroupCollectionFactory
     * @param Config $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Retrieve default entities: customer, customer_address
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        $entities = [
            'vendor' => [
                'entity_model' => 'Vnecoms\Vendors\Model\ResourceModel\Vendor',
                'attribute_model' => 'Vnecoms\Vendors\Model\Attribute',
                'table' => 'ves_vendor_entity',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'additional_attribute_table' => 'ves_vendor_eav_attribute',
                'entity_attribute_collection' => 'Vnecoms\Vendors\Model\ResourceModel\Attribute\Collection',
                'attributes' => [
                    'vendor_id' => [
                        'type' => 'static',
                        'label' => 'Vendor Id',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 30,
                        'position' => 30,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                        'unique' => 1,
                        'frontend_class'=> 'validate-code'
                    ],
                    'created_at' => [
                        'type' => 'static',
                        'label' => 'Created At',
                        'input' => 'date',
                        'required' => false,
                        'sort_order' => 86,
                        'visible' => false,
                        'system' => false,
                    ],
                    'updated_at' =>[
                        'type' => 'static',
                        'label' => 'Updated At',
                        'input' => 'date',
                        'required' => false,
                        'sort_order' => 87,
                        'visible' => false,
                        'system' => false,
                    ],
                    'group_id' => [
                        'type' => 'static',
                        'label' => 'Group',
                        'input' => 'select',
                        'source' => 'Vnecoms\Vendors\Model\Source\Group',
                        'required' => true,
                        'sort_order' => 60,
                        'position' => 40,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 0,
                    ],
                    'status' => [
                        'type' => 'static',
                        'label' => 'Status',
                        'input' => 'select',
                        'source' => 'Vnecoms\Vendors\Model\Source\Status',
                        'required' => true,
                        'sort_order' => 60,
                        'position' => 50,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 0,
                    ],
                    'company' => [
                        'type' => 'static',
                        'label' => 'Company',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 60,
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position' => 60,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'street' => [
                        'type' => 'static',
                        'label' => 'Street Address',
                        'input' => 'text',
                        'sort_order' => 70,
                        'multiline_count' => 2,
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position' => 70,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'city' => [
                        'type' => 'static',
                        'label' => 'City',
                        'input' => 'text',
                        'sort_order' => 80,
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position' => 80,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'country_id' => [
                        'type' => 'static',
                        'label' => 'Country',
                        'input' => 'select',
                        'source' => 'Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country',
                        'sort_order' => 90,
                        'position' => 90,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'region' => [
                        'type' => 'static',
                        'label' => 'State/Province',
                        'input' => 'text',
                        'backend' => 'Magento\Customer\Model\ResourceModel\Address\Attribute\Backend\Region',
                        'required' => false,
                        'sort_order' => 100,
                        'position' => 100,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'region_id' => [
                        'type' => 'static',
                        'label' => 'State/Province',
                        'input' => 'hidden',
                        'source' => 'Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Region',
                        'required' => false,
                        'sort_order' => 100,
                        'position' => 100,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'postcode' => [
                        'type' => 'static',
                        'label' => 'Zip/Postal Code',
                        'input' => 'text',
                        'sort_order' => 110,
                        'validate_rules' => 'a:0:{}',
                        'data' => 'Magento\Customer\Model\Attribute\Data\Postcode',
                        'position' => 110,
                        'required' => false,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'telephone' => [
                        'type' => 'static',
                        'label' => 'Phone Number',
                        'input' => 'text',
                        'sort_order' => 120,
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position' => 120,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                    'fax' => [
                        'type' => 'static',
                        'label' => 'Fax',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 130,
                        'validate_rules' => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position' => 130,
                        'used_in_profile_form' => 1,
                        'used_in_registration_form' => 1,
                    ],
                ],
            ]
        ];
        return $entities;
    }

    /**
     * Gets EAV configuration
     *
     * @return Config
     */
    public function getEavConfig()
    {
        return $this->eavConfig;
    }
}
