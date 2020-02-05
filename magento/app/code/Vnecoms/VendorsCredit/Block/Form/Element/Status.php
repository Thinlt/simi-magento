<?php
namespace Vnecoms\VendorsCredit\Block\Form\Element;

use Vnecoms\VendorsCredit\Model\Withdrawal;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Vnecoms\VendorsCredit\Model\CreditProcessor\Withdraw;

class Status extends AbstractElement
{
    /**
     * @var \Vnecoms\VendorsCredit\Model\Source\Status
     */
    protected $_withdrawalStatus;
    
    /**
     * Constructor
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param \Vnecoms\VendorsCredit\Model\Source\Status $withdrawalStatus
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Vnecoms\VendorsCredit\Model\Source\Status $withdrawalStatus,
        $data = []
    ) {
        $this->_withdrawalStatus = $withdrawalStatus;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }
    
    /**
     * Get Element HTML
     * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
     */
    public function getElementHtml()
    {
        $status = $this->getValue();
        switch ($status) {
            case Withdrawal::STATUS_PENDING:
                return '<span class="vendor-status withdrawal-status vendor-status-pending">'.$this->getStatusLabel($status).'</span>';
            case Withdrawal::STATUS_COMPLETED:
                return '<span class="vendor-status withdrawal-status vendor-status-approved">'.$this->getStatusLabel($status).'</span>';
            case Withdrawal::STATUS_CANCELED:
                return '<span class="vendor-status withdrawal-status vendor-status-disabled">'.$this->getStatusLabel($status).'</span>';
        }
    }
    
    
/**
 * Get Withdrawal Status Label
 *
 * @return string
 */
    public function getStatusLabel($status)
    {
        $statusOptions = $this->_withdrawalStatus->getOptionArray();
        return isset($statusOptions[$status])?$statusOptions[$status]:'';
    }
}
