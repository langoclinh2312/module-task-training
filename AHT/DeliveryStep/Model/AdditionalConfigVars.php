<?php

namespace AHT\DeliveryStep\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class AdditionalConfigVars
 * @package AHT\DeliveryStep\Model
 */
class AdditionalConfigVars implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    const PATH_DELIVERY_STEP = 'checkout/options/order_comment_enabled';

    /**
     * AdditionalConfigVars constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $enabledDeliveryStep = $this->scopeConfig->getValue(self::PATH_DELIVERY_STEP, $storeScope);
        if ($enabledDeliveryStep) {
            $additionalVariables['enabled_delivery_step'] = true;
        } else {
            $additionalVariables['enabled_delivery_step'] = false;
        }
        return $additionalVariables;
    }
}
