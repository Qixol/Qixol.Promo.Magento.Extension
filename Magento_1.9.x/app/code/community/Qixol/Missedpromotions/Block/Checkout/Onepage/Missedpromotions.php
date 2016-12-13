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
    
    public function getMissedPromotions()
    {
        // TODO: If showing missed promotions { }
        global $_SESSION;
        if ((!isset($_SESSION['qixol_quoted_items']['cart_session_id'])) ||
                $_SESSION['qixol_quoted_items']['cart_session_id']=='')
        {
            $_SESSION['qixol_quoted_items']['cart_session_id']=md5(time());
        }
        //unset($_SESSION['qixol_quoted_items']['coupons']);
        //$_SESSION['qixol_quoted_items']['coupons'][trim($controller->getRequest()->getParam('coupon_code'))]['code']
        //        = trim($controller->getRequest()->getParam('coupon_code'));
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();
        $basketService = Mage::getModel('qixol/basketservice');
        if ($qixol_quoted_items_new = $basketService->run_ValidateBasket($quote, true))
        {
            $qixol_quoted_items = $qixol_quoted_items_new; //if returned new structure
            $qixol_quoted_items['cart_session_id'] = $_SESSION['qixol_quoted_items']['cart_session_id'];
        }
        
        return $qixol_quoted_items['cart_session_id']['misedpromotions'];
    }
}
