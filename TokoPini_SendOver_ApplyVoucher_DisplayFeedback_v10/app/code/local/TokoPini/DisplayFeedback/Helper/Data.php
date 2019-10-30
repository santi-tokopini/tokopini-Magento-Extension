<?php
/**
 * Data.php
 */
 
class TokoPini_DisplayFeedback_Helper_Data extends Mage_Core_Helper_Abstract {

    const SECTION = "displayfeedback/";
    const GENERAL_GROUP = "general/";
	
    /**
     * @var array
     */
    protected $jsonReviewContent;
    /**
     * @var array
     */
    protected $jsonReviewTabContent;
	
    /**
     * Returns true/false on whether or not the module is enabled
     *
     * @return boolean
     */
	 
	public function isEnabled($store_id = 0)
	{
		//return (bool)Mage::getStoreConfig('displayfeedback/general/enabled');
        return (bool)Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'enabled');
	}
	
    /**
     * Returns an integer which is the log level
     *
     * @return int
     */
	 
    public function isLoggingEnabled($store_id = 0) {
        return (int)Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'logging_enabled');
    }    
	 
	/**
     * Returns the API Token
     *
     * @return string
     */
	 
	public function getApiToken($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'apitoken');
	}
	 
	/**
     * Returns the API URL
     *
     * @return string
     */
	 
	public function getApiUrl($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'apiurl');
	}
	 
	/**
     * Returns the Store ID
     *
     * @return string
     */
	 
	public function getStoreId() {
		return Mage::app()->getStore()->getStoreId();
	}
	/**
     * Returns the Review Content in Json Format
     *
     * @return string
     */
	 
	public function getJsonReviewTabContent()
	{
		if (is_null($this->jsonReviewTabContent)) {
			$client = new Zend_Http_Client($this->getApiUrl($this->getStoreId()).'?token='.$this->getApiToken($this->getStoreId()));
			$client->setMethod(Zend_Http_Client::GET);
			$response = $client->request();
			if($response->isSuccessful()) {
				$reviewContent = Mage::helper('core')->jsonDecode($response->getBody());
				$json = $reviewContent['data']['averagePunctuation']['average'];
				$puntuation = $json / 2;
				if($puntuation !="") {
					$starrating = ($puntuation / 10) * 2 * 100;
				} else {
					$starrating = "0";
				}
				#$json = json_encode($jsonEncodeReviews);
				if($json!="") { $this->jsonReviewTabContent = "REVIEWS " . $json . " / 10"; } else { $this->jsonReviewTabContent = "NO REVIEWS FOUND."; }
			} else {
				$this->jsonReviewTabContent = "NO REVIEWS FOUND.";
			}
			$this->jsonReviewTabContent = $this->jsonReviewTabContent . "&nbsp;<div class='star-ratings-sprite-vertical'><span style='height:".$starrating."%' class='star-ratings-sprite-rating-vertical'></span></div>";
		}
        return $this->jsonReviewTabContent;
	}
	/**
     * Returns the Review Content in Json Format
     *
     * @return string
     */
	 
	public function getJsonReviewContent()
	{
		if (is_null($this->jsonReviewContent)) {
			$client = new Zend_Http_Client($this->getApiUrl($this->getStoreId()).'?token='.$this->getApiToken($this->getStoreId()));
			$client->setMethod(Zend_Http_Client::GET);
			$response = $client->request();
			if($response->isSuccessful()) {
				$reviewContent = Mage::helper('core')->jsonDecode($response->getBody());
				foreach ($reviewContent['data']['feedbacks'] as $_review) {
					$jsonEncodeReviews[]['tokopiniReview'] = $_review;
				}
				$json = json_encode($jsonEncodeReviews);
				if($json!="") { $this->jsonReviewContent = $json; } else { $this->jsonReviewContent = "No Reviews Found."; }
			} else {
				$this->jsonReviewContent = "No Reviews Found.";
			}
		}
        return $this->jsonReviewContent;
	}
}