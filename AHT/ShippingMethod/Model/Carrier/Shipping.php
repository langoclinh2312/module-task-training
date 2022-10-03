<?php

namespace AHT\ShippingMethod\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'aht_shipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_customerSession;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('code')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');
        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);
        return $shippingPrice;
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $method = $this->_rateMethodFactory->create();
        $grandTotal = $request->getPackagePhysicalValue();
        $minOrder = $this->getConfigData('minimum_order');
        $maxorder = $this->getConfigData('maximum_order');

        if (isset($minOrder) && $grandTotal <= $minOrder) {
            $result->append($method);
        } elseif (isset($maxorder) &&  $grandTotal >= $maxorder) {
            $result->append($method);
        } else if (isset($minOrder) && isset($maxorder) &&  $grandTotal >= $maxorder && $grandTotal <= $minOrder) {
            $result->append($method);
        } else {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('code'));
            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));
            $amount = $this->getShippingPrice();
            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);
        }

        return $result;
    }

    /**
     * Validate request for available ship countries.
     *
     * @param \Magento\Framework\DataObject $request
     * @return $this|bool|false|\Magento\Framework\Model\AbstractModel
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkAvailableShipCountries(\Magento\Framework\DataObject $request)
    {
        $allowAllCountry = $this->getConfigData('all_country');
        if ($this->getConfigData('customer_group')) {
            $availableCustomerGroup = explode(',', $this->getConfigData('customer_group'));
        }

        /*
         * check allow shipping method for customer
         */
        if (isset($availableCustomerGroup)) {
            if ($this->_customerSession->isLoggedIn()) {
                $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
                if (isset($availableCustomerGroup) && in_array($customerGroupId, $availableCustomerGroup)) {
                    return $this;
                } else {
                    /** @var Error $error */
                    $error = $this->_rateErrorFactory->create();
                    $error->setCarrier($this->_code);
                    $error->setCarrierTitle($this->getConfigData('name'));
                    $errorMsg = $this->getConfigData('specificerrmsg');
                    $error->setErrorMessage(
                        $errorMsg ? $errorMsg : __(
                            'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                        )
                    );

                    return $error;
                }
            } else {
                return false;
            }
        }

        /*
         * for allow all country countries, the flag will be 0
         */
        if (isset($allowAllCountry) && $allowAllCountry == 0) {
            $availableCountries = '';
            if ($this->getConfigData('country_id')) {
                $availableCountries = $this->getConfigData('country_id');
                $availableState = $this->getConfigData('region_id');
                $availablePostcode = $this->getConfigData('postcode');
            }
            if (isset($availableCountries) && $request->getDestCountryId() == $availableCountries && !isset($availableState) && !isset($availableState)) {
                return $this;
            } elseif (isset($availableState) && $request->getDestRegionId() == $availableState && $request->getDestCountryId() == $availableCountries && !isset($availableState)) {
                return $this;
            } elseif (isset($availablePostcode) && $request->getDestPostcode() == $availablePostcode && $request->getDestRegionId() == $availableState && $request->getDestCountryId() == $availableCountries) {
                return $this;
            } elseif (
                (!isset($availableCountries) || $availableCountries && !$request->getDestCountryId() == $availableCountries)
            ) {
                /** @var Error $error */
                $error = $this->_rateErrorFactory->create();
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('name'));
                $errorMsg = $this->getConfigData('specificerrmsg');
                $error->setErrorMessage(
                    $errorMsg ? $errorMsg : __(
                        'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                    )
                );

                return $error;
            } else {
                /*
                 * The admin set not to show the shipping module if the delivery country
                 * is not within specific countries
                 */
                return false;
            }
        }

        return $this;
    }
}
