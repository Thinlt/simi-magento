<?php

/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simicustomize\Model\Api;
//use Magento\Framework\Controller\ResultFactory;

class Wishlistitems extends \Simi\Simiconnector\Model\Api\Wishlistitems
{

	public $DEFAULT_ORDER = 'wishlist_item_id';
	public $RETURN_MESSAGE;
	public $RETURN_URL;
	public $WISHLIST;
	const DEFAULT_LIMIT = 999;
	protected $_resultFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\ObjectManagerInterface $simiObjectManager

	) {
		$this->_resultFactory = $context->getResultFactory();
		$this->simiObjectManager = $simiObjectManager;
		$this->scopeConfig = $this->simiObjectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
		$this->storeManager = $this->simiObjectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$this->storeRepository = $this->simiObjectManager->get('\Magento\Store\Api\StoreRepositoryInterface');
		$this->storeCookieManager = $this->simiObjectManager->get('\Magento\Store\Api\StoreCookieManagerInterface');
		$this->resource = $this->simiObjectManager->get('\Magento\Framework\App\ResourceConnection');
		$this->eventManager = $this->simiObjectManager->get('\Magento\Framework\Event\ManagerInterface');
		return $this;
	}

	public function setBuilderQuery()
	{
		$data     = $this->getData();
		$customer = $this->simiObjectManager->get('Magento\Customer\Model\Session')->getCustomer();
		$code = false;
		if (isset($data['params']) && isset($data['params']['code']))
			$code = $data['params']['code'];
		if ($code !== false ||
		    ($customer->getId() && ($customer->getId() != ''))) {
			if ($code !== false)
				$this->WISHLIST = $this->simiObjectManager
					->get('Magento\Wishlist\Model\Wishlist')->loadByCode($code);
			else
				$this->WISHLIST = $this->simiObjectManager
					->get('Magento\Wishlist\Model\Wishlist')->loadByCustomerId($customer->getId(), true);
			//check if not shared
			if (!$this->WISHLIST->getShared()) {
				$this->WISHLIST->setShared('1');
				$this->WISHLIST->save();
			}
			$sharingCode           = $this->WISHLIST->getSharingCode();
			$this->RETURN_MESSAGE = $this->getStoreConfig('simiconnector/wishlist/sharing_message') . ' '
			                        . $this->simiObjectManager->get('Magento\Framework\UrlInterface')
			                                                  ->getUrl('wishlist/shared/index', ['code' => $sharingCode]);
			$this->RETURN_URL     = $this->simiObjectManager->get('Magento\Framework\UrlInterface')
			                                                ->getUrl('wishlist/shared/index', ['code' => $sharingCode]);
		} else {
			throw new \Simi\Simiconnector\Helper\SimiException(__('Please login First.', 4));
		}
		if ($data['resourceid']) {
			if ($data['resourceid'] == 'add_all_tocart') {
				$this->addAllWishlistItemsToCart();
			} else if ($data['params']['add_to_cart']) {
				$this->addWishlistItemToCart($data['resourceid']);
			}

			$this->builderQuery = $this->WISHLIST->getItemCollection();
		} else {
			$this->builderQuery = $this->WISHLIST->getItemCollection();
		}
	}

	public function index()
	{
		$data                 = $this->getData();
		$parameters           = $data['params'];
		$result        = parent::index();
		$addition_info = [];
		foreach ($this->builderQuery as $itemModel) {
			$product    = $itemModel->getProduct();
			$isSaleAble = $product->isSaleable();
			if ($isSaleAble) {
				$itemOptions = $this->simiObjectManager->get('Magento\Wishlist\Model\Item\Option')->getCollection()
				                                       ->addItemFilter([$itemModel->getData('wishlist_item_id')]);
				foreach ($itemOptions as $itemOption) {
					$optionProduct = $this->loadProductWithId($itemOption->getProductId());
					if (!$optionProduct->isSaleable()) {
						$isSaleAble = false;
						break;
					}
				}
			}

			$productSharingMessage = implode(
				' ',
				[$this->getStoreConfig('simiconnector/wishlist/product_sharing_message'),
					$product->getProductUrl()]
			);
			$options               = $this->simiObjectManager
				->get('\Simi\Simiconnector\Helper\Wishlist')->getOptionsSelectedFromItem($itemModel, $product);

			$product = $this->loadProductWithId($product->getId());
			if (isset($parameters['image_width'])) {
				$width  = $parameters['image_width'];
				$height = $parameters['image_height'];
			} else {
				$width  = $height = 200;
			}
			$vendor_id = $product->getData('vendor_id');
            $vendorName = '';
            if ($vendor_id) {
	            if (class_exists('Vnecoms\Vendors\Model\Vendor')) {
	                $vendor = \Magento\Framework\App\ObjectManager::getInstance()
	                    ->get(\Vnecoms\Vendors\Model\Vendor::class)
	                    ->load($vendor_id);
	                if ($vendorId = $vendor->getId()) {
	                    // productExtraData
	                    $vendorHelper = \Magento\Framework\App\ObjectManager::getInstance()
	                        ->get(\Simi\Simicustomize\Helper\Vendor::class);
	                    $profile = $vendorHelper->getProfile($vendorId);
	                    $vendorName =
	                        ($profile && isset($profile['store_name']) && $profile['store_name']) ? $profile['store_name'] : $vendor->getName();
	                }
	            }
	        }

            $addition_info[$itemModel->getData('wishlist_item_id')] = [
                'type_id'                       => $product->getTypeId(),
                'product_regular_price'         => $product->getPrice(),
                'product_price'                 => $product->getFinalPrice(),
                'vendor_id'                 => $vendor_id,
                'vendor_name'                 => $vendorName,
				'stock_status'                  => $isSaleAble,
				'product_image'                 => $this->simiObjectManager
					->get('\Simi\Simiconnector\Helper\Products')->getImageProduct($product, null, $width, $height),
				'is_show_price'                 => true,
				'options'                       => $options,
				'selected_all_required_options' => $this->simiObjectManager
					->get('\Simi\Simiconnector\Helper\Wishlist')
					->checkIfSelectedAllRequiredOptions($itemModel, $options),
				'product_sharing_message'       => $productSharingMessage,
				'product_sharing_url'           => $product->getProductUrl(),
				'product_url_key'               => $product->getData('url_key'),
				'product_sku'                   => $product->getSku(),
				'app_prices'                    => (isset($parameters['no_price']) && $parameters['no_price']) ?
					array():
					$this->simiObjectManager->get('\Simi\Simiconnector\Helper\Price')
					                        ->formatPriceFromProduct($product, true),
			];
		}
		foreach ($result['wishlistitems'] as $index => $item) {
			$result['wishlistitems'][$index] = array_merge($item, $addition_info[$item['wishlist_item_id']]);
		}
		return $result;
	}

	/*
	 * Add To Wishlist
	 */

	public function store()
	{
		$data = $this->getData();
		$params = (array) $data['contents'];
		if (isset($params['share_data']) && $params['share_data']){
			return $this->_shareWishlist();
		}else{
			$params             = $this->simiObjectManager
				->get( '\Simi\Simiconnector\Model\Api\Quoteitems' )->convertParams( (array) $data['contents'] );
			$product            = $this->simiObjectManager->create( 'Magento\Catalog\Model\Product' )->load( ( $params['product'] ) );
			$buyRequest         = $this->simiObjectManager->create( '\Magento\Framework\DataObject', [ 'data' => $params ] );
			/** fix for wishlist add Giftcard */
	        if($product->getTypeId() == 'aw_giftcard'){
	            $amounts = $product->getTypeInstance()->getAmountOptions($product);
	            if (!empty($amounts)) {
	                $value = array_pop($amounts);
	                $buyRequest['aw_gc_amount'] = $value;
	            } elseif($product->getData('aw_gc_allow_open_amount') && $product->getData('aw_gc_open_amount_max')) {
	                $buyRequest['aw_gc_amount'] = $product->getData('aw_gc_open_amount_max');
	            }
	        }
			$this->builderQuery = $this->WISHLIST->addNewItem( $product, $buyRequest );
			return $this->show();
		}
	}

	public function _shareWishlist() {
		$data = $this->getData();
		$contents_array = $data['contents_array'];
		$share_data = $contents_array['share_data'];

		$_wishlistConfig  = $this->simiObjectManager->get( '\Magento\Wishlist\Model\Config' );
		$wishlistProvider = $this->simiObjectManager->get( '\Magento\Wishlist\Controller\WishlistProviderInterface' );
		$escaper = $this->simiObjectManager->get('\Magento\Framework\Escaper');

		$wishlist = $wishlistProvider->getWishlist();
		if ( ! $wishlist ) {
			throw new NotFoundException( __( 'Page not found.' ) );
		}

		$sharingLimit = $_wishlistConfig->getSharingEmailLimit();
		$textLimit    = $_wishlistConfig->getSharingTextLimit();
		$emailsLeft   = $sharingLimit - $wishlist->getShared();

		$emails = $share_data['emails'];
		$emails = empty($emails) ? $emails : explode(',', $emails);

		$error = false;
		$message = $share_data['message'];
		if (strlen($message) > $textLimit) {
			$error = __('Message length must not exceed %1 symbols', $textLimit);
		} else {
			$message = nl2br($escaper->escapeHtml($message));
			if (empty($emails)) {
				$error = __('Please enter an email address.');
			} else {
				if (count($emails) > $emailsLeft) {
					$error = __('This wish list can be shared %1 more times.', $emailsLeft);
				} else {
					foreach ($emails as $index => $email) {
						$email = trim($email);
						if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
							$error = __('Please enter a valid email address.');
							break;
						}
						$emails[$index] = $email;
					}
				}
			}
		}

		if ($error) {
			throw new \Simi\Simiconnector\Helper\SimiException($error, 4);
		}

		$resultLayout = $this->_resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_LAYOUT);
		$resultLayout->addHandle('wishlist_email_items');
		$this->simiObjectManager->get('\Magento\Framework\Translate\Inline\StateInterface')->suspend();

		$sent = 0;

		try {
			$customerObj = $this->simiObjectManager->get('Magento\Customer\Model\Session');
			$customer = $customerObj->getCustomerDataObject();
			$customerName = $this->simiObjectManager->get('\Magento\Customer\Helper\View')->getCustomerName($customer);

			/*var_dump(get_class_methods($resultLayout->getLayout()->createBlock('\Magento\Wishlist\Block\Rss\EmailLink')->setTemplate('Magento_Wishlist::rss/email.phtml'))); die;
			$message .= $resultLayout->getLayout()->createBlock('\Magento\Wishlist\Block\Rss\EmailLink')
			                         ->setWishlistId($wishlist->getId())
			                         ->toHtml();*/

			$emails = array_unique($emails);
			$sharingCode = $wishlist->getSharingCode();
			/*var_dump($resultLayout->getLayout()->createBlock('Magento\Wishlist\Block\Share\Email\Items')->setTemplate('Magento_Wishlist::email/items.phtml')->toHtml());*/

			try {
				$storeBase = $this->getStoreConfig('simiconnector/general/pwa_studio_url');
				foreach ($emails as $email) {
					$transport = $this->simiObjectManager->get('\Magento\Framework\Mail\Template\TransportBuilder')->setTemplateIdentifier(
						$this->scopeConfig->getValue(
							'wishlist/email/email_template',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						)
					)->setTemplateOptions(
						[
							'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
							'store' => $this->storeManager->getStore()->getStoreId(),
						]
					)->setTemplateVars(
						[
							'customer' => $customer,
							'customerName' => $customerName,
							'salable' => $wishlist->isSalable() ? 'yes' : '',
							'items' => '',//$resultLayout->getLayout()->createBlock('Magento\Wishlist\Block\Share\Email\Items')->setTemplate('Magento_Wishlist::email/items.phtml')->toHtml(),
							'viewOnSiteLink' => $storeBase . 'sharedwishlist.html?code=' . $sharingCode,
							'message' => $message,
							'store' => $this->storeManager->getStore(),
						]
					)->setFrom(
						$this->scopeConfig->getValue(
							'wishlist/email/email_identity',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						)
					)->addTo(
						$email
					)->getTransport();

					$transport->sendMessage();

					$sent++;
				}
			} catch (\Exception $e) {
				$wishlist->setShared($wishlist->getShared() + $sent);
				$wishlist->save();
				throw $e;
			}
			$wishlist->setShared($wishlist->getShared() + $sent);
			$wishlist->save();

			$this->simiObjectManager->get('\Magento\Framework\Translate\Inline\StateInterface')->resume();
			$result = [
				'success' => 1,
				'message' => __( 'Your wish list has been shared.' )
			];

			return $result;
		} catch (\Exception $e) {
			throw new \Simi\Simiconnector\Helper\SimiException($e->getMessage(), 4);
		}

	}

	/*
	 * Remove From Wishlist
	 */

	public function destroy()
	{
		$data = $this->getData();
		$item = $this->simiObjectManager->create('Magento\Wishlist\Model\Item')->load($data['resourceid']);
		if ($item->getId()) {
			$item->delete();
			$this->WISHLIST->save();
			$this->simiObjectManager->get('Magento\Wishlist\Helper\Data')->calculate();
		}
		$this->builderQuery = $this->WISHLIST->getItemCollection();
		return $this->index();
	}

	/*
	 * Add From Wishlist To Cart
	 */

	public function addWishlistItemToCart($itemId)
	{
		foreach ($this->WISHLIST->getItemCollection() as $wishlistItem) {
			if ($wishlistItem->getData('wishlist_item_id') == $itemId) {
				$item = $wishlistItem;
			}
		}
		$product = $item->getProduct();
		$options = $this->simiObjectManager
			->get('\Simi\Simiconnector\Helper\Wishlist')->getOptionsSelectedFromItem($item, $product);
		if ($item && ($this->simiObjectManager
				->get('\Simi\Simiconnector\Helper\Wishlist')->checkIfSelectedAllRequiredOptions($item))) {
			$isSaleAble = $product->isSaleable();
			if ($isSaleAble) {
				$item    = $this->simiObjectManager->create('Magento\Wishlist\Model\Item')->load($itemId);
				$item->setQty('1');
				$cart    = $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
				$options = $this->simiObjectManager->get('Magento\Wishlist\Model\Item\Option')->getCollection()
				                                   ->addItemFilter([$itemId]);
				$item->setOptions($options->getOptionsByItem($itemId));
				if ($item->addToCart($cart, true)) {
					$cart->save()->getQuote()->collectTotals();
				}
				$this->WISHLIST->save();
				$this->simiObjectManager->get('Magento\Wishlist\Helper\Data')->calculate();
			}
		}
	}

	/*
	 * Show An Item
	 */

	public function show()
	{
		$data = $this->getData();
		$useIndex= false;
		if (isset($data['params']) && isset($data['params']['add_to_cart']) && $data['params']['add_to_cart'])
			$useIndex = true;
		if (isset($data['resourceid']) && isset($data['resourceid']) && ($data['resourceid'] == 'add_all_tocart'))
			$useIndex = true;

		if ($useIndex) {
			$this->builderQuery = $this->WISHLIST->getItemCollection();
			return $this->index();
		}

		return parent::show();
	}

	public function update(){
		$data = $this->getData();

		if ( $data['resourceid'] == 'update_all' ) {
			$this->_updateWishList();

		}
		return $this->index();
	}

	public function _updateWishList() {
		$data = $this->getData();
		$parameters = (array) $data['contents'];
		if ( $parameters ) {
			foreach ( $parameters as $parameter ) {
				$wishlist_item_id = $parameter->wishlist_item_id;
				if ( $wishlist_item_id ) {
					$wlItem = $this->simiObjectManager->create( 'Magento\Wishlist\Model\Item' )->load( $wishlist_item_id );
					if ( $wlItem ) {
						if (isset($parameter->qty)){
							$wlItem->setData( 'qty', $parameter->qty )->save();
						}
						if (isset($parameter->description)){
							$wlItem->setData( 'description', $parameter->description )->save();
						}
					}
				}
			}
		}
	}

	/*
	 * Add All wishlist to cart
	 */
	public function addAllWishlistItemsToCart()
	{
		$wishlist   = $this->WISHLIST;
		$this->RETURN_MESSAGE = '';

		$addedItems = array();

		$cart       = $this->simiObjectManager->get('Magento\Checkout\Model\Cart');
		$collection = $wishlist->getItemCollection()
		                       ->setVisibilityFilter();

		foreach ($collection as $item) {
			try {
				$disableAddToCart = $item->getProduct()->getDisableAddToCart();
				$item->unsProduct();

				$item->getProduct()->setDisableAddToCart($disableAddToCart);
				if ($item->addToCart($cart, true)) {
					$addedItems[] = $item->getProduct();
				}

			} catch (\Exception $e) {
				$this->RETURN_MESSAGE .= $e->getMessage();
			}
		}

		if ($addedItems) {
			$wishlist->save();
			$products = array();
			foreach ($addedItems as $product) {
				$products[] = '"' . $product->getName() . '"';
			}
			$this->RETURN_MESSAGE =
				__('Products have been added to shopping cart');
			$cart->save()->getQuote()->collectTotals();
		}
		$this->simiObjectManager->get('Magento\Wishlist\Helper\Data')->calculate();
	}

	/*
	 * Add Message
	 */

	public function getList($info, $all_ids, $total, $page_size, $from)
	{
		$result = parent::getList($info, $all_ids, $total, $page_size, $from);
		if ($this->RETURN_MESSAGE) {
			$result['message'] = [$this->RETURN_MESSAGE];
		}
		if ($this->RETURN_URL) {
			$result['sharing_url'] = [$this->RETURN_URL];
		}
		return $result;
	}

	public function loadProductWithId($id)
	{
		$categoryModel    = $this->simiObjectManager
			->create('Magento\Catalog\Model\Product')->load($id);
		return $categoryModel;
	}
}
