<?php
/**
 * Created by PhpStorm.
 * User: mrtuvn
 * Date: 23/01/2017
 * Time: 22:42
 */

namespace Vnecoms\PdfProCustomVariables\Model;

use Magento\Framework\Model\AbstractModel;

class PdfproCustomVariables extends AbstractModel
{
    const VARIABLE_TYPE_PRODUCT = 'attribute';
    const VARIABLE_TYPE_CUSTOMER = 'customer';
    
    /** @var \Magento\Eav\Model\Entity\Attribute  */
    protected $eavAttribute;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\Entity\Attribute $eavAttribute,
        \Magento\Framework\Model\ResourceModel\AbstractResource  $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->eavAttribute = $eavAttribute;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Vnecoms\PdfProCustomVariables\Model\ResourceModel\PdfproCustomVariables');
        $this->setIdFieldName('custom_variable_id');
    }

    /**
     * @return mixed
     */
    public function getCustomVariableId()
    {
        return $this->getData('custom_variable_id');
    }

    /**
     * @param int $id
     * @return PdfproCustomVariables
     */
    public function setCustomVariableId($id)
    {
        return $this->setData('custom_variable_id', $id);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @param string $name
     * @return PdfproCustomVariables
     */
    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    /**
     * @return string
     */
    public function getVariableType()
    {
        return $this->getData('variable_type');
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        return $this->getData('attribute_id');
    }

    /**
     * @return int
     */
    public function getAttributeIdCustomer()
    {
        return $this->getData('attribute_id_customer');
    }

    /**
     * @param string $type
     * @return PdfproCustomVariables
     */
    public function setVariableType($type)
    {
        return $this->setData('variable_type', $type);
    }

    /**
     * @param PdfproCustomVariables $type
     * @return bool
     */
    public function checkAttributeFrontendType($type = null)
    {
        switch ($this->getVariableType()) {
            case 'attribute':
                $attributeId    = $this->getAttributeId();
                $attributeInfo = $this->eavAttribute->setEntityTypeId('catalog_product')->load($attributeId)->getData();
                break;
            case 'customer':
                $attributeId    = $this->getAttributeIdCustomer();
                $attributeInfo  = $this->getAttributeInfo($attributeId);
                break;
        }
        return isset($attributeInfo['frontend_input']) ? $attributeInfo['frontend_input'] == $type : false;
    }

    /**
     * get attribute info from attribute id
     * in eav_attribute table
     * @param int $attribute_id
     * @return string[]
     */
    public function getAttributeInfo($attributeId=false)
    {
        $attributeId = $attributeId?$attributeId:$this->getAttributeId();
        $resource = $this->getResource();
        $readConnection = $resource->getConnection();
        $table = $resource->getTable('eav_attribute');
        $select = $readConnection->select()->from($table, ['*'])->where('attribute_id = ?', $attributeId);
        $rowsArray = $readConnection->fetchRow($select);

        return $rowsArray;
    }
}
