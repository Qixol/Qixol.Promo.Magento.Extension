<?php
class Qixol_Promo_Block_Bannerboxes extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getBannerboxes() {
        if (!$this->hasData('bannerboxes')) {
            $this->setData('bannerboxes', Mage::registry('bannerboxes'));
        }
        return $this->getData('bannerboxes');
    }



}