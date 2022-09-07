<?php

namespace AHT\CommentOrder\Observer;

/**
 * Class Emailtemplatevars
 * @package AHT\CommentOrder\Observer
 */
class Emailtemplatevars implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
        if ($transport->getOrder() != null) {
            $transport['order_comments'] = $transport->getOrder()->getOrderComments();
        }
    }
}
