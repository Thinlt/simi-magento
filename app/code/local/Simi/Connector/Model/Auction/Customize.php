<?php
/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Connector
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Connector Model
 *
 * @category
 * @package     Connector
 * @author      Developer
 */
class Simi_Connector_Model_Auction_Customize extends Simi_Connector_Model_Abstract
{
    protected $_product = null;

    public function setListAuction(&$collection)
    {
        if (!Mage::registry('current_category')) {
            $category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId())
                ->setIsAnchor(1)
                ->setName(Mage::helper('core')->__('Auctions'))
                ->setDisplayMode('PRODUCTS');
            Mage::register('current_category', $category);
        }
        Mage::helper('auction')->updateAuctionStatus();
        $collection->addFieldToFilter('entity_id', array('in' => $Ids = Mage::helper('auction')->getProductAuctionIds(Mage::app()->getStore()->getId())));
    }

    public function setActionToProduct($product, &$info)
    {
        $this->setProduct($product);
        $storeId = Mage::app()->getStore()->getId();
        $_helper = Mage::helper('auction');
        $this->requiredBidderName();

        if ($_helper->getBidderStatus()) {
            $auction = $this->getAuction();
            if ($auction) {
                if ($auction->getStatus() == 4) {
                    $info['auction_now_time'] = Mage::getModel('core/date')->timestamp(time());
                    $info['auction_end_time'] = strtotime($auction->getEndTime() . ' ' . $auction->getEndDate());
                    $info['auction_is_watch'] = $this->isWatcher();
                    $lastBid = $auction->getLastBid();
                    $min_next_price = $auction->getMinNextPrice();
                    $max_next_price = $auction->getMaxNextPrice();
                    $max_condition = $max_next_price ? ' ' . Mage::helper('auction')->__('to') . ' ' . Mage::helper('core')->currency($max_next_price,  true, false) : '';
                    $message_condition = Mage::helper('auction')->__('Your bid');
                    if ($max_condition) {
                        $message_condition .= '(' . Mage::helper('auction')->__('Enter an amount from') . ' ' . Mage::helper('core')->currency($min_next_price, true, false) . $max_condition . ')';
                        $info['auction_condition_price'] = array('min_nex_price' => $min_next_price, 'max_next_price' => $max_next_price, 'show' => 1);
                    } else {
                        $message_condition .= '(' . Mage::helper('auction')->__('Enter %s or more', Mage::helper('core')->currency($min_next_price, true, false)) . ')';
                        $info['auction_condition_price'] = array('min_nex_price' => $min_next_price, 'max_next_price' => $min_next_price, 'show' => 0);
                    }
                    $info['auction_message_condition'] = $message_condition;
                    $info['auction_is_auto_bid'] = Mage::getStoreConfig('auction/general/enable_autobid');
                    $info['auction_total_bid'] = $auction->getTotalBid();
                    $info['auction_current_price'] = $lastBid->getPrice() ? $lastBid->getPrice() : $auction->getInitPrice();
                    $info['auction_bidder_name'] = $lastBid ? $lastBid->getBidderName() : Mage::helper('auction')->__('None');
                    $info['auction_start_price'] = $auction->getInitPrice();
                    $info['auction_start_time'] = Mage::helper('core')->formatDate(new Zend_Date($auction->getStartDate() . ' ' . $auction->getStartTime(), null, 'en_GB'), 'medium', true);
                    $info['auction_close_time'] = Mage::helper('core')->formatDate(new Zend_Date($auction->getEndDate() . ' ' . $auction->getEndTime(), null, 'en_GB'), 'medium', true);
                    $info['auction_allow_buyout'] = $auction->getAllowBuyout();
                    $info['auction_status'] = 'processing';
                    $info['auction_bidder_name_required'] = 0;
                    if(!$this->isLoggedIn() || $this->requiredBidderName()){
                        $info['auction_bidder_name_required'] = 1;
                    }
                } elseif ($auction->getStatus() == 5) {
                    //change current_price to closing price when have winers.
                    $lastBid = $auction->getLastBid();
                    $info['auction_winers'] = $this->getWinnerList();
                    $info['auction_start_time'] = Mage::helper('core')->formatDate(new Zend_Date($auction->getStartDate() . ' ' . $auction->getStartTime(), null, 'en_GB'), 'medium', true);
                    $info['auction_close_time'] = Mage::helper('core')->formatDate(new Zend_Date($auction->getEndDate() . ' ' . $auction->getEndTime(), null, 'en_GB'), 'medium', true);
                    $info['auction_closing_price'] = $lastBid->getPrice() ? $lastBid->getPrice() : $auction->getInitPrice();
                    $info['auction_total_bid'] = $auction->getTotalBid();
                    $info['auction_message'] = Mage::helper('auction')->__('Completed Auction');
                    if($this->isWinner()){
                        $info['auction_message'] = Mage::helper('auction')->__('Congratulations');
                        $info['auction_message_content'] = Mage::helper('auction')->__('You have become the winner on this auction.');
                        $info['auction_is_checkout'] = 1;
                    }
                    $info['auction_status'] = 'completed';
                    $info['auction_bidder_name_required'] = 0;
                    if(!$this->isLoggedIn() || $this->requiredBidderName()){
                        $info['auction_bidder_name_required'] = 1;
                    }
                }
            }
        }
    }

    public function getBidUrl()
    {
        return Mage::getUrl('auction/index/bid', array());
    }

    public function isWatcher()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $auction = $this->getAuction();
        $watcher = Mage::getModel('auction/watcher')->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
            ->addFieldToFilter('status', 1);
        if (count($watcher))
            return true;
        else
            return false;
    }

    public function getAuction()
    {
        $product = $this->getProduct();
        if (!$this->hasData('auction')) {
            $auction = Mage::getModel('auction/productauction')->loadAuctionByProductId($product->getId());
            if ($auction) {
                $auction->setStoreId(Mage::app()->getStore()->getId())
                    ->loadByStore();
                if ($auction->getData('is_applied') == '2')
                    $auction = null;
            }
            $this->setData('auction', $auction);
        }

        return $this->getData('auction');
    }

    public function setProduct($product)
    {
        $this->_product = $product;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function getWinners()
    {
        if (!$this->hasData('winners')) {
            $winners = Mage::helper('auction')->getWinnerBids($this->getAuction()->getId());
            $this->setData('winners', $winners);
        }
        return $this->getData('winners');
    }

    public function getWinnerList()
    {
        $listWinner = '';
        $winnerBids = $this->getWinners();
        if (count($winnerBids)) {
            $i = 0;
            foreach ($winnerBids as $winnerBid) {
                $i++;
                $listWinner .= $winnerBid->getBidderName();
                if ($i != count($winnerBids))
                    $listWinner .= ', ';
            }
        } else {
            $listWinner = 'None';
        }
        return $listWinner;
    }

    public function isWinner()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $winners = $this->getWinners();
            if (count($winners)) {
                foreach ($winners as $winner) {
                    if ($customer->getId() == $winner->getCustomerId())
                        return true;
                }
            }
        }
        return false;
    }

    public function getBidsForCustomer()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('auction/auction')
            ->getListBidByCustomerId($customerId);
        $information = $this->statusSuccess();
        $list = array();
        $listStatus = Mage::helper('auction')->getListBidStatus();
        $store = Mage::getModel('core/store');
        foreach ($collection as $item) {
            $store->load($item->getStoreId());
            $list[] = array(
                'product_name' => $item->getData('product_name'),
                'product_id' => $item->getData('product_id'),
                'auctionbid_id' => $item->getData('auctionbid_id'),
                'price' => $item->getData('price'),
                'created_time' => $item->getData('created_time'),
                'created_date' => $item->getData('created_date'),
                'status' => $listStatus[$item->getData('status')],
                'store_name' => $store->getName(),
                'bidder_name' => $item->getBidderName(),
            );
        }
        $information['data'] = $list;
        return $information;
    }

    public function getAutoBidsForCustomer(){
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('auction/autobid')
            ->getListByCustomerId($customerId);
        $information = $this->statusSuccess();
        $list = array();
        $listStatus = Mage::helper('auction')->getListAuctionStatus();
        foreach ($collection as $item) {
            $auction = Mage::getModel('auction/productauction')->load($item->getProductauctionId());
            $product = Mage::getModel('catalog/product')->load($auction->getProductId());
            $list[] = array(
                'product_name' => $product->getName(),
                'autobid_id' => $item->getData('autobid_id'),
                'current_price' => $auction->getCurrentPrice(),
                'place_price' => $item->getData('price'),
                'total_bids' => $auction->getTotalBid(),
                'end_time' => $auction->getFormatedEndTime('short'),
                'status' => $listStatus[$auction->getStatus()],
                'bidder_name' => $item->getBidderName(),
                'product_id' => $item->getData('product_id'),
            );
        }
        $information['data'] = $list;
        return $information;
    }

    public function getWatchedAuctionsForCustomer(){
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('auction/watcher')
            ->getListByCustomerId($customerId);

        $information = $this->statusSuccess();
        $list = array();
        $listStatus = Mage::helper('auction')->getListAuctionStatus();
        foreach ($collection as $item) {
            $list[] = array(
                'product_name' => $item->getData('product_name'),
                'product_id' => $item->getData('product_id'),
                'start_price' => $item->getInitPrice(),
                'current_price' => $item->getCurrentPrice(),
                'close_price' => ((int)$item->getStatus() >4) ? $item->getFormatedClosePrice() : '',
                'start_time' => $item->getFormatedStartTime('short'),
                'end_time' =>  $item->getFormatedEndTime('short'),
                'status' => $listStatus[$item->getData('status')],
                'total_bids' => $item->getTotalBid(),
                'bidder_name' => $item->getBidderName(),
            );
        }
        $information['data'] = $list;
        return $information;
    }

    public function getBidsHistoryForCustomer($auction_id, $curr_page){
        $collection = Mage::getModel('auction/productauction')->setId($auction_id)
            ->getListBid();

        $collection->setPageSize(10);
        $collection->setCurPage($curr_page);
        $store = Mage::getModel('core/store');
        $information = $this->statusSuccess();
        $list = array();

        foreach ($collection as $item) {
            $create_time =  new Zend_Date($item->getCreatedDate().' '.$item->getCreatedTime(),null,'en_GB');
            $store->load($item->getStoreId());
            $list[] = array(
                'bidder_name' => $item->getBidderName(),
                'bid_amount' => $item->getPrice(),
                'bid_time' => Mage::helper('core')->formatDate($create_time,'medium',true),
                'current_price' => $item->getCurrentPrice(),
                'store_name' => $store->getName(),
            );
        }
        $information['data'] = $list;
        return $information;
    }

    public function requiredBidderName() {
        $biddertype = (int) Mage::getStoreConfig('auction/general/bidder_name_type');
        if ($biddertype == 2) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$customer->getBidderName()) {
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getBidToCheckout($product){
        $this->setProduct($product);
        $lastBid = Mage::getResourceModel('auction/auction_collection')
            ->addFieldToFilter('productauction_id', $this->getAuction()->getId())
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomerId())
            ->addFieldToFilter('status', 5)
            ->setOrder('auctionbid_id', 'DESC')
            ->getFirstItem();
        return $lastBid->getId();
    }
}