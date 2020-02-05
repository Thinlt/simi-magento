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
     * Plugin change option display for pre-order/try-to-buy item
     */
    public function afterGetOrderOptions(
        \Magento\Sales\Block\Adminhtml\Items\Column\Name $repository,
        $result
    ){
        if ($result && is_array($result)) {
            foreach ($result as $optionItem) {
                if (isset($optionItem['label']) &&
                    (
                        $optionItem['label'] == \Simi\Simicustomize\Model\Api\Quoteitems::PRE_ORDER_OPTION_TITLE ||
                        $optionItem['label'] == \Simi\Simicustomize\Model\Api\Quoteitems::TRY_TO_BUY_OPTION_TITLE
                    )
                ) {
                    $childProducts = json_decode(base64_decode($optionItem['option_value']), true);
                    if ($childProducts && is_array($childProducts)) {
                        foreach ($childProducts as $childProductIndex => $childProduct) {
                            $childProduct['label'] = $childProduct['sku'] . ' - ' . $childProduct['name'];
                            $childProduct['value'] = $childProduct['quantity'];
                            $childProduct['print_value'] = $childProduct['quantity'];
                            $childProduct['option_value'] = $childProduct['quantity'];
                            $childProduct['custom_view'] = false;
                            $childProduct['option_type'] = 'area';
                            $childProduct['option_id'] = $optionItem['option_id'];
                            $preOrderProducts[$childProductIndex] = $childProduct;
                        }
                        return $preOrderProducts;
                    }
                }
            }
        }
        return false;
    }


}
