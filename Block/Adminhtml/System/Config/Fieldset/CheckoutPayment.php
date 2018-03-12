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
 * @author      aldrapay
 * @copyright   2018 Aldrapay
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

namespace Aldrapay\Aldrapay\Block\Adminhtml\System\Config\Fieldset;

/**
 * Renderer for aldrapay Checkout Panel in System Configuration
 *
 * Class CheckoutPayment
 * @package Aldrapay\Aldrapay\Block\Adminhtml\System\Config\Fieldset
 */
class CheckoutPayment extends \Aldrapay\Aldrapay\Block\Adminhtml\System\Config\Fieldset\Base\Payment
{
    /**
     * Retrieves the Module Panel Css Class
     * @return string
     */
    protected function getBlockHeadCssClass()
    {
        return "AldrapayCheckout";
    }
}
