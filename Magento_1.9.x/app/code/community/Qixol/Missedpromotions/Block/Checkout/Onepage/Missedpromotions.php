<?php

class Qixol_Missedpromotions_Block_Checkout_Onepage_MissedPromotions extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('missedpromotions', array(
            'label'     => Mage::helper('checkout')->__('Missed Promotions'),
            'is_show'   => $this->isShow()
        ));

        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('missedpromotions', 'allow', true);
            $this->getCheckout()->setStepData('billing', 'allow', false);
        }
        parent::_construct();
    }
}
