<?php
/**
 * Data.php
 */
 
class TokoPini_ApplyVoucher_Helper_Data extends Mage_Core_Helper_Abstract {

    const SECTION = "applyvoucher/";
    const GENERAL_GROUP = "general/";
	
    /**
     * Returns true/false on whether or not the module is enabled
     *
     * @return boolean
     */
	 
	public function isEnabled($store_id = 0)
	{
		//return (bool)Mage::getStoreConfig('sendorder/general/enabled');
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
	 
	public function getApiVerifyCouponUrl($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'apiverifycouponurl');
	}
	/**
     * Returns the API URL
     *
     * @return string
     */
	 
	public function getApiUseCouponUrl($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'apiusecouponurl');
	}
}