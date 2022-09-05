<?php

namespace AHT\ProductFee\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class DefaultConfigProvider
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Magento\Framework\Pricing\Helper\Data
     */
    private $_formartPrice;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Framework\Pricing\Helper\Data $formartPrice
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_formartPrice = $formartPrice;
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        $items = $result['totalsData']['items'];
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $result['quoteItemData'][$index]['fee_value'] = $this->_formartPrice->currency($quoteItem->getProduct()->getFeeValue(), true, false);
        }
        return $result;
    }
}
