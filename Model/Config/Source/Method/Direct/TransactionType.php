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

namespace Aldrapay\Aldrapay\Model\Config\Source\Method\Direct;

use \Aldrapay\Aldrapay\Helper\Data as DataHelper;

/**
 * Direct Transaction Types Model Source
 * Class TransactionType
 * @package Aldrapay\Aldrapay\Model\Config\Source\Method\Direct
 */
class TransactionType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Builds the options for the Select control in the Admin Zone
     * @return array
     */
    public function toOptionArray()
    {
        return [

            [
                'value' => DataHelper::AUTHORIZE,
                'label' => __('Authorize'),
            ],
            [
                'value' => DataHelper::PAYMENT,
                'label' => __('Payment'),
            ]
        ];
    }
}
