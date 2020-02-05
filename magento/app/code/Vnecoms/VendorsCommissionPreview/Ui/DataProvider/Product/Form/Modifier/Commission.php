<?php
namespace Vnecoms\VendorsCommissionPreview\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

/**
 * Data provider for "Customizable Options" panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Commission extends AbstractModifier
{    
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
  
    /**
     * @var array
     */
    protected $_meta = [];
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    
    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Ui\DataProvider\Modifier\ModifierInterface::modifyData()
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
        $this->_meta = $meta;
        $this->addCommissionField('price');
        return $this->_meta;
    }
    
    /**
     * Customize credit dropdown value field
     *
     * @return $this
     */
    protected function addCommissionField($afterField)
    {
        $fieldPath = $this->arrayManager->findPath(
            $afterField,
            $this->_meta,
            null,
            'children'
        );
        $fieldPath = $this->arrayManager->slicePath($fieldPath, 0, -1);
        if ($fieldPath) {
            $fieldMeta = $this->arrayManager->get($fieldPath, $this->_meta);
            $fieldMeta['commission_preview'] = $this->getCommissionFieldMeta();
            $this->_meta = $this->arrayManager->merge(
                $fieldPath,
                $this->_meta,
                $fieldMeta
            );
        }
    
        return $this;
    }
    
    public function getCommissionFieldMeta(){
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'group',
                        'title' => __('Add Option'),
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'additionalClasses' => 'admin__field-small',
                        'template' => 'Vnecoms_VendorsCommissionPreview/commission',
                        'component' => 'Vnecoms_VendorsCommissionPreview/js/components/commission',
                        'additionalForGroup' => true,
                        'provider' => false,
                        'source' => 'product_details',
                        'displayArea' => 'insideGroup',
                        'sortOrder' => 25,
                        'commissionCalcUrl' => $this->urlBuilder->getUrl('commission/preview'),
                    ],
                ],
            ],
        ];
    }
}
