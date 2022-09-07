<?php

namespace AHT\CommentOrder\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class AdditionalConfigVars
 * @package AHT\CommentOrder\Model
 */
class AdditionalConfigVars implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    const PATH_ORDER_COMMENTS = 'checkout/options/order_comment_enabled';

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
        $enabledComments = $this->scopeConfig->getValue(self::PATH_ORDER_COMMENTS, $storeScope);
        if ($enabledComments) {
            $additionalVariables['enabled_comments'] = true;
        } else {
            $additionalVariables['enabled_comments'] = false;
        }
        return $additionalVariables;
    }
}
