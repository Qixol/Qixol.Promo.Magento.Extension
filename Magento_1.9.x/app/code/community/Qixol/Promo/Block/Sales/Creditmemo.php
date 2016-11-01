<?php
//????????????????
class Qixol_Promo_Block_Adminhtml_Sales_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    protected function _initTotals() {
        parent::_initTotals();
		    $amt = $this->getSource()->getPointsAmount();
        $baseAmt = $this->getSource()->getBasePointsAmount();
        if ($amt != 0) {
 
            $this->addTotal(new Varien_Object(array(
                        'code' => 'points',
                        'value' => $amt,
                        'base_value' => $baseAmt,
                        'label' => 'Rewards points discount',
                    )), 'points');
        }
        return $this;
    }					
	
}
