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
class Qixol_Missedpromotions_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    public function saveExcellence($data){
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $this->getQuote()->setExcellenceLike($data['like']);
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
 
        $this->getCheckout()
        ->setStepData('excellence', 'allow', true)
        ->setStepData('excellence', 'complete', true)
        ->setStepData('billing', 'allow', true);
 
        return array();
    }
}
