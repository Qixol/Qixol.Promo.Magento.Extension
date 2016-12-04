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
class Qixol_Missedpromotions_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
//class Qixol_Missedpromotions_Block_Checkout_Onepage extends Qixol_Missedpromotions_Block_Checkout_Onepage_Abstract
{
    public function getSteps()
    {
        $steps = array();
        //$stepCodes = $this->_getStepCodes();
        $stepCodes = array('login', 'missedpromotions', 'billing', 'shipping', 'shipping_method', 'payment', 'review');

        if ($this->isCustomerLoggedIn()) {
            $stepCodes = array_diff($stepCodes, array('login'));
        }

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }
    
    public function getActiveStep()
    {
        return $this->isCustomerLoggedIn() ? 'missedpromotions' :'login';
    }
}
