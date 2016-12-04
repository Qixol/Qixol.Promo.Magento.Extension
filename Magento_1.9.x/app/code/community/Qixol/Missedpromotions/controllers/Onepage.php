<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Onepage
 *
 * @author ken
 */
class Qixol_Missedpromotions_OnepageController extends Mage_Checkout_OnepageController
{
    public function saveExcellenceAction(){
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('excellence', array());
 
            $result = $this->getOnepage()->saveExcellence($data);
 
            if (!isset($result['error'])) {
                $result['goto_section'] = 'billing';
            }
 
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
