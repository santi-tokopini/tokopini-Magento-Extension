<?php
/**
 * ListMode.php
 */
class TokoPini_SendOrder_Model_Config_Source_ListMode
{
    public function toOptionArray()
    {
		$groups = array();
		$groups = Mage::helper('customer')->getGroups()->toOptionArray();
		return $groups;
    }
}
