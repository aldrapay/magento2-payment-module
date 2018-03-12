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

namespace Aldrapay\Aldrapay\Model\Ipn;

/**
 * Checkout Method IPN Handler Class
 * Class CheckoutIpn
 * @package Aldrapay\Aldrapay\Model\Ipn
 */
class AldrapayIpn extends \Aldrapay\Aldrapay\Model\Ipn\AbstractIpn
{
	
    /**
     * @return string
     */
    protected function getPaymentMethodCode()
    {
        return \Aldrapay\Aldrapay\Model\Method\Checkout::CODE;
    }
    
    public function getConfigHelper()
    {
    	return $this->_configHelper;
    }

    /**
     * Update Pending Transactions and Order Status
     * @param \stdClass $responseObject
     * @throws \Exception
     */
    protected function processNotification($responseObject)
    {
    	//error_log("##DBG [".__METHOD__."] init ");
    	
        $payment = $this->getPayment(); /* @var $payment \Magento\Sales\Model\Order\Payment */
        $helper = $this->getModuleHelper();
        
        $this->getModuleHelper()->updateTransactionAdditionalInfo(
            $responseObject->getUid(),
            $responseObject,
            true
        );

        
        $parentTransactionId = isset($responseObject->getResponse()->initialTransactionID) ?
                	$responseObject->getResponse()->initialTransactionID : null;

        if ($parentTransactionId == null)
        	$parentTransactionId = isset($responseObject->getResponse()->refundTransactionID) ?
                		$responseObject->getResponse()->refundTransactionID : null;
                		
        
        if (isset($responseObject->getResponse()->transaction->transactionID)) {
            $payment_transaction = $responseObject;

//             if ($this->_order->canFetchPaymentReviewUpdate())
//            		$payment->update(true);
            
            $payment
                ->setLastTransId(
                    $payment_transaction->getUid()
                )
                ->setTransactionId(
                    $payment_transaction->getUid()
                )
                ->setParentTransactionId(
                	$parentTransactionId
                )
                ->setIsTransactionPending(
                    $this->getShouldSetCurrentTranPending(
                        $payment_transaction
                    )
                )
                ->setShouldCloseParentTransaction(
                    false
                );
            $payment
                ->setIsTransactionClosed( 
                	false 
                )
                ->setPreparedMessage(
                    $this->createIpnComment(
                        $payment_transaction->getMessage()
                    )
                )
                ->resetTransactionAdditionalInfo(
                		
                		);
	    	
            $this->getModuleHelper()->setPaymentTransactionAdditionalInfo(
                $payment,
                $payment_transaction
            );

            switch ($payment_transaction->getStatus()) {
                case $helper::PROC_STATUS_AUTHORIZED:
                case $helper::AUTHORIZE:
                    $payment->registerAuthorizationNotification($payment->getAmountAuthorized());
                    break;
                case $helper::PROC_STATUS_APPROVED:
                case $helper::SUCCESSFUL:
                    $payment->registerCaptureNotification($payment->getAmountOrdered());
                    break;
                default:
                    break;
            }

            if (!$this->getOrder()->getEmailSent()) {
               $this->_orderSender->send($this->getOrder());
            }

            $payment->save();
        }

        $this->getModuleHelper()->setOrderState(
            $this->getOrder(),
            $responseObject->getStatus()
        );
    }
}
