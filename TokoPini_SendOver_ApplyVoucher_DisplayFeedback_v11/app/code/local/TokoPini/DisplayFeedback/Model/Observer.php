<?php
/**
 * Observer.php
 */
class TokoPini_DisplayFeedback_Model_Observer
{
	public function addBlockAfterContent(Varien_Event_Observer $observer)
	{  
  		if(Mage::helper('DisplayFeedback/data')->isEnabled(Mage::helper('DisplayFeedback/data')->getStoreId())) {
			/*$layout = $observer->getEvent()->getLayout();*/
			$update = Mage::getSingleton('core/layout')->getUpdate();
			$update->addHandle('tokopini_displayfeedback_handle');
			#var_dump($update->getHandles());
		}
	}
}