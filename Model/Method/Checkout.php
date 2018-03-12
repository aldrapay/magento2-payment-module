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

namespace Aldrapay\Aldrapay\Model\Method;

/**
 * Checkout Payment Method Model Class
 * Class Checkout
 * @package Aldrapay\Aldrapay\Model\Method
 */
class Checkout extends \Magento\Payment\Model\Method\AbstractMethod
{
    use \Aldrapay\Aldrapay\Model\Traits\OnlinePaymentMethod;

    const CODE = 'aldrapay_checkout';
    /**
     * Checkout Method Code
     */
    protected $_code = self::CODE;

    protected $_canOrder                    = true;
    protected $_isGateway                   = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = true;
    protected $_canRefund                   = true;
    protected $_canCancelInvoice            = true;
    protected $_canVoid                     = true;
    protected $_canRefundInvoicePartial     = true;
    protected $_canAuthorize                = true;
    protected $_isInitializeNeeded          = false;
    

    /**
     * Get Instance of the Magento Code Logger
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * Checkout constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\App\Action\Context $actionContext
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Aldrapay\Aldrapay\Helper\Data $moduleHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\App\Action\Context $actionContext,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger  $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Aldrapay\Aldrapay\Helper\Data $moduleHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_actionContext = $actionContext;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_moduleHelper = $moduleHelper;
        $this->_configHelper =
            $this->getModuleHelper()->getMethodConfig(
                $this->getCode()
            );
    }

//     /**
//      * Get Default Payment Action On Payment Complete Action
//      * @return string
//      */
//     public function getConfigPaymentAction()
//     {
//         return \Magento\Payment\Model\Method\AbstractMethod::ACTION_ORDER;
//     }
    
    
    /**
     * Retrieves the Checkout Payment Action according to the
     * Module Transaction Type setting
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
    	$helper = $this->getModuleHelper();
    
    	$transactionTypeActions = [
    			$helper::AUTHORIZE    =>
    			\Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE,
    			$helper::PAYMENT      =>
    			\Magento\Payment\Model\Method\AbstractMethod::ACTION_ORDER
    	];
    
    	$transactionSelectedTypes = $this->getCheckoutTransactionTypes();
    	
    	
    	foreach ($this->getCheckoutTransactionTypes() as $selectType){
    		
	    	if (array_key_exists($selectType, $transactionTypeActions)) 
		    	return $transactionTypeActions[$selectType];
    	}
    
    	$this->getModuleHelper()->throwWebApiException(
    				sprintf(
    						'Transaction Type (%s) not supported yet',
    						$transactionType
    						)
    				);
    }
    
    
    /**
     * Retrieves the Module Transaction Type Setting
     *
     * @return string
     */
    public function getConfigTransactionType()
    {
    	return $this->getConfigData('transaction_type');
    }
    
    

    /**
     * Get Available Checkout Transaction Types
     * @return array
     */
    public function getCheckoutTransactionTypes()
    {
        $selected_types = $this->getConfigHelper()->getTransactionTypes();

        return $selected_types;
    }

