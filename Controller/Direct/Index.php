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

namespace Aldrapay\Aldrapay\Controller\Direct;

/**
 * Front Controller for Direct Method
 * it redirects to the 3D-Secure Form when applicable
 * Class Index
 * @package Aldrapay\Aldrapay\Controller\Direct
 */
class Index extends \Aldrapay\Aldrapay\Controller\AbstractCheckoutAction
{
    /**
     * Redirect to the 3-D Secure Form or to the Final Checkout Success Page
     *
     * @return void
     */
    public function execute()
    {
        $order = $this->getOrder();

        if (isset($order)) {
            $redirectUrl = $this->getCheckoutSession()->getAldrapayCheckoutRedirectUrl();

            if (isset($redirectUrl)) {
                $this->getCheckoutSession()->setAldrapayCheckoutRedirectUrl(null);
                $this->getResponse()->setRedirect($redirectUrl);
            } else {
                $this->redirectToCheckoutOnePageSuccess();
            }
        }
    }
}
