<?php
/**
 * Observer.php
 */
class TokoPini_ApplyVoucher_Model_Checkout_Observer
{
  private function getStoreId() {
  	return Mage::app()->getStore()->getStoreId();
  }
  
  public function applyVoucher(Varien_Event_Observer $observer)
  {
  
	if(Mage::helper('ApplyVoucher/data')->isEnabled($this->getStoreId())) {
		/* @var Mage_Core_Controller_Front_Action $controller */
		$controller = $observer->getControllerAction();
		$couponcode = $controller->getRequest()->getParam('coupon_code');
		if ($controller->getRequest()->getParam('remove') == 1) {
			$this->removeCouponRule($couponcode);
		} else {
			$response = $this->checkCoupon($couponcode);
			if(!$response->isSuccessful()) {
				if(Mage::helper('ApplyVoucher/data')->isLoggingEnabled($this->getStoreId())) {
					#Mage::log("[START] - JSON DATA SEND TO API", null,'tokopini_applyvoucher.log');
					#Mage::log(print_r($json, 1), null, 'tokopini_applyvoucher.log');
					#Mage::log("[END] - JSON DATA SEND TO API", null,'tokopini_applyvoucher.log');
					Mage::log("[START] - Response Data From API", null,'tokopini_applyvoucher.log');
					Mage::log(print_r($response->getBody(), 1), null, 'tokopini_applyvoucher.log');
					Mage::log("[END] - Response Data From API", null,'tokopini_applyvoucher.log');
				}
			} else {
				//success with response 200
				if($response->getStatus() == "200") {
					$couponData = Mage::helper('core')->jsonDecode($response->getBody());
					if(isset($couponData['credit'])) { 
						$this->generateCouponRule($couponcode,$couponData['credit']);
						/*
						if($this->generateCouponRule($couponcode,$couponData['credit'])) {
							Mage::getSingleton('checkout/cart')
							->getQuote()
							->setCouponCode(strlen($couponcode) ? $couponcode : '')
							->collectTotals()
							->save();
							
							$session = Mage::getSingleton('checkout/session');
							$session->addSuccess("Coupon code ".$couponcode." was applied.");   
						}   
						*/    
					}
				}
			}
		}
	}
    return $this;
  
  }
  
