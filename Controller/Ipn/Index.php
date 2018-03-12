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

namespace Aldrapay\Aldrapay\Controller\Ipn;

/**
 * Unified IPN controller for all supported Aldrapay Payment Methods
 * Class Index
 * @package Aldrapay\Aldrapay\Controller\Ipn
 */
class Index extends \Aldrapay\Aldrapay\Controller\AbstractAction
{

    /**
     * Instantiate IPN model and pass IPN request to it
     *
     * @return void
     */
    public function execute()
    {
    	if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
          $ipn = $this->getObjectManager()->create(
              "Aldrapay\\Aldrapay\\Model\\Ipn\\AldrapayIpn"
          );

          $responseBody = $ipn->handleAldrapayNotification();
          $this->getResponse()
              ->setHeader('Content-type', 'text/html')
              ->setBody($responseBody['body'])
              ->setHttpResponseCode($responseBody['code'])
              ->sendResponse();
        } catch (\Exception $e) {
            $this->getLogger()->critical($e);
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
