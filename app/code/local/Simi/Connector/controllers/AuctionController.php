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
 * Customer Controller
 *
 * @category
 * @package     Connector
 * @author      Developer
 */
class Simi_Connector_AuctionController extends Simi_Connector_Controller_Action {

    //?id=1
    public function cancel_bidAction() {
        $data = $this->getData();
        $id = $data->id;
        $bid = Mage::getModel('auction/auction')->load($id);
        $modelAuction = Mage::getModel('connector/abstract');
        $_helper = Mage::helper('auction');

        if ($bid->getStatus() != 1 && $bid->getStatus() != 3) {
            $result = $_helper->__('You bid price is invalid.');
            $information = $modelAuction->statusError();
            $information['message'] = array($result);
            $this->_printDataJson($information);
            return;
        }
        try {
            $bid->setStatus(2)->save();
            Mage::helper('auction')->updateAuctionStatus();
            $information = Mage::getModel('connector/auction_customize')->getBidsForCustomer();
            $this->_printDataJson($information);
            return;
        } catch (Exception $e) {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('The bid cancelation has been failed. Please try again.'));
            $this->_printDataJson($information);
            return;
        }
    }

    //?bid_price=322&product_id=166&bid_type=1&weigh=100&heigh=100
    /**
     * return the product information.
     */
    public function bidAction() {
        $result = "";
        $_helper = Mage::helper('auction');
        $notice = Mage::getSingleton('auction/notice');
        $modelAuction = Mage::getModel('connector/abstract');
        //check login
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $backUrl = $_SERVER['HTTP_REFERER'];
                Mage::getSingleton('core/session')->setData('auction_backurl', $backUrl);
            }
            $result .= $_helper->__('You have to log in to bid.');

            $information = $modelAuction->statusError();
            $information['message'] = array($result);
            $this->_printDataJson($information);
            return;
        }
        $data_r = $this->getData();
        $bidType = $data_r->bid_type;
        $data['price'] = $data_r->bid_price;
        $data['product_id'] = $data_r->product_id;

        if (!isset($data['price']) || !$data['price']) {
            $result .= $_helper->__('You bid price is invalid.');
            $information = $modelAuction->statusError();
            $information['message'] = array($result);
            $this->_printDataJson($information);
            return;
        }

        $data['price'] = str_replace(',', '', $data['price']);
        $data['price'] = str_replace(' ', '', $data['price']);

        $customer = $customerSession->getCustomer();
        $bidderNameType = Mage::getStoreConfig('auction/general/bidder_name_type');
        if (!$customer->getBidderName()) {
            if ($bidderNameType == '2') {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $backUrl = $_SERVER['HTTP_REFERER'];
                    Mage::getSingleton('core/session')->setData('auction_backurl', $backUrl);
                }
                $result .= $_helper->__('You have to create a bidder name before bidding for auctioned products.');

                $information = $modelAuction->statusError();
                $information['message'] = array($result);
                $this->_printDataJson($information);
                return;
            }
            if ($bidderNameType == '3') {
                if (!$customer->getBidderName()) {
                    $customer->setBidderName($customer->getName())
                        ->save();
                }
            }
        }
        $product = Mage::getModel('catalog/product')->load($data['product_id']);
        $auction = Mage::getModel('auction/productauction')->loadAuctionByProductId($data['product_id']);

        if ($auction->getStatus() == 5) { //complete auction
            $result .= $_helper->__('Completed Auction');

            $information = $modelAuction->statusError();
            $information['message'] = array($result);
            $this->_printDataJson($information);
            return;
        }

        $timestamp = Mage::getModel('core/date')->timestamp(time());

        $lastBid = $auction->getLastBid();

        if (!Mage::helper('auction')->checkValidBidPrice($data['price'], $auction, $bidType)) {
            $result .= $_helper->__('You bid price is invalid.');

            $information = $modelAuction->statusError();
            $information['message'] = array($result);
            $this->_printDataJson($information);
            return;
        }
        $data['productauction_id'] = $auction->getId();
        $data['customer_id'] = $customer->getId();
        $data['customer_name'] = $customer->getName();
        $data['customer_email'] = $customer->getEmail();
        $store_id = Mage::app()->getStore()->getId();

        //prepare bidder name
        if ($bidderNameType == '1') {
            $data['bidder_name'] = $_helper->encodeBidderName($auction, $customer);
        } else {
            $data['bidder_name'] = $customer->getBidderName();
        }
        //end bidder name

        /* check standard bid (1) or auto bid (0) */
        if ($bidType) {
            $auctionbid = Mage::getModel('auction/auction');
            $lastAuctionBid = $auction->getLastBid();

            if ($customer->getId() == $lastAuctionBid->getCustomerId()) {

                $result .= $_helper->__('You have placed the highest bid.');

                $information = $modelAuction->statusError();
                $information['message'] = array($result);
                $this->_printDataJson($information);
                return;
            }

            $data['product_name'] = $product->getName();
            $data['created_date'] = date('Y-m-d', $timestamp);
            $data['created_time'] = date('H:i:s', $timestamp);
            $data['status'] = 3; //waiting
            $auctionbid->setData($data)
                ->setStoreId($store_id);

            //get autobids greater  current price (before save)
            $customersId = Mage::getModel('auction/email')->getCollection()->addFieldToFilter('overautobid', 0)->getAllCustomerIds();
            $autobids = Mage::getModel('auction/autobid')->getCollection()
                ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
                ->addFieldToFilter('price', array('gteq' => $auction->getMinNextPrice()));

            if (count($customersId) > 0) {
                $autobids->addFieldToFilter('customer_id', array('nin' => $customersId));
            }
            $autobidIds = array();
            foreach ($autobids as $autobid) {
                $autobidIds[] = $autobid->getId();
            }

            try {
                $auctionbid->save();

                $auction->setLastBid($auctionbid);
                $auctionbid->setAuction($auction);
                $auctionbid->emailToWatcher();
                $auctionbid->emailToBidder();
                $auctionbid->emailToAdmin();

                $auctionbid->sendNoticToAllBider($auctionbid->getCustomerId(), $auctionbid->getProductauctionId());

                //get autobids over
                $overAutobids = Mage::getModel('auction/autobid')->getCollection()
                    ->addFieldToFilter('productauction_id', $auction->getProductauctionId())
                    ->addFieldToFilter('price', array('lt' => $auction->getMinNextPrice()))
                    ->addFieldToFilter('autobid_id', array('in' => $autobidIds))
                ;

                if (count($overAutobids))
                    $auctionbid->noticeOverautobid($overAutobids);

                if (strtotime($auction->getEndDate() . ' ' . $auction->getEndTime()) - $timestamp <= $auction->getLimitTime()) {
                    $newTime = $timestamp + (int) $auction->getLimitTime();
                    $new_endDate = date('Y-m-d', $newTime);
                    $new_endTime = date('H:i:s', $newTime);
                    $auction->setEndDate($new_endDate)
                        ->setEndTime($new_endTime);
                    $auction->save();
                }

                $store = Mage::app()->getStore();
                $baseCurrency = $store->getBaseCurrency();
                $currCurrency = $store->getCurrentCurrency();
                if ($baseCurrency->getCode() != $currCurrency->getCode()) {
                    $store->setCurrentCurrencyCode($baseCurrency->getCode());
                    $store->setData('current_currency', $baseCurrency);
                }
                $lastBid = $auction->getLastBid();
                $new_endtime = strtotime($auction->getEndTime() . ' ' . $auction->getEndDate());
                $now_time = Mage::getSingleton('core/date')->timestamp(time());
                $result = '<div id="result_auction_id">' . $auction->getId() . '</div>';
                $result .= '<div id="result_auction_end_time_' . $auction->getId() . '">' . $new_endtime . '</div>';
                $result .= '<div id="result_auction_now_time_' . $auction->getId() . '">' . $now_time . '</div>';
                $result .= '<div id="result_auction_info_' . $auction->getId() . '">' . $this->_getAuctionInfo($auction, $lastBid) . '</div>';
                $result .= '<div id="result_price_condition_' . $auction->getId() . '">' . $this->_getPriceAuction($auction, $lastBid) . '</div>';
                $result .= '<div id="result_current_bid_id_' . $auction->getId() . '">' . $lastBid->getId() . '</div>';

                $auctionbid->setAuctioninfo($result);

                $result = '<div id="result_product_id">' . $auction->getProductId() . '</div>';
                $result .= '<div id="result_auction_info_' . $auction->getProductId() . '">' . $this->_getAuctionInfo($auction, $lastBid, 'auctionlistinfo') . '</div>';

                $auctionbid->setAuctionlistinfo($result)->save();
                if ($baseCurrency->getCode() != $currCurrency->getCode()) {
                    $store->setCurrentCurrencyCode($currCurrency->getCode());
                    $store->setData('current_currency', $currCurrency);
                }
                $check = true;

                if ($check && $check == true) {
                    Mage::getModel('auction/event')->autobid($auction->getProductauctionId());
                    if ($bidType) {
                        $lastAuctionBid = $auction->getLastBid();
                        if ($lastAuctionBid->getId() == $auctionbid->getId()) {
                            Mage::getModel('auction/event')->autobid($auction->getProductauctionId());
                        }
                    }
                }

                $information = Mage::getModel('connector/catalog_product')->getDetail($data_r);
                $this->_printDataJson($information);
                return;

            } catch (Exception $e) {
                $result .= $e->getMessage();
                $information = $modelAuction->statusError();
                $information['message'] = array($result);
                $this->_printDataJson($information);
                return;
            }
        } else {
            $autobid = Mage::getModel('auction/autobid')->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addFieldToFilter('productauction_id', $auction->getId())
                ->addFieldToFilter('price', array('gt' => $auction->getMinNextPrice()))
                ->getFirstItem();
            $check_autobid = Mage::getStoreConfig('auction/general/auto_bid');

            // check allow customer config change autobid price for multiple times.
            if ($check_autobid == 1) {
                $data['created_time'] = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->timestamp(time()));
                $autobid->setData($data)
                    ->setStoreId($store_id);
                try {
                    $autobid->save();
                    $autobid->emailToBidder();
                    $check = true;

                    $result .= $_helper->__('You have placed an auto bid successfully.');
                } catch (Exception $e) {
                    $information = $modelAuction->statusError();
                    $information['message'] = array($result);
                    $this->_printDataJson($information);
                    return;
                }
            } elseif ($check_autobid == 2 && !($autobid->getId())) {
                $data['created_time'] = date('Y-m-d H:i:s', Mage::getSingleton('core/date')->timestamp(time()));
                $autobid->setData($data)
                    ->setStoreId($store_id);

                try {
                    $autobid->save();
                    $autobid->emailToBidder();
                    $result .= $_helper->__('You have placed an auto bid successfully.');
                } catch (Exception $e) {
                    $result .= $e->getMessage();
                }
            } else {
                $result .= $_helper->__('You have already placed an auto bid for this auction.');

            }
            $information = $modelAuction->statusSuccess();
            $information['message'] = array($result);
            $this->_printDataJson($information);
            return;
        }
    }

    protected function _getAuctionInfo($auction, $lastBid = null, $tmpl = null) {
        $lastBid = $lastBid ? $lastBid : $auction->getLastBid();
        $tmpl = $tmpl ? $tmpl : 'auctioninfo';
        $auction->setLastBid($lastBid);
        $block = $this->getLayout()->createBlock('auction/auction');
        $block->setTemplate('auction/' . $tmpl . '.phtml');
        $block->setData('auction', $auction);

        return $block->toHtml();
    }

    protected function _getPriceAuction($auction, $lastBid = null) {
        $auction->setCurrentPrice(null)
            ->setMinNextPrice(null)
            ->setMaxNextPrice(null);

        $min_next_price = $auction->getMinNextPrice();
        $max_next_price = $auction->getMaxNextPrice();
        $max_condition = $max_next_price ? ' ' . Mage::helper('core')->__('to') . ' ' . Mage::helper('core')->currency($max_next_price) : '';
        if ($max_condition)
            $html = '(' . Mage::helper('core')->__('Enter an amount from') . ' ' . Mage::helper('core')->currency($min_next_price) . $max_condition . ')';
        else
            $html = '(' . Mage::helper('core')->__('Enter %s or more',Mage::helper('core')->currency($min_next_price)) . ')';

        return $html;
    }

    public function get_my_bidsAction(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $modelAuction = Mage::getModel('connector/abstract');
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__("Please login"));
            $this->_printDataJson($information);
            return;
        }

        Mage::helper('auction')->updateAuctionStatus();
        $information = Mage::getModel('connector/auction_customize')->getBidsForCustomer();
        $this->_printDataJson($information);
    }

    public function get_my_auto_bidsAction(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $modelAuction = Mage::getModel('connector/abstract');
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__("Please login"));
            $this->_printDataJson($information);
            return;
        }

        Mage::helper('auction')->updateAuctionStatus();
        $information = Mage::getModel('connector/auction_customize')->getAutoBidsForCustomer();
        $this->_printDataJson($information);
    }

    public function get_my_watched_auctionsAction(){
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $modelAuction = Mage::getModel('connector/abstract');
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__("Please login"));
            $this->_printDataJson($information);
            return;
        }
        $information = Mage::getModel('connector/auction_customize')->getWatchedAuctionsForCustomer();
        $this->_printDataJson($information);
    }

    public function save_emailAction() {
        $param = $this->getData();
        $data = array();
        if (!isset($param->place_bid)) {
            $param->place_bid = '0';
        }
        if (!isset($param->place_autobid)) {
            $param->place_autobid = '0';
        }
        if (!isset($param->overbid)) {
            $param->overbid = '0';
        }
        if (!isset($param->overautobid)) {
            $param->overautobid = '0';
        }
        if (!isset($param->cancel_bid)) {
            $param->cancel_bid = '0';
        }
        if (!isset($param->highest_bid)) {
            $param->highest_bid = '0';
        }
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $param->customer_id = $customerId;

        $data['place_bid'] = $param->place_bid;
        $data['place_autobid'] = $param->place_autobid;
        $data['overbid'] = $param->overbid;
        $data['overautobid'] = $param->overautobid;
        $data['cancel_bid'] = $param->cancel_bid;
        $data['highest_bid'] = $param->highest_bid;
        $data['customer_id'] = $param->customer_id;

        $modelAuction = Mage::getModel('connector/abstract');
        $model = Mage::getModel('auction/email');
        $id = $model->getCollection()->addFieldToFilter('customer_id', $customerId)->getFirstItem()->getId();
        try {
            $model->setData($data)->setId($id)->save();
            Mage::getSingleton('core/session')->addSuccess();
            $information = $modelAuction->statusSuccess();
            $information['message'] = array(Mage::helper('auction')->__('Your email settings have been saved successfully.'));
            $this->_printDataJson($information);
            return;
        } catch (Exception $e) {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('An error has occurred when saving your email settings.'));
            $this->_printDataJson($information);
            return;
        }
    }

    public function save_bidder_nameAction() {
        $data = $this->getData();
        $bidder_name = $data->bidder_name;
        $modelAuction = Mage::getModel('connector/abstract');
        if($bidder_name==''){
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Please enter your bidder name!'));
            $this->_printDataJson($information);
            return;
        }
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('bidder_name', $bidder_name);

        if (!count($collection)&&$bidder_name!='') {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            try {
                $customer->setBidderName($bidder_name)
                    ->save();
                $information = $modelAuction->statusSuccess();
                $information['message'] = array(Mage::helper('auction')->__('Your bidder name has been successfully created.'));
                $this->_printDataJson($information);
                return;
                return;
            } catch (Exception $e) {
                $information = $modelAuction->statusError();
                $information['message'] = array($e->getMessage());
                $this->_printDataJson($information);
                return;
            }
        } else {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Bidder name already exists!'));
            $this->_printDataJson($information);
            return;
        }
    }

    public function view_bidsAction(){
        $data = $this->getData();
        $auction_id = $data->id;
        $curr_page = $data->curr_page;

        $modelAuction = Mage::getModel('connector/abstract');

        Mage::helper('auction')->updateAuctionStatus($auction_id);

        if (!$auction_id) {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Has some errors!'));
            $this->_printDataJson($information);
            return;
        }

        Mage::helper('auction')->updateAuctionStatus();
        $information = Mage::getModel('connector/auction_customize')->getBidsHistoryForCustomer($auction_id, $curr_page);
        $this->_printDataJson($information);
    }

    public function change_watcherAction(){
        $result = null;
        $data = $this->getData();
        $productId = $data->product_id;
        $isWatcher = $data->is_watcher;
        $modelAuction = Mage::getModel('connector/abstract');
        //check login
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Please login!'));
            $this->_printDataJson($information);
            return;
        }

        $auction = Mage::getModel('auction/productauction')->loadAuctionByProductId($productId);
        if ($auction && $auction->getId()) {
            $product = Mage::getModel('catalog/product')->load($auction->getProductId());
        } else {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Has some problems!'));
            $this->_printDataJson($information);
            return;
        }
        if ($auction->getStatus() == 5) { //complete auction
            $information = $modelAuction->statusSuccess();
            $this->_printDataJson($information);
            return;
        }

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $model = Mage::getModel('auction/watcher')->getCollection()
            ->addFieldToFilter('productauction_id', $auction->getId())
            ->addFieldToFilter('customer_id', $customer->getId())
            ->getFirstItem();

        $storeId = Mage::app()->getStore()->getId();

        if ($model->getId()) {
            $model->setStatus($isWatcher);
        } else {
            $model->setProductauctionId($auction->getId())
                ->setCustomerId($customer->getId())
                ->setCustomerName($customer->getName())
                ->setCustomerEmail($customer->getEmail())
                ->setStoreId($storeId)
                ->setStatus($isWatcher);
        }

        try {
            $model->setCreatedTime(Mage::getSingleton('core/date')->timestamp(time()))
                ->save();

            $information = $modelAuction->statusSuccess();
            $this->_printDataJson($information);
            return;
        } catch (Exception $e) {
            $information['message'] = array($e->getMessage());
            $this->_printDataJson($information);
            return;
        }
    }

    public function checkoutAction() {
        $data = $this->getData();
        $product_id = $data->product_id;
        $modelAuction = Mage::getModel('connector/abstract');
        if (!$product_id) {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Has some errors.'));
            $this->_printDataJson($information);
            return;
        }

        $product = Mage::getModel('catalog/product')->load($product_id);

        $bid_id = Mage::getModel('connector/auction_customize')->getBidToCheckout($product);
        $bid = Mage::getModel('auction/auction')->load($bid_id);

        //check authentication
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer || ($customer->getId() != $bid->getCustomerId())) {
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Has some errors.'));
            $this->_printDataJson($information);
            return;
        }

        if ($bid->getStatus() == 6) { //complete bid
            $information = $modelAuction->statusError();
            $information['message'] = array(Mage::helper('auction')->__('Has some errors.'));
            $this->_printDataJson($information);
            return;
        }


        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->getId() || $quote->getId() <= 0) {
            $quote = Mage::getModel('sales/quote')->assignCustomer(Mage::getModel('customer/customer')->load($customer->getId()));
            $quote->setStoreId(Mage::app()->getStore()->getStoreId());
        } else {
            $items = $quote->getAllItems();
            foreach ($items as $item) {
                $bidId = $item->getOptionByCode('bid_id');
                if ($bidId != null && $bidId->getValue() > 0) {
                    if ($bidId->getValue() == $bid_id) {
                        $information = $modelAuction->statusError();
                        $information['message'] = array(Mage::helper('auction')->__('You cannot update the quantity of autioned product(s).'));
                        $this->_printDataJson($information);
                        return;
                    }
                }
            }
        }
        try {
            $quoteItem = Mage::getModel('sales/quote_item')->setProduct($product);
            $quoteItem->setCustomPrice($bid->getPrice());
            $quoteItem->setOriginalCustomPrice($bid->getPrice());
            $quoteItem->addOption(array(
                'product_id' => $product->getId(),
                'product' => $product,
                'label' => 'Auction',
                'code' => 'bid_id',
                'value' => $bid_id,
            ));
            $quoteItem->setQty(1);
            $quoteItem->getProduct()->setIsSuperMode(true);
            Mage::getSingleton('core/session')->setData('checkout_auction', true);
            $quote->addItem($quoteItem);
            $quote->collectTotals();
            $quote->save();

            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
            $information = $modelAuction->statusSuccess();
            $information['message'] = array(Mage::helper('checkout')->__('The auctioned product %s has been added to cart successfully at your winning price.', $product->getName()));
            $this->_printDataJson($information);
            return;
        } catch (Exception $e) {
            $information = $modelAuction->statusError(array($e->getMessage()));
            $this->_printDataJson($information);
            return;
        }
    }
}