    /**
     * Create a Web-Payment Form Instance
     * @param array $data
     * @return \stdClass
     * @throws \Magento\Framework\Webapi\Exception
     */
    protected function checkout($data)
    {
      
      //error_log("##DBG [".__METHOD__."] init ");
      	
      if ($this->getConfigPaymentAction() == \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE){
	      
      	$transaction = new \Aldrapay\AuthorizationHostedPageOperation();
      }
	  else{
	  	
      	$transaction = new \Aldrapay\PaymentHostedPageOperation();
	  }
      
      $transaction->money->setAmount($data['order']['amount']);
      $transaction->money->setCurrency($data['order']['currency']);
      $transaction->setDescription(substr($data['order']['description'],0,64));
      $transaction->setTrackingId($data['tracking_id']);
      //$transaction->setLanguage($data['order']['language']);
      $transaction->customer->setFirstName(substr(strval($data['order']['billing']->getFirstname()),0,32));
      $transaction->customer->setLastName(substr(strval($data['order']['billing']->getLastname()),0,50));
      $transaction->customer->setAddress(substr(strval($data['order']['billing']->getStreetLine(1)),0,64));
      $transaction->customer->setCity(substr(strval($data['order']['billing']->getCity()),0,32));
      $transaction->customer->setCountry(strval($data['order']['billing']->getCountryId()));
      $transaction->customer->setZip(substr(strval($data['order']['billing']->getPostcode()),0,16));
      $transaction->customer->setIP($_SERVER['REMOTE_ADDR']);

      if (in_array(strval($data['order']['billing']->getCountryId()), array('US', 'CA')))
        $transaction->customer->setState(strval($data['order']['billing']->getRegionCode()));
      else
        $transaction->customer->setState(substr(strval($data['order']['billing']->getCity()),0,64));

      if (!empty(strval($data['order']['customer']['email']))) {
        $transaction->customer->setEmail(substr(strval($data['order']['customer']['email']),0,120));
      }

      $transaction->customer->setPhone(substr(strval($data['order']['billing']->getTelephone()),0,16));

      $transaction->setNotificationUrl($data['urls']['notify']);
      $transaction->setReturnUrl($data['urls']['return']);
      
      $payment_methods = $this->getCheckoutTransactionTypes();
      $helper = $this->getModuleHelper();

      //return new \Aldrapay\Response(json_encode(array()));
      
      $response = $transaction->submit();

      return $response;
    }

    
    
    /**
     * Authorize payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
    	return $this->order($payment, $amount);
    }
    
    
    
    
    /**
     * Order Payment
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $order = $payment->getOrder();

        $orderId = ltrim(
            $order->getIncrementId(),
            '0'
        );
        
        $transactionTypes = $this->getConfigHelper()->getTransactionTypes();
        
    	if ($transactionTypes != null && isset($transactionTypes[0]) 
      		&& $transactionTypes[0] == \Aldrapay\Aldrapay\Helper\Data::AUTHORIZE){
      			
      			$order->setPaymentAuthorizationAmount($amount);
      			$order->setPaymentAuthExpiration(time()+60*60*48);
     	 }
	  	

        $data = [
            'tracking_id' =>
                $this->getModuleHelper()->genTransactionId(
                  $orderId
                ),
            'transaction_types' => $transactionTypes,

        	'order' => [
                'increment_id' => $orderId,
                'currency' => $order->getBaseCurrencyCode(),
                'language' => $this->getModuleHelper()->getLocale(),
                'amount' => $amount,
                'usage' => $this->getModuleHelper()->buildOrderUsage(),
                'description' => __('Order # %1 payment', $orderId),
                'customer' => [
                    'email' => $this->getCheckoutSession()->getQuote()->getCustomerEmail(),
                ],
                'billing' =>
                    $order->getBillingAddress(),
                'shipping' =>
                    $order->getShippingAddress()
            ],
            'urls' => [
                'notify' =>
                    $this->getModuleHelper()->getNotificationUrl(
                        $this->getCode()
                    ),
                'return' =>
                    $this->getModuleHelper()->getReturnUrl(
                        $this->getCode(),
                        'return'
                    ),
            ]
        ];

        $this->getConfigHelper()->initGatewayClient();

        try {
        	
            $responseObject = $this->checkout($data); /* @var $responseObject \Aldrapay\Response */
            
            $isAldrapaySuccessful = $responseObject->isValid() && !empty($responseObject->getRedirectUrl());
            
            if (!$isAldrapaySuccessful) {
                $errorMessage = $responseObject->getMessage();

                $this->getCheckoutSession()->setAldrapayLastCheckoutError(
                    $errorMessage
                );

                $this->getModuleHelper()->throwWebApiException($errorMessage, 500);
            }
            
            $customerRedirect = new \Aldrapay\CustomerRedirectHostedPage($responseObject->getRedirectUrl(), $responseObject->getUid());
            $customerRedirect->money =  new \Aldrapay\Money($data['order']['amount'], $data['order']['currency']);
            $customerRedirect->setTrackingId($data['tracking_id']);
            
            $customerRedirect->setReturnUrl($data['urls']['return']);
            $customerRedirect->setNotificationUrl($data['urls']['notify']);

            $payment->setTransactionId($responseObject->getUid());
            $payment->setIsTransactionClosed(false);
            $payment->setIsTransactionPending(true);
            $payment->setTransactionAdditionalInfo(
                \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                $responseObject->getResponse()
            );
            

