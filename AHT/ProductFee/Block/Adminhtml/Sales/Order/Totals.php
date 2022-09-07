<?php

namespace AHT\ProductFee\Block\Adminhtml\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if (!$this->getSource()->getFeeAmount()) {
            return $this;
        }
        $total = new DataObject(
            [
                'code' => 'fee',
                'value' => $this->getSource()->getFeeAmount(),
                'label' => __('Total Fee'),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
