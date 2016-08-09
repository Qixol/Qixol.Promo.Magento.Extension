<?php
class Holbi_Qixol_Block_Adminhtml_Shippingmap_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $hlp=Mage::helper('qixol');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('shippingmap_form', array('legend' => $hlp->__('Item information')));
        //$version = substr(Mage::getVersion(), 0, 3);
        //$config = (($version == '1.4' || $version == '1.5') ? "'config' => Mage::getSingleton('quxol/wysiwyg_config')->getConfig()" : "'class'=>''");



        $shipping_name_array_list=Array();
        

          $list_map_names=Mage::getModel('qixol/shippingmap')->getCollection();
          $list_map_names_exists=array();

          foreach ($list_map_names as $list_map){
              $list_map_names_exists[$list_map->getShippingName()]=$list_map->getShippingName();
          }
          //returns only active list
          $only_active=Mage::getStoreConfig('qixol/shippings/onlyactive');
          if ($only_active>0)
          {
             $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
          }
          else
          {   
            $carriers = Mage::getSingleton('shipping/config')->getAllCarriers();
          }

          $shippingMethodDropDownValues = array();
          
          foreach($carriers as $_ccode => $_carrier)
          {
              $carrierMethods = array();
              
           try{ //some methods not allowed getAllowedMethods
              if($_methods = $_carrier->getAllowedMethods())
              {
                  foreach($_methods as $_mcode => $_method)
                  {
                      $_code = $_ccode . '_' . $_mcode;
                      $carrierMethods[] = array(
                          'value' => $_code,
                          'label' => $hlp->__(trim($_method) == '' ? $_code : $_method)
                        );
                      if (isset($list_map_names_exists[$_code]))
                      {
                          unset($_code);
                      }
                  }

                if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                {
                        $_title = $_ccode;
                }

                $shippingMethodDropDownValues[] = array('label' => $_title, 'value' => $carrierMethods);
              }
            }
            catch(Exception $e) {
            continue;
            }
              
          }

//$shipping_name_array_list = array(
//    array(
//        'label' => 'Flatrate',
//        'value' =>  array(array('label' => 'Fixed', 'value' => 'flatrate_flatrate'))
//    ),
//    array(
//        'label' => 'Free Shipping',
//        'value' => array(array('label' => 'Free', 'value' => 'freeshipping_freeshipping'))
//    ),
//    array(
//        'label' => 'Federal Express',
//        'value' => array(
//                        array('label' => '2 Day', 'value' => 'fedex_FEDEX_2_DAY'),
//                        array('label' => 'Ground', 'value' => 'fedex_FEDEX_GROUND'),
//                        array('label' => 'First Overnight', 'value' => 'fedex_FIRST_OVERNIGHT')
//    ))
//);

        $fieldset->addField('shipping_name', 'select', array(
            'label' => Mage::helper('qixol')->__('Shipping Method'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'shipping_name',
            'values' => $shippingMethodDropDownValues
        ));

        $fieldset->addField('shipping_name_map', 'text', array(
            'label' => Mage::helper('qixol')->__('Integration Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'shipping_name_map',
            'after_element_html' => Mage::helper('qixol')->__('Code to be synchronised to Promo'),
        ));


        if (Mage::getSingleton('adminhtml/session')->getShippingmapData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getShippingmapData());
            Mage::getSingleton('adminhtml/session')->setShippingmapData(null);
        } elseif (Mage::registry('shippingmap_data')) {
            $form->setValues(Mage::registry('shippingmap_data')->getData());
        }
        return parent::_prepareForm();
    }

}