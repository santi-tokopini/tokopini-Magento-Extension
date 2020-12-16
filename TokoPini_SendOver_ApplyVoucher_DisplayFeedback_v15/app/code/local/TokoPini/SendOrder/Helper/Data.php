<?php
/**
 * Data.php
 */
 
class TokoPini_SendOrder_Helper_Data extends Mage_Core_Helper_Abstract {

    const SECTION = "sendorder/";
    const GENERAL_GROUP = "general/";
    const EXCLUSION_GROUP = "exclusions/";
	
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
	 
	public function getApiUrl($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::GENERAL_GROUP . 'apiurl');
	} 
	/**
     * Returns Exclusions Enabled
     *
     * @return string
     */
	 
	public function getExclusionsEnabled($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::EXCLUSION_GROUP . 'enable_exclusions');
	}
	/**
     * Returns Customer Groups to Exclude
     *
     * @return string
     */
	 
	public function getCustomerGroupsToExclude($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::EXCLUSION_GROUP . 'customer_groups_to_exclude');
	}
	/**
     * Returns the Exclusions By Keywords
     *
     * @return string
     */
	 
	public function getExclusionsByKeywords($store_id = 0)
	{
        return Mage::app()->getStore($store_id)->getConfig(self::SECTION . self::EXCLUSION_GROUP . 'exclusions_by_keywords');
	}
}