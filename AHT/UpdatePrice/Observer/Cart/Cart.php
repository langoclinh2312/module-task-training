<?php

namespace AHT\UpdatePrice\Observer\Cart;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Cart
 * @package VendorName\Changeprice\Observer
 */
class Cart implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $item = $observer->getEvent()->getData('quote_item');
        $product = $observer->getEvent()->getData('product');
        $enable = $this->_scopeConfig->getValue(
            'checkout/cart/custom_price_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($enable != 0) {
            $price = $product->getCustomPrice() > 0 ? ($product->getPrice() + $product->getCustomPrice()) : $product->getPrice();
        } else {
            $price = $product->getPrice();
        }

        $cartItems = [];
        if ($item->getQuote()->getItems()) {
            foreach ($item->getQuote()->getItems() as $key => $value) {
                $cartItems[$value->getSku()] = $value->getQty();
            }
        }

        $item->setPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->setCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }
}