  public function checkVoucherBeforeOrder(Varien_Event_Observer $observer)
  {
	if(Mage::helper('ApplyVoucher/data')->isEnabled($this->getStoreId())) {
		$quote = $observer->getEvent()->getQuote();
		if($quote) {
			Mage::log("checkVoucherBeforeOrder()" . $quote->getCouponCode(), null, 'tokopini_applyvoucher.log');
			if($quote->getCouponCode()!="") {
				$response = $this->checkCoupon($quote->getCouponCode());
				if(!$response->isSuccessful()) {
					$this->removeCouponRule($quote->getCouponCode());
					Mage::log("Removing Coupon Code Rule. Expired Code: ". $quote->getCouponCode(), null,'tokopini_applyvoucher.log');
				}
			}
		}
	}
    return $this;
  }
  public function useVoucherAfterOrder(Varien_Event_Observer $observer)
  {
	if(Mage::helper('ApplyVoucher/data')->isEnabled($this->getStoreId())) {
		//$quote = $observer->getEvent()->getQuote();
		if($observer->getEvent()->getOrders()) {
		  foreach($observer->getEvent()->getOrders() as $order) {
			$response = $this->useCoupon($order->getCouponCode());
			if(!$response->isSuccessful()) {
				if(Mage::helper('ApplyVoucher/data')->isLoggingEnabled($this->getStoreId())) {
					#Mage::log("[START] - JSON DATA SEND TO API", null,'tokopini_applyvoucher.log');
					#Mage::log(print_r($json, 1), null, 'tokopini_applyvoucher.log');
					#Mage::log("[END] - JSON DATA SEND TO API", null,'tokopini_applyvoucher.log');
					Mage::log("[START] - Response Data From API", null,'tokopini_applyvoucher.log');
					Mage::log(print_r($response->getBody(), 1), null, 'tokopini_applyvoucher.log');
					Mage::log("[END] - Response Data From API", null,'tokopini_applyvoucher.log');
				}
			} else {
				//success with response 200
				if($response->getStatus() == "200") {
					$this->removeCouponRule($order->getCouponCode());
				}
			}
		  }
		} else {
			$order = $observer->getEvent()->getOrder();
			$response = $this->useCoupon($order->getCouponCode());
			if(!$response->isSuccessful()) {
				if(Mage::helper('ApplyVoucher/data')->isLoggingEnabled($this->getStoreId())) {
					#Mage::log("[START] - JSON DATA SEND TO API", null,'tokopini_applyvoucher.log');
					#Mage::log(print_r($json, 1), null, 'tokopini_applyvoucher.log');
					#Mage::log("[END] - JSON DATA SEND TO API", null,'tokopini_applyvoucher.log');
					Mage::log("[START] - Response Data From API", null,'tokopini_applyvoucher.log');
					Mage::log(print_r($response->getBody(), 1), null, 'tokopini_applyvoucher.log');
					Mage::log("[END] - Response Data From API", null,'tokopini_applyvoucher.log');
				}
			} else {
				//success with response 200
				if($response->getStatus() == "200") {
					$this->removeCouponRule($order->getCouponCode());
				}
			}
		}
	}
    return $this;
  }
	/**
	 * Returns an Zend_HTTP_Response Object which you can check if successful or not
	 *
	 * @return Zend_HTTP_Response Object
	 */
  private function checkCoupon($couponcode)
  {
	$url = Mage::helper('ApplyVoucher/data')->getApiVerifyCouponUrl($this->getStoreId());
  	$couponCodeData = array();
	$couponCodeData['code'] = $couponcode;
	$couponCodeData['token'] = Mage::helper('ApplyVoucher/data')->getApiToken($this->getStoreId());
			
	$json = json_encode($couponCodeData);
	$client = new Zend_Http_Client($url);
	return $client->setRawData($json, 'application/json')->request('POST');
  }
  /**
	 * Returns an Zend_HTTP_Response Object which you can check if successful or not
	 *
	 * @return Zend_HTTP_Response Object
	 */
  private function useCoupon($couponcode)
  {
	$url = Mage::helper('ApplyVoucher/data')->getApiUseCouponUrl($this->getStoreId());
  	$couponCodeData = array();
	$couponCodeData['code'] = $couponcode;
	$couponCodeData['token'] = Mage::helper('ApplyVoucher/data')->getApiToken($this->getStoreId());
			
	$json = json_encode($couponCodeData);
	$client = new Zend_Http_Client($url);
	return $client->setRawData($json, 'application/json')->request('POST');
  }
  	/**
	 * Removed the Coupon Code Rule
	 *
	 * @return bool
	 */
  private function removeCouponRule($couponcode)
  {
	$oRule = $this->checkCouponRule($couponcode);
	if($oRule->getId()) {
		// Remove the Rule For now. It will be re-added when they re-add the coupon code at a later time.
		$oRule->delete();
		return true;
	}
  	return false;
  }
  	/**
	 * Checked if the Coupon Code Rule Already Exists
	 *
	 * @return Model salesrule/rule
	 */
  private function checkCouponRule($couponCode)
  {
	$oCoupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
	return Mage::getModel('salesrule/rule')->load($oCoupon->getRuleId());
  }
  	/**
	 * Generates a Coupon Rule Based on the Coupon Code From the TokoPini Api
	 *
	 * @return bool
	 */
  
  private function generateCouponRule($couponCode, $fixedDiscountAmount)
  {
  	$oRule = $this->checkCouponRule($couponCode);
	// Check and Make Sure we don't already have this Rule
	if(!$oRule->getId()) {
		// All customer group ids
		$customerGroupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
		// Current Website Id
		$website_id = Mage::app()->getWebsite()->getId();
		// SalesRule Rule model
		$rule = Mage::getModel('salesrule/rule');
		// Rule data
		$rule->setName('TokoPini Generated Apply Voucher Code: '.$couponCode)                                             
			->setDescription('Generated Voucher Code From the TokoPini_ApplyVoucher Extension')
			->setFromDate('')
			->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
			->setCouponCode($couponCode)
			->setUsesPerCustomer(1)
			->setUsesPerCoupon(1)
			->setCustomerGroupIds($customerGroupIds)
			->setIsActive(1)
			->setConditionsSerialized('')
			->setActionsSerialized('')
			->setStopRulesProcessing(0)
			->setIsAdvanced(1)
			->setProductIds('')
			->setSortOrder(0)
			->setSimpleAction(Mage_SalesRule_Model_Rule::CART_FIXED_ACTION)
			->setDiscountAmount($fixedDiscountAmount)
			->setDiscountQty(1)
			->setDiscountStep(0)
			->setSimpleFreeShipping('0')
			->setApplyToShipping('0')
			->setIsRss(0)
			->setWebsiteIds(array($website_id))
			->setStoreLabels(array('TokoPini Generated Apply Voucher Code: '.$couponCode));
		// Save Rule	
		$rule->save();
		return true;
	}
	return false;
  }
}