<?php
namespace Simi\Simicustomize\Plugin\Adminhtml;


class SalesBlockItemsColumnName
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }
    /**
     * Plugin allow qty = 1 when try to buy update item to cart
     */
    public function afterGetOrderOptions(
        \Magento\Sales\Block\Adminhtml\Items\Column\Name $repository,
        $result
    ){
        if ($result && is_array($result)) {
            foreach ($result as $optionItem) {
                if (isset($optionItem['label']) && $optionItem['label'] == \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE) {
                    $preOrderProducts = json_decode(base64_decode($optionItem['option_value']), true);
                    if ($preOrderProducts && is_array($preOrderProducts)) {
                        foreach ($preOrderProducts as $preOrderProductIndex => $preOrderProduct) {
                            $preOrderProduct['label'] = $preOrderProduct['sku'];
                            $preOrderProduct['value'] = $preOrderProduct['quantity'];
                            $preOrderProduct['print_value'] = $preOrderProduct['quantity'];
                            $preOrderProduct['option_value'] = $preOrderProduct['quantity'];
                            $preOrderProduct['custom_view'] = false;
                            $preOrderProduct['option_type'] = 'area';
                            $preOrderProduct['option_id'] = $optionItem['option_id'];
                            $preOrderProducts[$preOrderProductIndex] = $preOrderProduct;
                        }
                        return $preOrderProducts;
                    }
                }
            }
        }
        return false;
    }


}
