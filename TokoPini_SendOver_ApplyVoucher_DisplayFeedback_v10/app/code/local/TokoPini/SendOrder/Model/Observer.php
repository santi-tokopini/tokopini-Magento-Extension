<?php
/**
 * Observer.php
 */
class TokoPini_SendOrder_Model_Observer
{
  
  public function sendOrderDataToApi(Varien_Event_Observer $observer)
  {
  	if(Mage::helper('SendOrder/data')->isEnabled($observer->getEvent()->getInvoice()->getStoreId())) {
	
		$_invoice = $observer->getEvent()->getInvoice();
		//echo "NEW" . count($observer->getEvent()->getDataObject()->getCommentsCollection()->getItems());
		if ($_invoice->getUpdatedAt() == $_invoice->getCreatedAt()) {
			
			// Logic for new invoices ONLY
			$url = Mage::helper('SendOrder/data')->getApiUrl($_invoice->getStoreId());
			$_order = Mage::getModel('sales/order')->load($_invoice->getOrderId());
			
			//EXCLUSIONS
			if (Mage::helper('SendOrder/data')->getExclusionsEnabled($_invoice->getStoreId())) { 
				$customergroupIdsToCheck = explode(",",Mage::helper('SendOrder/data')->getCustomerGroupsToExclude($_invoice->getStoreId()));
				foreach($customergroupIdsToCheck as $customergroupID) {
					if($customergroupID == Mage::getSingleton('customer/session')->getCustomer()->getGroupId()) {
						 return $this;
					}
				}
				$exclusionKeywordsToCheck = explode(",",Mage::helper('SendOrder/data')->getExclusionsByKeywords($_invoice->getStoreId()));
				foreach($exclusionKeywordsToCheck as $exclusionKeyword) {
					 if (strpos($_order->getCustomerEmail(), $exclusionKeyword) !== false) {
						return $this;
					 }
				}
				
			}
			//EXCLUSIONS
			
			$invoiceData['email'] = $_order->getCustomerEmail();
			$invoiceData['phone'] = $_invoice->getBillingAddress()->getTelephone();
			$items = $_order->getAllItems();
			$itemsOrdered = "";
			foreach ($items as $itemId => $item)
			{
				if($item->getItemId() !="" && $item->getParentItemId() == "") { //this is a check for when a simple with custom option or configurable is used we don't repeat the sku twice
					$itemsOrdered .= $item->getName() . " " . $item->getQtyOrdered() . ";";
				}
			}
			$invoiceData['description'] = $itemsOrdered;
			$invoiceData['reference'] = $_order->getIncrementId();	
			$invoiceData['total_buy'] = $_invoice->getGrandTotal();
			$invoiceData['token'] = Mage::helper('SendOrder/data')->getApiToken($_invoice->getStoreId());
			if(substr($_order->getCouponCode(), 0, 3) == 'TKP') {
				$invoiceData['code'] = $_order->getCouponCode();
			}
			
			$json = json_encode($invoiceData);
			$client = new Zend_Http_Client($url);
			$response = $client->setRawData($json, 'application/json')->request('POST');
			if(!$response->isSuccessful()) {
				if(Mage::helper('SendOrder/data')->isLoggingEnabled($_invoice->getStoreId())) {
					Mage::log("[START] - JSON DATA SEND TO API", null,'tokopini_sendorder.log');
                    Mage::log(print_r($json, 1), null, 'tokopini_sendorder.log');
					Mage::log("[END] - JSON DATA SEND TO API", null,'tokopini_sendorder.log');
					
					Mage::log("[START] - Response Data From API", null,'tokopini_sendorder.log');
                    Mage::log(print_r($client->request()->getBody(), 1), null, 'tokopini_sendorder.log');
					Mage::log("[END] - Response Data From API", null,'tokopini_sendorder.log');
					//var_dump($client->request()->getHeaders());
					//var_dump($client->request()->getBody());
				}
			}
		
		}
	}
    return $this;
  }

}