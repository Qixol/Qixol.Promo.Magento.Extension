<?php

class Qixol_Missedpromotions_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login'
                    || Mage::getSingleton('customer/session')->isLoggedIn() && $step==='missedpromotions')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }

        $checkout->setStepData('missedpromotions', 'allow', true);
        $checkout->setStepData('missedpromotions', 'is_show', true);

        /*
        * want to laod the correct customer information by assiging to address
        * instead of just loading from sales/quote_address
        */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }
        return $this;
    }

    public function saveMissedpromotions($data){
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $this->getCheckout()
        ->setStepData('missedpromotions', 'allow', true)
        ->setStepData('missedpromotions', 'complete', true)
        ->setStepData('billing', 'allow', true);
 
        return array();
    }
}
