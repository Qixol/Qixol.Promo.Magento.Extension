<?php
class Holbi_Qixol_Block_Adminhtml_Bannerimage_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('bannerimage_form', array('legend' => Mage::helper('qixol')->__('Item information')));
        $version = substr(Mage::getVersion(), 0, 3);
        //$config = (($version == '1.4' || $version == '1.5') ? "'config' => Mage::getSingleton('sticker/wysiwyg_config')->getConfig()" : "'class'=>''");

        $field_last = $fieldset->addField('banner_id', 'text', array(
            'label' => Mage::helper('qixol')->__('Banner id'),
            'name' => 'banner_id',
        ));

        $fieldset->addField('filename', 'image', array(
            'label' => Mage::helper('qixol')->__('Upload and use Image'),
            'required' => true,
            'name' => 'filename',
        ));

        $field_last = $fieldset->addField('promotion_reference', 'text', array(
            'label' => Mage::helper('qixol')->__('Promotion reference'),
            'name' => 'promotion_reference',
        ));

        $field_last = $fieldset->addField('comment', 'text', array(
            'label' => Mage::helper('qixol')->__('Comment'),
            'name' => 'comment',
        ));

        $field_last = $fieldset->addField('url', 'text', array(
            'label' => Mage::helper('qixol')->__('Url'),
            'name' => 'url',
        ));

//        if (Mage::getSingleton('adminhtml/session')->getStickerData()) {
//            $form->setValues(Mage::getSingleton('adminhtml/session')->getStickerData());
//            Mage::getSingleton('adminhtml/session')->setStickerData(null);
//        } elseif (Mage::registry('sticker_data')) {
//            $form->setValues(Mage::registry('sticker_data')->getData());
//        }
        return parent::_prepareForm();
    }

}