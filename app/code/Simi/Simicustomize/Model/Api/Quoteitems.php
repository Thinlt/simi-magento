<?php

namespace Simi\Simicustomize\Model\Api;

class Quoteitems extends \Simi\Simiconnector\Model\Api\Apiabstract
{

    public $DEFAULT_ORDER = 'item_id';
    public $RETURN_MESSAGE;
    public $removed_items;
    public $detail_list;
    public $estimateShipping;
    public $estimateAddress;

    public function _getSession()
    {
        return $this->simiObjectManager->create('Magento\Checkout\Model\Session');
    }

    public function _getCart()
    {
        return $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
    }

    public function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    public function setBuilderQuery()
    {
        $data = $this->getData();
        $quote              = $this->_getQuote();
//        $this->estimateShipping();
        if (isset($data['resourceid']) &&
            $data['resourceid'] && isset($data['params']) &&
            isset($data['params']['move_to_wishlist']) &&
            $data['params']['move_to_wishlist'] &&
            $this->simiObjectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            $this->moveToWishlist($data['resourceid']);
            $this->removed_items = [$data['resourceid']];
            $this->RETURN_MESSAGE = __('Item has been moved to Wishlist');
        }

        //buy service
        if (isset($data['resourceid']) &&
            $data['resourceid'] && isset($data['params']) &&
            (
                isset($data['params']['add_buy_service']) ||
                isset($data['params']['remove_buy_service'])
            )
        ) {
            if (isset($data['params']['add_buy_service'])) {
                $this->_addBuyService($data['resourceid']);
                $this->RETURN_MESSAGE = __('Item has been added Service');
            } else {
                $this->_removeBuyService($data['resourceid']);
                $this->RETURN_MESSAGE = __('Item has been removed Service');
            }
        }

        $this->builderQuery = $quote->getItemsCollection();
    }

    /*
     * Change Qty, Add/remove Coupon Code
     */

    public function update()
    {
        $data = $this->getData();
        if ($data && isset($data['params']['subproductsku'])) {
            $this->updateSubProductSpecialItem();
            return $this->index();
        }

        $data       = $this->getData();
        $parameters = (array) $data['contents'];
        if (isset($parameters['coupon_code'])) {
            $this->RETURN_MESSAGE = $this->simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Coupon')->setCoupon($parameters['coupon_code']);
        }
        $this->_updateItems($parameters);
        return $this->index();
    }

