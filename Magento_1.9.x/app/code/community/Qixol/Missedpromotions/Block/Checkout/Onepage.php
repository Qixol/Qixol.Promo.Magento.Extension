<?php

class Qixol_Missedpromotions_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
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
