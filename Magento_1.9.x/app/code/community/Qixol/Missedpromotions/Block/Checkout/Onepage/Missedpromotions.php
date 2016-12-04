<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MissedPromotions
 *
 * @author ken
 */
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