    private function _updateItems($parameters)
    {
        $cartData = [];
        foreach ($parameters as $index => $qty) {
            $cartData[$index] = ['qty' => $qty];
        }
        if ($this->simiObjectManager->get('Simi\Simiconnector\Helper\Data')->countArray($cartData)) {
            $filter       = $this->simiObjectManager
                    ->create('\Magento\Framework\Filter\LocalizedToNormalized', ['locale' => $this->simiObjectManager
                    ->create('Magento\Framework\Locale\ResolverInterface')->getLocale()]);
            $removedItems = [];
            foreach ($cartData as $index => $data) {
                if (isset($data['qty'])) {
                    $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    if ($data['qty'] == 0) {
                        $removedItems[] = $index;
                    }
                }
            }
            $this->removed_items = $removedItems;
            $cart                 = $this->_getCart();
            if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                $cart->getQuote()->setCustomerId(null);
            }
            $cartData = $cart->suggestItemsQty($cartData);
            $cart->updateItems($cartData)->save();
            $this->_getSession()->setCartWasUpdated(true);
        }
    }

    /*
     * Add To Cart
     */

    public function store()
    {
        $this->addToCart();
        return $this->index();
    }

    public function addToCart()
    {
        $data = $this->getData();
        $cart = $this->_getCart();

        $controller = $data['controller'];

        /*
         * The same with param parsing on Simi\Simiconnector\Model\Server, but to Array instead
         */
        $params = isset($data['contents_array'])?$data['contents_array']:array();
        $params = isset($params)?$this->convertParams($params):array();
        if (isset($params['qty'])) {
            $filter        = $this->simiObjectManager
                    ->create('\Magento\Framework\Filter\LocalizedToNormalized', ['locale' => $this->simiObjectManager
                    ->create('Magento\Framework\Locale\ResolverInterface')->getLocale()]);
            $params['qty'] = $filter->filter($params['qty']);
        }

        $product               = $this->_initProduct($params['product']);

        $this->_prepareSpecialProduct($product, $params);

        $cart->addProduct($product, $params);

        $this->_getSession()->setCartWasUpdated(true);
        $this->eventManager->dispatch(
            'checkout_cart_add_product_complete',
            ['product' => $product, 'request' => $controller->getRequest(),
            'response' => $controller->getResponse()]
        );
        $this->RETURN_MESSAGE = __('You added %1 to your shopping cart.', $product->getName());
    }

    public function convertParams($params)
    {
        $convertList = [
            //Custom Option (Simple/Virtual/Downloadable)
            'options',
            //Configurable Product
            'super_attribute',
            //Group Product
            'super_group',
            //Bundle Product
            'bundle_option',
            //Bundle Product Qty
            'bundle_option_qty',
        ];
        foreach ($convertList as $type) {
            if (!isset($params[$type])) {
                continue;
            }
            $params[$type]  = (array) $params[$type];
            $convertedParam = [];
            foreach ($params[$type] as $index => $item) {
                $convertedParam[(int) $index] = $item;
            }
            $params[$type] = $convertedParam;
        }
        return $params;
    }

    public function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->simiObjectManager
                    ->create('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            return $this->simiObjectManager
                    ->create('Magento\Catalog\Api\ProductRepositoryInterface')->getById($productId, false, $storeId);
        }
        return false;
    }

    /*
     * Return Cart Detail
     */

    public function show()
    {
        return $this->index();
    }

    public function index()
    {
        $this->estimateShipping();
        $this->_getQuote()->collectTotals()->save();
        $collection = $this->builderQuery;
        $collection->addFieldToFilter('item_id', ['nin' => $this->removed_items])
                ->addFieldToFilter('parent_item_id', ['null' => true]);

        $this->filter();
        $data       = $this->getData();
        $parameters = $data['params'];
        $page       = 1;
        
        $limit = self::DEFAULT_LIMIT;
        $offset = 0;
        $this->setPageSize($parameters, $limit, $offset, $collection, $page);
        $all_ids = [];
        $info    = [];
        $total   = $collection->getSize();
        if ($offset > $total) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Invalid method.'), 4);
        }

        $fields = [];
        if (isset($parameters['fields']) && $parameters['fields']) {
            $fields = explode(',', $parameters['fields']);
        }

        $check_limit  = 0;
        $check_offset = 0;

        /*
         * Add options and image
         */
        foreach ($collection as $entity) {
            if ((++$check_offset <= $offset) ||
                    ($entity->getData('parent_item_id') != null) ||
                    ($this->removed_items && in_array($entity->getData('item_id'), $this->removed_items))) {
                continue;
            }
            $options = [];
            switch ($entity->getProductType()) {
                case 'configurable':
                    $block   = $this->simiObjectManager
                        ->get('Magento\ConfigurableProduct\Block\Cart\Item\Renderer\Configurable');
                    $block->setItem($entity);
                    $options = $this->simiObjectManager
                            ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                    break;
                case 'bundle':
                    $block   = $this->simiObjectManager
                        ->get('Magento\Bundle\Block\Checkout\Cart\Item\Renderer');
                    $block->setItem($entity);
                    $options = $this->simiObjectManager
                            ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                    break;
                case 'downloadable':
                    $block   = $this->simiObjectManager
                        ->get('Magento\Downloadable\Block\Checkout\Cart\Item\Renderer');
                    $block->setItem($entity);
                    $options = $this->simiObjectManager
                            ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                    break;
                default:
                    $block   = $this->simiObjectManager->get('Magento\Checkout\Block\Cart\Item\Renderer');
                    $block->setItem($entity);
                    $options = $this->simiObjectManager
                            ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                    break;
            }

            $quoteitem           = $entity->toArray($fields);
            $quoteitem['option'] = $options;
            $quoteitem['image']  = $this->simiObjectManager
                    ->create('Simi\Simiconnector\Helper\Products')
                    ->getImageProduct(
                        $this->loadProductWithId($entity->getProduct()->getId()),
                        null,
                        $parameters['image_width'],
                        $parameters['image_height']
                    );

            $info[]              = $quoteitem;
            $all_ids[]           = $entity->getId();
        }
        $this->detail_list = $this->getList($info, $all_ids, $total, $limit, $offset);
        $this->eventManager->dispatch(
            'simi_simiconnector_model_api_quoteitems_index_after',
            ['object' => $this, 'data' => $this->detail_list]
        );
        return $this->detail_list;
    }
    
    private function setPageSize($parameters, &$limit, &$offset, $collection, &$page)
    {
        if (isset($parameters[self::PAGE]) && $parameters[self::PAGE]) {
            $page = $parameters[self::PAGE];
        }
        if (isset($parameters[self::LIMIT]) && $parameters[self::LIMIT]) {
            $limit = $parameters[self::LIMIT];
        }
        $offset = $limit * ($page - 1);
        if (isset($parameters[self::OFFSET]) && $parameters[self::OFFSET]) {
            $offset = $parameters[self::OFFSET];
        }
        $collection->setPageSize($offset + $limit);
    }


    /*
     * Move to Wishlist
     */
    public function moveToWishlist($itemId) {
        $customer = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer();
        if ($customer->getId() && ($customer->getId() != '')) {
            $wishlist = $this->simiObjectManager
                ->get('Magento\Wishlist\Model\Wishlist')->loadByCustomerId($customer->getId(), true);
        } else {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Please login first'), 4);
        }
        $cart = $this->_getCart();

        $item = $cart->getQuote()->getItemById($itemId);
        if (!$item) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Requested cart item doesn\'t exist'), 4);
        }
        $productId  = $item->getProductId();
        $buyRequest = $item->getBuyRequest();
        $wishlist->addNewItem($productId, $buyRequest);
        $productIds[] = $productId;
        $cart->getQuote()->removeItem($itemId);
        $cart->save();
        $this->simiObjectManager->get('Magento\Wishlist\Helper\Data')->calculate();
        $this->RETURN_MESSAGE = __("Item has been moved to wishlist");
        $wishlist->save();
    }

    /*
     * Add Message
     */

    public function getList($info, $all_ids, $total, $page_size, $from)
    {
        $result          = parent::getList($info, $all_ids, $total, $page_size, $from);
        $result['total'] = $this->simiObjectManager->get('Simi\Simiconnector\Helper\Total')->getTotal();
        
        if($this->estimateAddress && $this->estimateShipping) {
            $result['estimate_shipping'] = [
                'address' => $this->estimateAddress,
                'shipping_method' => $this->estimateShipping
            ];
        }
        
        if ($this->RETURN_MESSAGE) {
            $result['message'] = [$this->RETURN_MESSAGE];
        }
        $session              = $this->_getSession();
        $result['cart_total'] = $this->_getCart()->getItemsCount();
        $result['quote_id']   = $session->getQuoteId();
        
        $customerSession = $this->simiObjectManager->get('Magento\Customer\Model\Session');
        $result['customer_email'] = $customerSession->isLoggedIn()?
            $customerSession->getCustomer()->getEmail():
            null;

        return $result;
    }
    
    public function loadProductWithId($id)
    {
        $categoryModel    = $this->simiObjectManager
                ->create('Magento\Catalog\Model\Product')->load($id);
        return $categoryModel;
    }
    
    protected function estimateShipping() {
        $quote              = $this->_getQuote();
        
        $customerSession = $this->simiObjectManager->get('Magento\Customer\Model\Session');
        if ($quote->getItemsCount() > 0 && $customerSession->isLoggedIn()) {
            $defaultShippingId = $customerSession->getCustomer()->getDefaultShipping();
            $addressArray = [];
            foreach ($customerSession->getCustomer()->getAddresses() as $index => $address) {
                $addressArray[] = $index;
            }
            if(!in_array($defaultShippingId, $addressArray)) {
                if(count($addressArray) > 0) {
                    $defaultShippingId = $addressArray[0];
                } else {
                    $defaultShippingId = null;
                }
            }
            if($defaultShippingId) {
                $defaultShipping = $this->simiObjectManager
                ->create('Magento\Customer\Model\Address')->load($defaultShippingId);
                if (!$quote->getIsVirtual()) {
                    $quote->getShippingAddress()->addData($defaultShipping->toArray());
                    $quote->getShippingAddress()
                    ->setCollectShippingRates(true)
                    ->collectShippingRates();
                    $shippingRates = $quote->getShippingAddress()->getGroupedAllShippingRates();
                    $shippingMethod = null;
                    foreach ($shippingRates as $carrierRates) {
                        foreach ($carrierRates as $rate) {
                            $shippingMethod = $rate;
                            break;
                        }
                        if($shippingMethod) {
                            break;
                        }
                    }
                    if($shippingMethod) {
                        $this->estimateShipping = $shippingMethod->toArray();
                        $this->estimateAddress = $defaultShipping->toArray();
                        $quote->getShippingAddress()->setShippingMethod($shippingMethod->getCode());
                    }
                    $quote->save();
                }
                $quote->collectTotals();
            } else {
                $quote->getShippingAddress()->setShippingMethod(null);
            }
        }
    }

    /*Preorder handling*/

    protected $itemProcessor;
    private $requestInfoFilter;
    const PRE_ORDER_OPTION_TITLE = 'Pre-order Products';
    const TRY_TO_BUY_OPTION_TITLE = 'Try-to-buy Products';


    protected function _prepareSpecialProduct(&$product, &$param) {
        $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
        $tryToByProductId = $this->scopeConfig->getValue('sales/trytobuy/trytobuy_product_id');
        $isPreOrder = false;
        $isTryToBuy = false;
        if (isset($param['pre_order']) && $param['pre_order']) {
            $isPreOrder = true;
        }
        if (isset($param['try_to_buy']) && $param['try_to_buy']) {
            $isTryToBuy = true;
        }
        if (!$isPreOrder) {
            foreach ($this->builderQuery as $quoteItem) {
                if ($quoteItem->getProduct()->getId() == $depositProductId) {
                    throw new \Simi\Simiconnector\Helper\SimiException(__('Your cart contains Pre-order product, please complete it to continue.'), 4);
                }
            }
        } else {
            // report error when adding pre-order product with existing normal product in the cart
            $hasPreorderItem = false;
            foreach ($this->builderQuery as $quoteItem) {
                if ($quoteItem->getProduct()->getId() == $depositProductId) {
                    $hasPreorderItem = true;
                    break;
                }
            }
            if (!$hasPreorderItem && $this->builderQuery->getSize()) {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Pre-order products can not be added to the same cart with regular products. Please checkout with existing products in cart first.'), 4);
            }
        }

        if (!$isTryToBuy) {
            foreach ($this->builderQuery as $quoteItem) {
                if ($quoteItem->getProduct()->getId() == $tryToByProductId) {
                    throw new \Simi\Simiconnector\Helper\SimiException(__('Your cart contains Try-to-buy product, please complete it to continue.'), 4);
                }
            }
        }

        if (!$isTryToBuy && !$isPreOrder) {
            return;
        } else {
            if (!$this->_getQuote()->getData('customer_id')) {
                throw new \Simi\Simiconnector\Helper\SimiException(__('Please login to continue.'), 4);
            }
        }

        if (!$this->itemProcessor)
            $this->itemProcessor = $this->simiObjectManager->create('\Magento\Quote\Model\Quote\Item\Processor');

        //get child product sku from request options
        $sku = false;
        $request = $this->_getProductRequest($param);
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL;
        $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        $parentItem = null;
        $item = null;
        foreach ($cartCandidates as $candidate) {
            $stickWithinParent = $candidate->getParentProductId() ? $parentItem : null;
            $candidate->setStickWithinParent($stickWithinParent);
            $item = $this->itemProcessor->init($candidate, $request);
            $item->setProduct($candidate);
            $sku = $item->getData('sku');
            $requestOfNewItem = $param;
            $nameOfNewItem = $item->getProduct()->getData('name');
            if ($isPreOrder)
                unset($requestOfNewItem['pre_order']);
        }

        //if found sku, add deposit/try-to-buy instead of original product
        if ($sku) {
            $storeId = $this->simiObjectManager
                ->create('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            if ($isPreOrder) {
                $depositProduct = $this->simiObjectManager
                    ->create('Magento\Catalog\Api\ProductRepositoryInterface')->getById($depositProductId, false, $storeId);
                if ($depositProduct && $depositProduct->getId()) {
                    $app_options = $this->simiObjectManager
                        ->get('\Simi\Simiconnector\Helper\Options')->getOptions($depositProduct);
                    if ($app_options && isset($app_options['custom_options']) && is_array($app_options['custom_options'])) {
                        foreach ($app_options['custom_options'] as $custom_option) {
                            if (isset($custom_option['title']) && $custom_option['title'] === self::PRE_ORDER_OPTION_TITLE) {
                                $preOrderProducts = false;
                                //remove all cart items
                                $quoteItems = $this->_getQuote()->getItemsCollection();
                                foreach ($quoteItems as $quoteItem) {
                                    //cart already contains deposit product
                                    if ($quoteItem->getData('product_id') == $depositProductId) {
                                        $block = $this->simiObjectManager->get('Magento\Checkout\Block\Cart\Item\Renderer');
                                        $block->setItem($quoteItem);
                                        $selectedDepositOptions = $this->simiObjectManager
                                            ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                                        if ($selectedDepositOptions && is_array($selectedDepositOptions)) {
                                            foreach ($selectedDepositOptions as $selectedDepositOption) {
                                                if (isset($selectedDepositOption['option_title']) && $selectedDepositOption['option_title'] == self::PRE_ORDER_OPTION_TITLE) {
                                                    $preOrderProducts = json_decode(base64_decode($selectedDepositOption['option_value']), true);
                                                }
                                            }
                                        }
                                    }
                                    $this->_getCart()->removeItem($quoteItem->getId())->save();
                                }

                                /*
                                 * change request param to add deposit product instead of original product
                                 */
                                if (!$preOrderProducts)
                                    $preOrderProducts = array();
                                $updatedPreOrderProduct = false;
                                //if  quote already exist, update the option
                                foreach ($preOrderProducts as $preOrderPtIndex => $preOrderProduct) {
                                    if ($preOrderProduct['sku'] == $sku) {
                                        $preOrderProduct['quantity']++;
                                        $preOrderProducts[$preOrderPtIndex] = $preOrderProduct;
                                        $updatedPreOrderProduct = true;
                                    }
                                }
                                if (!$updatedPreOrderProduct)
                                    $preOrderProducts[] = array(
                                        'sku' => $sku,
                                        'quantity' => 1,
                                        'name' => $nameOfNewItem,
                                        'request' => $requestOfNewItem
                                    );
                                $optionString = base64_encode(json_encode($preOrderProducts));
                                $param['options'] = array($custom_option['id'] => $optionString);
                                $product = $depositProduct;
                                $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
                                $registry->register('simi_pre_order_option', $optionString);

                                break;
                            }
                        }
                    }
                }
            } else if ($isTryToBuy) {
                $trytobuyProduct = $this->simiObjectManager
                    ->create('Magento\Catalog\Api\ProductRepositoryInterface')->getById($tryToByProductId, false, $storeId);
                if ($trytobuyProduct && $trytobuyProduct->getId()) {
                    $trytobuy_app_options = $this->simiObjectManager
                        ->get('\Simi\Simiconnector\Helper\Options')->getOptions($trytobuyProduct);
                    if ($trytobuy_app_options && isset($trytobuy_app_options['custom_options']) && is_array($trytobuy_app_options['custom_options'])) {
                        foreach ($trytobuy_app_options['custom_options'] as $trytobuy_custom_options) {
                            if (isset($trytobuy_custom_options['title']) && $trytobuy_custom_options['title'] === self::TRY_TO_BUY_OPTION_TITLE) {
                                $tryToBuyProducts = false;
                                //remove all cart items
                                $quoteItems = $this->_getQuote()->getItemsCollection();
                                foreach ($quoteItems as $quoteItem) {
                                    //cart already contains trytobuy product
                                    if ($quoteItem->getData('product_id') == $tryToByProductId) {
                                        $block = $this->simiObjectManager->get('Magento\Checkout\Block\Cart\Item\Renderer');
                                        $block->setItem($quoteItem);
                                        $selectedTryToBuyOptions = $this->simiObjectManager
                                            ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                                        if ($selectedTryToBuyOptions && is_array($selectedTryToBuyOptions)) {
                                            foreach ($selectedTryToBuyOptions as $selectedTryToBuyOption) {
                                                if (isset($selectedTryToBuyOption['option_title']) && $selectedTryToBuyOption['option_title'] == self::TRY_TO_BUY_OPTION_TITLE) {
                                                    $tryToBuyProducts = json_decode(base64_decode($selectedTryToBuyOption['option_value']), true);
                                                }
                                            }
                                        }
                                    }
                                    $this->_getCart()->removeItem($quoteItem->getId())->save();
                                }

                                /*
                                 * change request param to add deposit product instead of original product
                                 */
                                if (!$tryToBuyProducts)
                                    $tryToBuyProducts = array();
                                $updatedTryToBuyProduct = false;
                                //if  quote already exist, update the option
                                foreach ($tryToBuyProducts as $tryToBuyPtIndex => $tryToBuyProduct) {
                                    if ($tryToBuyProduct['sku'] == $sku) {
                                        $updatedTryToBuyProduct = true;
                                    }
                                }
                                if (count($tryToBuyProducts) >= 3) {
                                    throw new \Simi\Simiconnector\Helper\SimiException(__('You have already tried to by 3 products'), 4);
                                }
                                if (!$updatedTryToBuyProduct)
                                    $tryToBuyProducts[] = array(
                                        'sku' => $sku,
                                        'quantity' => 1,
                                        'name' => $nameOfNewItem,
                                        'request' => $requestOfNewItem
                                    );
                                $optionString = base64_encode(json_encode($tryToBuyProducts));
                                $param['options'] = array($trytobuy_custom_options['id'] => $optionString);
                                $product = $trytobuyProduct;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    private function getRequestInfoFilter()
    {
        if ($this->requestInfoFilter === null) {
            $this->requestInfoFilter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Checkout\Model\Cart\RequestInfoFilterInterface::class);
        }
        return $this->requestInfoFilter;
    }

    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof \Magento\Framework\DataObject) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new \Magento\Framework\DataObject(['qty' => $requestInfo]);
        } elseif (is_array($requestInfo)) {
            $request = new \Magento\Framework\DataObject($requestInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        $this->getRequestInfoFilter()->filter($request);
        return $request;
    }

    /*
     * Trytobuy-preorder product remove subproducts
     */
    public function updateSubProductSpecialItem()
    {
        $data = $this->getData();
        $cart = $this->_getCart();
        $controller = $data['controller'];

        if ($data && isset($data['params']['subproductsku']) &&
            isset($data['params']['newquantity']) && isset($data['resourceid'])) {
            $depositProductId = $this->scopeConfig->getValue('sales/preorder/deposit_product_id');
            $tryToByProductId = $this->scopeConfig->getValue('sales/trytobuy/trytobuy_product_id');
            $isPreOrder = false;
            $quoteItems = null;
            $currentQuoteItems = $this->_getQuote()->getItemsCollection();
            foreach ($currentQuoteItems as $currentQuoteItem) {
                if ($currentQuoteItem->getId() == $data['resourceid']) {
                    $quoteItem = $currentQuoteItem;
                }
            }
            if ($quoteItem && $quoteItem->getId()) {
                $newquantity = $data['params']['newquantity'];
                $productId = $quoteItem->getData('product_id');
                $optionTitle = self::TRY_TO_BUY_OPTION_TITLE;
                if ($productId == $depositProductId) {
                    $optionTitle = self::PRE_ORDER_OPTION_TITLE;
                    $isPreOrder = true;
                } else if ($productId == $tryToByProductId) {
                    if ($newquantity != 0 && $newquantity > 1) {
                        throw new \Simi\Simiconnector\Helper\SimiException(__('Cannot try to buy with quantity over 2'), 4);
                    }
                }

                $block = $this->simiObjectManager->get('Magento\Checkout\Block\Cart\Item\Renderer');
                $block->setItem($quoteItem);
                $selectedOptions = $this->simiObjectManager
                    ->get('Simi\Simiconnector\Helper\Checkout')->convertOptionsCart($block->getOptionList());
                $subProducts = null;
                if ($selectedOptions && is_array($selectedOptions)) {
                    foreach ($selectedOptions as $selectedOption) {
                        if (isset($selectedOption['option_title']) && $selectedOption['option_title'] == $optionTitle) {
                            $subProducts = json_decode(base64_decode($selectedOption['option_value']), true);
                        }
                    }
                }
                if ($subProducts) {
                    $specialProduct = $this->simiObjectManager
                        ->create('Magento\Catalog\Api\ProductRepositoryInterface')->getById($productId, false);
                    $specialProductOptions = $this->simiObjectManager
                        ->get('\Simi\Simiconnector\Helper\Options')->getOptions($specialProduct);
                    if ($specialProductOptions && isset($specialProductOptions['custom_options']) && is_array($specialProductOptions['custom_options'])) {
                        foreach ($specialProductOptions['custom_options'] as $specialCustomOption) {
                            if (isset($specialCustomOption['title']) && $specialCustomOption['title'] === $optionTitle) {
                                $newSubproducts = array();
                                $willUpdate = false;
                                foreach ($subProducts as $subProduct) {
                                    if ($subProduct['sku'] == $data['params']['subproductsku']) {
                                        $willUpdate = true;
                                        if ($newquantity == 0)
                                            continue;
                                        else {
                                            $subProduct['quantity'] = (int)$newquantity;
                                        }
                                    }
                                    $newSubproducts[] = $subProduct;
                                }
                                if ($willUpdate) {
                                    $cart->getQuote()->removeItem($quoteItem->getId())->save();
                                    if (count($newSubproducts)) {
                                        $product = $this->_initProduct($productId);
                                        $param = array();
                                        $optionString = base64_encode(json_encode($newSubproducts));
                                        $param['options'] = array($specialCustomOption['id'] => $optionString);
                                        if ($isPreOrder) {
                                            $registry = $this->simiObjectManager->get('\Magento\Framework\Registry');
                                            $registry->register('simi_pre_order_option', $optionString);
                                        }

                                        $cart->addProduct($product, $param);

                                        $this->_getSession()->setCartWasUpdated(true);
                                        $this->eventManager->dispatch(
                                            'checkout_cart_add_product_complete',
                                            ['product' => $product, 'request' => $controller->getRequest(),
                                                'response' => $controller->getResponse()]
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    /*
     * Buy Service
     */
    protected function _addBuyService($itemId) {
        $cart = $this->_getCart();
        $item = $cart->getQuote()->getItemById($itemId);
        if (!$item || !$item->getId()) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Requested cart item doesn\'t exist'), 4);
        }
        $item->setIsBuyService(1)->save();
        //die('!!!');
    }

    protected function _removeBuyService($itemId) {
        $cart = $this->_getCart();
        $item = $cart->getQuote()->getItemById($itemId);
        if (!$item || !$item->getId()) {
            throw new \Simi\Simiconnector\Helper\SimiException(__('Requested cart item doesn\'t exist'), 4);
        }
        $item->setData('is_buy_service', 0)->save();
    }

}
