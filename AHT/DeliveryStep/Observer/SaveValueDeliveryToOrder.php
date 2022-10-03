<?php

namespace AHT\DeliveryStep\Observer;

class SaveValueDeliveryToOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $order->setData('delivery_date', $quote->getDeliveryDate());

        $order->setData('delivery_comment', $quote->getDeliveryComment());
    }
}
