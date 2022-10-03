<?php

namespace AHT\UpdatePrice\Observer\Cart;


use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Updatecart
 * @package VendorName\Changeprice\Observer
 */
class Updatecart implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $_productRepository;

    /**
     * Updatecart constructor.
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_productRepository = $productRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $item = $observer->getEvent()->getItem();
        $productId = $item->getProduct()->getId();
        $product = $this->_productRepository->getById($productId);
        $quote = $item->getQuote();
        $enable = $this->_scopeConfig->getValue(
            'checkout/cart/custom_price_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($enable != 0) {
            $price = $product->getCustomPrice() > 0 ? ($product->getPrice() + $product->getCustomPrice()) : $product->getPrice();
        } else {
            $price = $product->getPrice();
        }
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }
}
