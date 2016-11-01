<?php
class Qixol_Promo_Model_Creditmemopoints extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract {
 
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo) {

        echo "call Qixol_Promo_Model_Creditmemopoints";
die();
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $creditmemo->getPointsAmount());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $creditmemo->getBasePointsAmount());
        /*
        // ???????????????????
        $creditmemo->setPointsAmount($creditmemo->getPointsAmount());
        $creditmemo->setBasePointsAmount($creditmemo->getBasePointsAmount());
        */
        return $this;
    }
 
}