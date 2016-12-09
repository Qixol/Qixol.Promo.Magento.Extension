<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class Qixol_Missedpromotions_OnepageController extends Mage_Checkout_OnepageController
{
    public function saveMissedpromotionsAction(){
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('missedpromotions', array());
 
            $result = $this->getOnepage()->saveMissedpromotions($data);
 
            if (!isset($result['error'])) {
                $result['goto_section'] = 'billing';
            }
 
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
