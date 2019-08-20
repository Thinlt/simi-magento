<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:44
 */

namespace Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'custom_variable_id';

    protected function _construct()
    {
        $this->_init('Vnecoms\PdfProCustomVariables\Model\PdfproCustomVariables', 'Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables');
        $this->_map['fields']['custom_variable_id'] = 'main_table.custom_variable_id';
    }

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    /**
     * Returns pairs identifier - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('custom_variable_id', 'name');
    }
}
