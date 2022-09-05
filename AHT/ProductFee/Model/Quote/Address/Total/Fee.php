<?php

declare(strict_types=1);

namespace AHT\ProductFee\Model\Quote\Address\Total;

use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var string
     */
    protected $_code = 'fee';

    /**
     * @var \AHT\PaymentFee\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Collect grand total address amount
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    protected $_quoteValidator = null;

    /**
     * Payment Fee constructor.
     * @param QuoteValidator $quoteValidator
     * @param Session $checkoutSession
     * @param Data $helperData
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \AHT\ProductFee\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_quoteValidator = $quoteValidator;
        $this->_helperData = $helperData;
        $this->_checkoutSession = $checkoutSession;
        $this->logger = $loggerInterface;
    }

    /**
     * Collect totals process.
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if (!count($shippingAssignment->getItems())) {
            return $this;
        }

        $fees = null;
        $newFee = 0;
        if ($this->_helperData->canApply($quote)) {
            $fees = $this->_helperData->getFee($quote);
            foreach ($fees as $fee) {
                $newFee == 0 ? $newFee = $fee['fee'] : $newFee = $newFee + $fee['fee'];
            }
        }

        if ($newFee != 0) {
            $total->setFeeAmount($newFee);
            $total->setBaseFeeAmount($newFee);

            $total->setTotalAmount('fee_amount', $newFee);
            $total->setBaseTotalAmount('base_fee_amount', $newFee);

            // Duplicate fee added when this is added
            $total->setGrandTotal($total->getGrandTotal());
            $total->setBaseGrandTotal($total->getBaseGrandTotal());
        }

        // Make sure that quote is also updated
        $quote->setFeeAmount($newFee);
        $quote->setBaseFeeAmount($newFee);

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(
        Quote $quote,
        Total $total
    ) {
        if ($total->getFeeAmount()) {
            $result = [
                'code' => $this->getCode(),
                'title' => __('Total Fee'),
                'value' => $total->getFeeAmount()
            ];
        }

        return $result;
    }

    /**
     * Get Subtotal label
     *
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Total Fee');
    }
}
