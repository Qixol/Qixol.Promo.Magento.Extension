<?php

require_once 'Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Action.php';
class Qixol_Promo_Block_Adminhtml_Widget_Grid_Column_Renderer_Banner extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function __construct() {

    }

    public function render(Varien_Object $row) {
        return $this->_getValue($row);
    }

    protected function _getValue(Varien_Object $row) {
        $dored = false;
        $out = '';
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $val = $row->getData($this->getColumn()->getIndex());
        if (trim($val)!=''){
        $url = Mage::helper('qixol')->getImageUrl($val);
        $size = Mage::helper('qixol')->getImageThumbSize($val);
        $file_extis = Mage::helper('qixol')->getFileExists($val);
        $popLink = "popWin('$url','image','width=800,height=600,resizable=yes,scrollbars=yes')";
        if (is_array($size) && $file_extis)
            $out = '<a href="javascript:;" onclick="'.$popLink.'"><img src="'.$url.'" width="'.$size['width'].'" height="'.$size['height'].'" style="border: 2px solid #CCCCCC;"/></a>';
        }
        return $out;
    }
}