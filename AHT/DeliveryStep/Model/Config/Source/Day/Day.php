<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\DeliveryStep\Model\Config\Source\Day;

/**
 * @api
 * @since 100.0.2
 */
class Day implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [];
        $arr[] = ['label' => 'Sunday', 'value' => 'su'];
        $arr[] = ['label' => 'Monday', 'value' => 'mo'];
        $arr[] = ['label' => 'Tuesday', 'value' => 'tu'];
        $arr[] = ['label' => 'Wednesday', 'value' => 'we'];
        $arr[] = ['label' => 'Thursday', 'value' => 'th'];
        $arr[] = ['label' => 'Friday', 'value' => 'fr'];
        $arr[] = ['label' => 'Saturday', 'value' => 'sa'];
        return $arr;
    }
}
