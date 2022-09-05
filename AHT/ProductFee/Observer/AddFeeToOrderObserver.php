<?php

namespace AHT\ProductFee\Observer;

class AddFeeToOrderObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /** @var \AHT\ProductFee\Helper\Data  */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * AddFeeToOrderObserver constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession,
     * @param \AHT\ProductFee\Helper\Data $helper,
     * @param \Psr\Log\LoggerInterface $loggerInterface
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \AHT\ProductFee\Helper\Data $helper,
        \Psr\Log\LoggerInterface $loggerInterface
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_helper = $helper;
        $this->logger = $loggerInterface;
    }

    /**
     * Set payment fee to order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        if ($this->_helper->canApply($quote)) {
            $feeAmount = 0;
            $fees = $this->_helper->getFee($quote);
            foreach ($fees as $fee) {
                $feeAmount == 0 ? $feeAmount = $fee['fee'] : $feeAmount = $feeAmount + $fee['fee'];
            }
            if ($feeAmount != 0) {
                //Set fee data to order
                $order = $observer->getOrder();
                $order->setData('fee_amount', $feeAmount);
                $order->setData('base_fee_amount', $feeAmount);
            }
        }

        return $this;
    }
}
