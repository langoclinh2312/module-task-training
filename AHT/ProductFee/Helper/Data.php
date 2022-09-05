<?php

namespace AHT\ProductFee\Helper;

use InvalidArgumentException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Recipient fixed amount of custom payment config path
     */
    const CONFIG_PAYMENT_fee = 'productfee/config/';
    const HANDLING_TYPE_FIXED = 'fixed';

    /**
     * Total Code
     */
    const TOTAL_CODE = 'fee_amount';

    /**
     * @var array
     */
    public $_productFee = null;

    /**
     * @var array
     */
    public $_fee = null;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $_pricingHelper;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    private $_priceCurrency;

    /**
     * @var Magento\Framework\Serialize\SerializerInterface
     */
    private $_serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $_collectionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Unserialize\Unserialize $unserialize,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->unserialize = $unserialize;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
        $this->_pricingHelper = $pricingHelper;
        $this->_priceCurrency = $priceCurrency;
        interface_exists(SerializerInterface::class) ? $this->_serializer = $serializer : $this->_serializer = $unserialize;
        $this->logger = $context->getLogger();
        $this->_getProductFee();
    }

    /**
     * Retrieve Fees from Products
     * @return array
     */
    protected function _getProductFee()
    {
        if (is_null($this->_productFee)) {
            try {
                $initialFees = $this->_collectionFactory->create();
                $initialFees->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id', array('eq' => 'simple'))
                    ->load()->getItems();
                $fees        = isset($initialFees) ? $initialFees : $this->_serializer->unserialize($initialFees);
            } catch (InvalidArgumentException $e) {
                $fees = [];
            }

            if (isset($fees)) {
                foreach ($fees as $fee) {
                    $this->_productFee[$fee->getId()] = [
                        'fee_type' => $fee->getFeeType(),
                        'fee' => $fee->getFeeValue()
                    ];
                }
            }
        }
        return $this->_productFee;
    }

    /**
     * Retrieve Store Config
     * @param string $field
     * @return mixed|null
     */
    public function getConfig($field = '')
    {
        if ($field) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            return $this->scopeConfig->getValue(self::CONFIG_PAYMENT_fee . $field, $storeScope);
        }
        return null;
    }

    /**
     * Check if Extension is Enabled config
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig('enabled');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function canApply(\Magento\Quote\Model\Quote $quote)
    {

        /**@TODO check module or config**/
        if ($this->isEnabled()) {
            foreach ($quote->getAllItems() as $key => $value) {
                if ($product = $value->getProduct()) {
                    if (isset($this->_productFee[$product->getId()])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array|null
     */
    public function getFee(\Magento\Quote\Model\Quote $quote)
    {
        foreach ($quote->getAllItems() as $key => $value) {
            if ($product = $value->getProduct()) {
                if ($product->getTypeId() == 'simple') {
                    $this->_fee[$product->getId()] = [
                        'fee_type' => $this->_productFee[$product->getId()]['fee_type'],
                        'fee' => (floatval($this->_productFee[$product->getId()]['fee']) > 0) ? floatval($this->_productFee[$product->getId()]['fee']) : 0
                    ];
                }
            }
        }

        if (isset($this->_fee)) {
            foreach ($this->_fee as $fee) {
                if ($fee['fee_type'] != $this::HANDLING_TYPE_FIXED) {
                    $subTotal = $quote->getSubtotal();
                    $fee['fee'] = $subTotal * ($fee['fee'] / 100);
                }

                $fee['fee'] = $this->_priceCurrency->round($fee['fee']);
            }
        }
        return $this->_fee;
    }
}
