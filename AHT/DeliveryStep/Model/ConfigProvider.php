<?php

namespace AHT\DeliveryStep\Model;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /** @var \Magento\Framework\View\LayoutInterface  */
    protected $_layout;

    private $_scopeConfig;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_layout = $layout;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'dataConfig' => [
                'date_off' => json_decode($this->_scopeConfig->getValue("delivery_setting/general/delivery_date_off")),
                'day_off' => $this->_scopeConfig->getValue("delivery_setting/general/delivery_day_off"),
                'comment' => $this->_scopeConfig->getValue("delivery_setting/general/delivery_comment"),
                'enable_module' => $this->_scopeConfig->getValue("delivery_setting/general/enable")
            ]
        ];
        return $config;
    }
}
