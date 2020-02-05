<?php

namespace Simi\Simipaypalexpress\Model;

/**
 * Connector Model
 *
 * @method \Simi\Simiconnector\Model\Resource\Page _getResource()
 * @method \Simi\Simiconnector\Model\Resource\Page getResource()
 */
class Simiconnectorexpress extends \Magento\Framework\Model\AbstractModel
{

    public $quote;
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Simi\Simiconnector\Model\ResourceModel\Banner $resource,
        \Simi\Simiconnector\Model\ResourceModel\Banner\Collection $resourceCollection,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    ) {
        $this->simiObjectManager = $simiObjectManager;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }
    
    public function getAddress($data) {
        if (!count($data))
            return $data;
        return $this->simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Address')->getAddressDetail($data);
    }

    public function updateAddress($parameters) {
        $checkout = $this->_getOnepage();
        $this->getQuote()->setTotalsCollectedFlag(true);
        $checkout->setQuote($this->quote);
        if (isset($parameters['s_address'])) {
            $this->simiObjectManager->get('Simi\Simiconnector\Helper\Address')
                    ->saveShippingAddress($parameters['s_address']);
        }
        
        if (isset($parameters['b_address'])) {
            $this->simiObjectManager->get('Simi\Simiconnector\Helper\Address')
                    ->saveBillingAddress($parameters['b_address']);
        }
        
        $this->getQuote()->setTotalsCollectedFlag(false);
        $this->getQuote()->collectTotals();
        $this->getQuote()->setDataChanges(true);
        $this->getQuote()->save();
    }

    /*
     * Get Billing and Shipping address
     */

    public function getBillingShippingAddress() {
        $info = array();
        $billingAddress = $this->getAddress($this->getBillingAddress());
        $shippingAddress = $this->getAddress($this->getShippingAddress());
        if (!count($shippingAddress))
            $shippingAddress = $billingAddress;
        $billingAddress["address_id"] = $this->getBillingAddress()->getId();
        if (!$this->simiObjectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            $billingAddress['email'] = $this->getBillingAddress()->getEmail();
            $shippingAddres['email'] = $this->getBillingAddress()->getEmail();
            $info[] = array(
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
            );
        } else {
            $billingAddress['email'] = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer()->getEmail();
            $shippingAddres['email'] = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer()->getEmail();
            $info[] = array(
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
            );
        }
        $data = $this->statusSuccess();
        $data['data'] = $info;
        return $data;
    }
        
    public function statusSuccess() {
        return array('status' => 'SUCCESS',
            'message' => array('SUCCESS'),           
        );
    }
    
    public function getBillingAddress() {
        return $this->getQuote()->getBillingAddress();
    }
    
    public function getShippingAddress() {
        if ($this->getQuote()->getIsVirtual()) {
            return array();
        }
        return $this->getQuote()->getShippingAddress();
    }
    
    public function getQuote() {
        if (!$this->quote) {
            $this->quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->quote;
    }

    public function _getOnepage()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Type\Onepage');
    }
    
    public function _getCheckoutSession() {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Session');
    }
}
