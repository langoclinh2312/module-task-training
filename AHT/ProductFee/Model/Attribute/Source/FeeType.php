<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace AHT\ProductFee\Model\Attribute\Source;

class FeeType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['value' => 'fixed', 'label' => __('fixed')],
            ['value' => 'percent', 'label' => __('percent')]
        ];
        return $this->_options;
    }
}