	        $this->getModuleHelper()->setPaymentTransactionAdditionalInfo(
	               $payment,
	               $responseObject
	        );

            $this->getCheckoutSession()->setAldrapayCheckoutRedirectUrl(
                $customerRedirect->getFullRedirectUrl()
            );
            
            return $this;
            
        } catch (\Exception $e) {
            $this->getLogger()->error(
                $e->getMessage()
            );

            $this->getCheckoutSession()->setAldrapayLastCheckoutError(
                $e->getMessage()
            );

            $this->getModuleHelper()->maskException($e);
        }
    }

    /**
     * Payment Capturing
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        $this->getLogger()->debug('Capture transaction for order #' . $order->getIncrementId());
        //error_log('------Capture transaction for order #' . $order->getIncrementId());

        $authTransaction = $this->getModuleHelper()->lookUpAuthorizationTransaction(
            $payment
        );

        if (!isset($authTransaction)) {
            $errorMessage = __('Capture transaction for order # %1 cannot be finished (No Authorize Transaction exists)',
                $order->getIncrementId()
            );

            $this->getLogger()->error(
                $errorMessage
            );
	        //error_log('------'.$errorMessage);

            $this->getModuleHelper()->throwWebApiException(
                $errorMessage
            );
        }

        try {
            $this->doCapture($payment, $amount, $authTransaction);
        } catch (\Exception $e) {
            $this->getLogger()->error(
                $e->getMessage()
            );
            $this->getModuleHelper()->maskException($e);
        }

        return $this;
    }

    /**
     * Payment refund
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $payment->getOrder();

        $this->getLogger()->debug('Refund transaction for order #' . $order->getIncrementId());

        $captureTransaction = $this->getModuleHelper()->lookUpCaptureTransaction(
            $payment
        );

        if (!isset($captureTransaction)) {
            $errorMessage = __('Refund transaction for order # %1 cannot be finished (No Capture Transaction exists)',
                $order->getIncrementId()
            );

            $this->getLogger()->error(
                $errorMessage
            );

            $this->getMessageManager()->addError($errorMessage);

            $this->getModuleHelper()->throwWebApiException(
                $errorMessage
            );
        }

        try {
            $this->doRefund($payment, $amount, $captureTransaction);
        } catch (\Exception $e) {
            $this->getLogger()->error(
                $e->getMessage()
            );

            $this->getMessageManager()->addError(
                $e->getMessage()
            );

            $this->getModuleHelper()->maskException($e);
        }

        return $this;
    }

    /**
     * Payment Cancel
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     */
    public function cancel(\Magento\Payment\Model\InfoInterface $payment)
    {
        $this->void($payment);

        return $this;
    }

    /**
     * Void Payment
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        /** @var \Magento\Sales\Model\Order $order */

        $order = $payment->getOrder();

        $this->getLogger()->debug('Void transaction for order #' . $order->getIncrementId());

        $referenceTransaction = $this->getModuleHelper()->lookUpVoidReferenceTransaction(
            $payment
        );

        if ($referenceTransaction->getTxnType() == \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH) {
            $authTransaction = $referenceTransaction;
        } else {
            $authTransaction = $this->getModuleHelper()->lookUpAuthorizationTransaction(
                $payment
            );
        }

        if (!isset($authTransaction) || !isset($referenceTransaction)) {
            $errorMessage = __('Void transaction for order # %1 cannot be finished (No Authorize / Capture Transaction exists)',
                            $order->getIncrementId()
            );

            $this->getLogger()->error($errorMessage);
            $this->getModuleHelper()->throwWebApiException($errorMessage);
        }

        try {
            $this->doVoid($payment, $authTransaction, $referenceTransaction);
        } catch (\Exception $e) {
            $this->getLogger()->error(
                $e->getMessage()
            );
            $this->getModuleHelper()->maskException($e);
        }

        return $this;
    }

    /**
     * Determines method's availability based on config data and quote amount
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return parent::isAvailable($quote) &&
            $this->getConfigHelper()->isMethodAvailable();
    }

    /**
     * Checks base currency against the allowed currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        return $this->getModuleHelper()->isCurrencyAllowed(
            $this->getCode(),
            $currencyCode
        );
    }
}
