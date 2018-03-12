<?php
/*
 * Copyright (C) 2018 Aldrapay
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      Aldrapay
 * @copyright   2018 Aldrapay
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

namespace Aldrapay\Aldrapay\Model\Config\Source\Locale\Currency;

/**
 * Specific Currency Source
 * Class AllSpecificCurrencies
 * @package Aldrapay\Aldrapay\Model\Config\Source\Locale\Currency
 */
class AllSpecificCurrencies implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Builds an array for the select control in the Admin Zone
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('All Allowed Currencies'),
            ],
            [
                'value' => 1,
                'label' => __('Specific Currencies'),
            ]
        ];
    }
}
