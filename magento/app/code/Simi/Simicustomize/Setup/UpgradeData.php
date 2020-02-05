<?php

namespace Simi\Simicustomize\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Framework\DB\Ddl\Table;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    private $logger;

    /**
     * Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Magento\Store\Api\StoreRepositoryInterface;
     */
    protected $storeRepository;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
    * @param EavSetup $eavSetup,
    * @param ObjectManagerInterface $objectManager,
    * @param StoreRepositoryInterface $storeRepository,
    * @param LoggerInterface $logger,
    * @param SalesSetupFactory $salesSetupFactory
    */
    public function __construct(
        EavSetup $eavSetup,
        ObjectManagerInterface $objectManager,
        StoreRepositoryInterface $storeRepository,
        LoggerInterface $logger,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->eavSetup = $eavSetup;
        $this->_objectManager = $objectManager;
        $this->storeRepository = $storeRepository;
        $this->logger = $logger;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $attributes = [
                'try_to_buy' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Try to buy',
                    'visible'            => true,
                    'required'           => false,
                    'user_defined'               => false,
                    'searchable'                 => false,
                    'filterable'                 => true,
                    'comparable'                 => false,
                    'visible_on_front'           => false,
                    'visible_in_advanced_search' => false,
                    'is_html_allowed_on_front'   => false,
                    'used_for_promo_rules'       => true,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ],
                'reservable' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Reservable',
                    'visible'            => true,
                    'required'           => false,
                    'user_defined'               => false,
                    'searchable'                 => false,
                    'filterable'                 => true,
                    'comparable'                 => false,
                    'visible_on_front'           => false,
                    'visible_in_advanced_search' => false,
                    'is_html_allowed_on_front'   => false,
                    'used_for_promo_rules'       => true,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ],
                'pre_order' => [
                    // 'group'              => '',
                    'input'              => 'boolean',
                    'type'               => 'int',
                    'label'              => 'Pre order',
                    'visible'            => true,
                    'required'           => false,
                    'user_defined'               => false,
                    'searchable'                 => false,
                    'filterable'                 => true,
                    'comparable'                 => false,
                    'visible_on_front'           => false,
                    'visible_in_advanced_search' => false,
                    'is_html_allowed_on_front'   => false,
                    'used_for_promo_rules'       => true,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_NO,
                    // 'frontend_class'             => '',
                    'global'                     =>  \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'unique'                     => false,
                    'apply_to'                   => 'simple,grouped,configurable,downloadable,virtual,bundle,aw_giftcard'
                ]
            ];
    
            foreach ($attributes as $attribute_code => $attributeOptions) {
                $this->eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attribute_code,
                    $attributeOptions
                );
            }
        }

        /**
         * Add try to buy order status
         */
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.2', '<')) {
            $data = [];
            $statusCode = 'try_to_buy';
            $storeLabel = 'Try To Buy';
            $status = $this->_objectManager->create(\Magento\Sales\Model\Order\Status::class)->load($statusCode);
            if ($status && !$status->getStatus()) {
                // try {
                //     throw new \Exception(__('We found another order status with the same order status try_to_buy code.'));
                // } catch (\Exception $e) {
                //     $this->logger->log($e, 1);
                // }
                try {
                    $data['status'] = $statusCode;
                    $data['label'] = $storeLabel;
                    if (!isset($data['store_labels'])) {
                        $data['store_labels'] = [];
                    }
                    // get all stores \Magento\Store\Api\Data\StoreInterface[]
                    $stores = $this->storeRepository->getList();
                    foreach ($stores as $store) {
                        $data['store_labels'][(int)$store->getId()] = $storeLabel;
                    }
                    // save status
                    $status->setData($data)->setStatus($statusCode);
                    $status->save();
                    // assign status to state
                    $isDefault = 0;
                    $visibleOnFront = 1;
                    $status->assignState('pending', $isDefault, $visibleOnFront);
                } catch (\Exception $e) {
                    $this->logger->log($e, 1);
                }
            }
        }

        /**
         * Add pre_order order status
         */
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {
            $data = [];
            $statusCode = 'pre_order';
            $storeLabel = 'Pre-order';
            $status = $this->_objectManager->create(\Magento\Sales\Model\Order\Status::class)->load($statusCode);
            if ($status && !$status->getStatus()) {
                try {
                    $data['status'] = $statusCode;
                    $data['label'] = $storeLabel;
                    if (!isset($data['store_labels'])) {
                        $data['store_labels'] = [];
                    }
                    // get all stores \Magento\Store\Api\Data\StoreInterface[]
                    $stores = $this->storeRepository->getList();
                    foreach ($stores as $store) {
                        $data['store_labels'][(int)$store->getId()] = $storeLabel;
                    }
                    // save status
                    $status->setData($data)->setStatus($statusCode);
                    $status->save();
                    // assign status to state
                    $status->assignState('processing', $isDefault = 0, $visibleOnFront = 1);
                } catch (\Exception $e) {
                    $this->logger->log($e, 1);
                }
            }

            /**
             * Add pre-order deposit to sales order attribute
             */
            /** @var SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute('order', 'order_type', ['type' => Table::TYPE_TEXT, 'length' => 100]);
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.4', '<')) {

        }

        /**
         * Add pre_order deposit order increment id
         */
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.10', '<')) {
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute('order', 'deposit_order_increment_id', ['type' => Table::TYPE_TEXT, 'length' => 100]);
        }
        /**
         * Add pre_order deposit order increment id
         */
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.12', '<')) {
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute('order', 'preorder_deposit_discount', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => "12,4"]);
        }
        /**
         * Add service support fee to order
         */
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.13', '<')) {
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
            $salesSetup->addAttribute('order', 'service_support_fee', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => "12,4"]);
        }
    }
}
